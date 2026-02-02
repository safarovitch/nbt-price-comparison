<?php

namespace App\Services\Transformers;

use Illuminate\Support\Str;

/**
 * Transforms Shukr Moliya's unique API response format to the standard product format.
 * Returns translatable fields as arrays with language keys (ru, en, tj).
 * Supports extended fields: rate ranges, term ranges, rate_tiers.
 */
class ShukrMoliyaTransformer
{
    /**
     * Transform credits endpoint response.
     *
     * @param array<int, array<string, mixed>> $data
     * @return array{products: array<int, array<string, mixed>>}
     */
    public function transformCredits(array $data): array
    {
        $products = [];

        foreach ($data as $category) {
            // Skip non-array items (API might return metadata)
            if (!is_array($category)) {
                continue;
            }

            $categoryId = $category['creditCategoryId'] ?? null;
            $categoryName = [
                'ru' => $category['creditCategoryNameRU'] ?? 'Unknown',
                'en' => $category['creditCategoryNameEN'] ?? 'Unknown',
                'tj' => $category['creditCategoryNameTJ'] ?? 'Unknown',
            ];

            // Process each slider credit (main product info)
            $sliderCredits = $category['sliderCredits'] ?? [];
            $creditTerms = $category['creditTerms'] ?? [];

            // Match terms by creditCategoryId
            $terms = collect($creditTerms)->firstWhere('creditCategoryId', $categoryId) ?? [];

            foreach ($sliderCredits as $credit) {
                $products[] = $this->buildCreditProduct($credit, $terms, $categoryName);
            }
        }

        return ['products' => $products];
    }

    /**
     * Transform money transfers endpoint response.
     *
     * @param array<int, array<string, mixed>> $data
     * @return array{products: array<int, array<string, mixed>>}
     */
    public function transformTransfers(array $data): array
    {
        $products = [];

        foreach ($data as $item) {
            // Skip non-array items (API might return metadata)
            if (!is_array($item)) {
                continue;
            }

            // Extract fee from commission fields
            $feeValue = $this->extractNumericValue($item['commission1RU'] ?? $item['commission1EN'] ?? null);

            $products[] = [
                'name' => [
                    'ru' => $item['titleRU'] ?? 'Money Transfer',
                    'en' => $item['titleEN'] ?? 'Money Transfer',
                    'tj' => $item['titleTJ'] ?? 'Интиқоли пул',
                ],
                'type' => 'transfer',
                'currency' => 'TJS',
                'interest_rate_min' => null,
                'interest_rate_max' => null,
                'fees' => $feeValue,
                'fee_structure' => $this->buildTransferFeeStructure($item),
                'description' => [
                    'ru' => $item['textRU'] ?? null,
                    'en' => $item['textEN'] ?? null,
                    'tj' => $item['textTJ'] ?? null,
                ],
                'extra' => [
                    'image' => $item['image'] ?? null,
                ],
            ];
        }

        return ['products' => $products];
    }

    /**
     * Transform cards endpoint response.
     *
     * @param array<int, array<string, mixed>> $data
     * @return array{products: array<int, array<string, mixed>>}
     */
    public function transformCards(array $data): array
    {
        $products = [];

        foreach ($data as $item) {
            // Skip non-array items (API might return metadata)
            if (!is_array($item)) {
                continue;
            }

            $products[] = [
                'name' => [
                    'ru' => $item['titleRU'] ?? 'Card',
                    'en' => $item['titleEN'] ?? 'Card',
                    'tj' => $item['titleTJ'] ?? 'Корт',
                ],
                'type' => 'card',
                'currency' => 'TJS',
                'interest_rate_min' => null,
                'interest_rate_max' => null,
                'fees' => null,
                'description' => [
                    'ru' => $item['textRU'] ?? null,
                    'en' => $item['textEN'] ?? null,
                    'tj' => $item['textTJ'] ?? null,
                ],
                'extra' => [
                    'image' => $item['image'] ?? null,
                ],
            ];
        }

        return ['products' => $products];
    }

