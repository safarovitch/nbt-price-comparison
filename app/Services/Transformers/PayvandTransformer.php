<?php

namespace App\Services\Transformers;

/**
 * Transforms Payvand API response format to the standard product format.
 * 
 * Payvand API returns services grouped by category like:
 * {
 *   "services": [
 *     { "Category Name": [ { name, srvid, paymin, paymax, service-fee, input-data }, ... ] }
 *   ]
 * }
 */
class PayvandTransformer
{
    /**
     * Map Payvand categories to our product types.
     */
    private const CATEGORY_TYPE_MAP = [
        'electronic payment systems' => 'transfer',
        'banking services' => 'card',
        'credits' => 'credit',
        'deposits' => 'deposit',
        'insurance' => 'insurance',
        'money transfers' => 'transfer',
    ];

    /**
     * Transform Payvand services response.
     *
     * @param array<string, mixed> $data
     * @return array{products: array<int, array<string, mixed>>}
     */
    public function transformServices(array $data): array
    {
        $products = [];

        // Check if response has services array
        $services = $data['services'] ?? [];

        foreach ($services as $categoryGroup) {
            // Each item is an object with category name as key
            foreach ($categoryGroup as $categoryName => $categoryServices) {
                $productType = $this->mapCategoryToType($categoryName);

                foreach ($categoryServices as $service) {
                    $products[] = $this->buildProduct($service, $productType, $categoryName);
                }
            }
        }

        return ['products' => $products];
    }

    /**
     * Build a product from Payvand service data.
     *
     * @param array<string, mixed> $service
     * @param string $productType
     * @param string $categoryName
     * @return array<string, mixed>
     */
    private function buildProduct(array $service, string $productType, string $categoryName): array
    {
        $name = $service['name'] ?? 'Unknown Service';
        $minAmount = isset($service['paymin']) ? (float) $service['paymin'] : null;
        $maxAmount = isset($service['paymax']) ? (float) $service['paymax'] : null;
        $serviceFee = isset($service['service-fee']) ? (float) $service['service-fee'] : null;

        // Build eligibility/input requirements
        $eligibility = $this->buildEligibility($service['input-data'] ?? []);
        $requiredFields = $this->buildRequiredFields($service['input-data'] ?? []);

        return [
            'name' => [
                'ru' => $name,
                'en' => $name,
                'tj' => $name,
            ],
            'type' => $productType,
            'currency' => 'TJS',

            // Rate ranges - Payvand services don't have interest rates
            'interest_rate_min' => null,
            'interest_rate_max' => null,

            // Term ranges - not applicable for Payvand services
            'term_months_min' => null,
            'term_months_max' => null,

            // Amount range
            'min_amount' => $minAmount,
            'max_amount' => $maxAmount,

            // Fees
            'fees' => $serviceFee,
            'fee_structure' => $serviceFee !== null ? [
                'service' => $serviceFee,
            ] : null,

            // Eligibility
            'eligibility' => $eligibility,
            'required_documents' => !empty($requiredFields) ? $requiredFields : null,

            // Application
            'online_application' => true, // Payvand is a digital payment system

            'description' => [
                'ru' => "Сервис категории: {$categoryName}",
                'en' => "Service category: {$categoryName}",
                'tj' => "Категорияи хидмат: {$categoryName}",
            ],
            'extra' => [
                'srvid' => $service['srvid'] ?? null,
                'category' => $categoryName,
                'input_data' => $service['input-data'] ?? [],
            ],
        ];
    }

    /**
     * Build eligibility from input-data requirements.
     *
     * @param array<int, array<string, mixed>> $inputData
     * @return array<string, string|null>
     */
    private function buildEligibility(array $inputData): array
    {
        if (empty($inputData)) {
            return ['ru' => null, 'en' => null, 'tj' => null];
        }

        $fields = [];
        foreach ($inputData as $input) {
            $fields[] = $input['title'] ?? 'Unknown field';
        }

        $fieldList = implode(', ', $fields);

        return [
            'ru' => "Необходимые данные: {$fieldList}",
            'en' => "Required data: {$fieldList}",
            'tj' => "Маълумоти зарурӣ: {$fieldList}",
        ];
    }

    /**
     * Build required fields array from input-data.
     *
     * @param array<int, array<string, mixed>> $inputData
     * @return array<string>
     */
    private function buildRequiredFields(array $inputData): array
    {
        $fields = [];
        foreach ($inputData as $input) {
            if (isset($input['title'])) {
                $fields[] = $input['title'];
            }
        }
        return $fields;
    }

    /**
     * Map Payvand category name to product type.
     */
    private function mapCategoryToType(string $categoryName): string
    {
        $normalized = strtolower(trim($categoryName));

        return self::CATEGORY_TYPE_MAP[$normalized] ?? 'other';
    }
}