    /**
     * Build a credit product from slider credit and terms data.
     *
     * @param array<string, mixed> $credit
     * @param array<string, mixed> $terms
     * @param array<string, string> $categoryName
     * @return array<string, mixed>
     */
    private function buildCreditProduct(array $credit, array $terms, array $categoryName): array
    {
        // Extract interest rate range
        [$interestRateMin, $interestRateMax] = $this->extractInterestRateRange($terms);

        // Extract amounts
        $minAmount = $this->extractNumericValue($terms['minAmount1RU'] ?? $terms['minAmount1EN'] ?? null);
        $maxAmount = $this->extractNumericValue($terms['maxAmount1RU'] ?? $terms['maxAmount1EN'] ?? null);

        // Extract term range in months
        [$termMonthsMin, $termMonthsMax] = $this->extractTermRange($terms['loanTermRU'] ?? $terms['loanTermEN'] ?? null);

        // Build rate tiers if multiple amount/term combinations exist
        $rateTiers = $this->buildRateTiers($terms);

        // Extract required documents as array
        $requiredDocs = $this->buildDocumentsList($terms);

        return [
            'name' => [
                'ru' => $credit['titleRU'] ?? $categoryName['ru'],
                'en' => $credit['titleEN'] ?? $categoryName['en'],
                'tj' => $credit['titleTJ'] ?? $categoryName['tj'],
            ],
            'type' => 'credit',
            'currency' => $this->detectCurrency($terms),

            // Rate ranges
            'interest_rate_min' => $interestRateMin,
            'interest_rate_max' => $interestRateMax,

            // Term ranges
            'term_months_min' => $termMonthsMin,
            'term_months_max' => $termMonthsMax,

            // Rate tiers
            'rate_tiers' => $rateTiers,

            // Amount range
            'min_amount' => $minAmount,
            'max_amount' => $maxAmount,

            // Fees
            'fees' => null,
            'fee_structure' => null,

            // Product info
            'purpose' => $this->detectPurpose($categoryName['en']),

            // Eligibility
            'eligibility' => $this->buildEligibility($terms),
            'required_documents' => !empty($requiredDocs) ? $requiredDocs : null,

            'description' => [
                'ru' => $credit['textRU'] ?? null,
                'en' => $credit['textEN'] ?? null,
                'tj' => $credit['textTJ'] ?? null,
            ],
            'extra' => [
                'image' => $credit['image'] ?? null,
                'category_id' => $credit['creditCategoryId'] ?? null,
                'collateral' => $this->buildCollateralList($terms),
            ],
        ];
    }

    /**
     * Build rate tiers from terms data.
     * Format: {"tiers": [{"term_months": 12, "min_amount": 1000, "interest_rate": 24.0}, ...]}
     */
    private function buildRateTiers(array $terms): ?array
    {
        $tiers = [];

        // Look for multiple amount/rate combinations in terms
        for ($i = 1; $i <= 5; $i++) {
            $minAmountKey = "minAmount{$i}RU";
            $maxAmountKey = "maxAmount{$i}RU";
            $rateKey = "interestRate{$i}RU";

            $minAmount = $this->extractNumericValue($terms[$minAmountKey] ?? null);
            $maxAmount = $this->extractNumericValue($terms[$maxAmountKey] ?? null);
            $rate = $this->extractInterestRateFromString($terms[$rateKey] ?? null);

            if ($minAmount !== null || $maxAmount !== null || $rate !== null) {
                $tier = [];
                if ($minAmount !== null) $tier['min_amount'] = $minAmount;
                if ($maxAmount !== null) $tier['max_amount'] = $maxAmount;
                if ($rate !== null) $tier['interest_rate'] = $rate;

                if (!empty($tier)) {
                    $tiers[] = $tier;
                }
            }
        }

        return !empty($tiers) ? ['tiers' => $tiers] : null;
    }

    /**
     * Build fee structure for transfers.
     */
    private function buildTransferFeeStructure(array $item): ?array
    {
        $fees = [];

        for ($i = 1; $i <= 3; $i++) {
            $commValue = $this->extractNumericValue($item["commission{$i}RU"] ?? $item["commission{$i}EN"] ?? null);
            if ($commValue !== null) {
                $fees["commission_{$i}"] = $commValue;
            }
        }

        return !empty($fees) ? $fees : null;
    }

    /**
     * Detect product purpose from category name.
     */
    private function detectPurpose(string $categoryName): ?string
    {
        $lower = strtolower($categoryName);
        if (str_contains($lower, 'consumer') || str_contains($lower, 'potreb')) return 'consumer';
        if (str_contains($lower, 'auto') || str_contains($lower, 'car')) return 'auto';
        if (str_contains($lower, 'business') || str_contains($lower, 'biznes')) return 'business';
        if (str_contains($lower, 'mortgage') || str_contains($lower, 'ipoteka')) return 'mortgage';
        if (str_contains($lower, 'education') || str_contains($lower, 'talim')) return 'education';
        return 'consumer';
    }

    /**
     * Build eligibility as translatable.
     *
     * @return array<string, string|null>
     */
    private function buildEligibility(array $terms): array
    {
        $docsRu = $this->buildDocumentsListByLang($terms, 'RU');
        $docsEn = $this->buildDocumentsListByLang($terms, 'EN');
        $docsTj = $this->buildDocumentsListByLang($terms, 'TJ');

        return [
            'ru' => !empty($docsRu) ? 'Необходимые документы: ' . implode(', ', $docsRu) : null,
            'en' => !empty($docsEn) ? 'Required documents: ' . implode(', ', $docsEn) : null,
            'tj' => !empty($docsTj) ? 'Ҳуҷҷатҳои зарурӣ: ' . implode(', ', $docsTj) : null,
        ];
    }

    /**
     * Extract interest rate range from terms.
     * @return array{0: float|null, 1: float|null}
     */
    private function extractInterestRateRange(array $terms): array
    {
        $rates = [];

        // Look through all interest rate fields
        foreach ($terms as $key => $value) {
            if (Str::startsWith($key, 'interestRate') && is_string($value)) {
                $rate = $this->extractInterestRateFromString($value);
                if ($rate !== null) {
                    $rates[] = $rate;
                }
            }
        }

        if (empty($rates)) {
            return [null, null];
        }

        return [min($rates), max($rates)];
    }

    /**
     * Extract interest rate from string like "22%" or "от 18% до 24%".
     */
    private function extractInterestRateFromString(?string $value): ?float
    {
        if (!$value) {
            return null;
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*%/', $value, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    /**
     * Extract numeric value from string like "500 сомони" or "3 000 сомони".
     */
    private function extractNumericValue(?string $value): ?float
    {
        if (!$value) {
            return null;
        }

        // Remove non-numeric characters except digits, dots, and spaces
        $cleaned = preg_replace('/[^\d\s.]/', '', $value);
        $cleaned = str_replace(' ', '', $cleaned);

        if (is_numeric($cleaned)) {
            return (float) $cleaned;
        }

        return null;
    }

    /**
     * Extract term range from string like "от 3 до 12 месяцев".
     * @return array{0: int|null, 1: int|null}
     */
    private function extractTermRange(?string $value): array
    {
        if (!$value) {
            return [null, null];
        }

        $min = null;
        $max = null;

        // Look for "от X до Y" pattern
        if (preg_match('/(?:от|from)\s*(\d+)\s*(?:до|to)\s*(\d+)/iu', $value, $matches)) {
            $min = (int) $matches[1];
            $max = (int) $matches[2];
        }
        // Look for single max pattern "до 12 месяцев"
        elseif (preg_match('/(?:до|to)\s*(\d+)\s*(?:месяц|month|моҳ)/iu', $value, $matches)) {
            $max = (int) $matches[1];
        }
        // Look for single value
        elseif (preg_match('/(\d+)\s*(?:месяц|month|моҳ)/iu', $value, $matches)) {
            $min = $max = (int) $matches[1];
        }

        return [$min, $max];
    }

    /**
     * Detect currency from terms.
     */
    private function detectCurrency(array $terms): string
    {
        return 'TJS';
    }

    /**
     * Build list of collateral items.
     */
    private function buildCollateralList(array $terms): array
    {
        $collateral = [];
        foreach ($terms as $key => $value) {
            if (Str::startsWith($key, 'collateral') && Str::endsWith($key, 'RU') && is_string($value) && !empty(trim($value, '- '))) {
                $collateral[] = trim($value, '- ');
            }
        }
        return $collateral;
    }

    /**
     * Build list of required documents.
     */
    private function buildDocumentsList(array $terms): array
    {
        return $this->buildDocumentsListByLang($terms, 'RU');
    }

    /**
     * Build list of required documents by language.
     */
    private function buildDocumentsListByLang(array $terms, string $lang): array
    {
        $docs = [];
        foreach ($terms as $key => $value) {
            if (Str::startsWith($key, 'requiredDocuments') && Str::endsWith($key, $lang) && is_string($value) && !empty(trim($value, '- '))) {
                $docs[] = trim($value, '- ');
            }
        }
        return $docs;
    }
}
