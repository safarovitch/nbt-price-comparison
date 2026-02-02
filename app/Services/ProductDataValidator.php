<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductDataValidator
{
    private const PRODUCT_TYPES = [
        'credit',
        'deposit',
        'insurance',
        'transfer',
        'mortgage',
        'card',
        'islamic',
        'other',
    ];

    private const CURRENCIES = ['TJS', 'USD', 'EUR', 'RUB'];

    private const CURRENCY_MAP = [
        'TJS' => 972,
        'USD' => 840,
        'EUR' => 978,
        'RUB' => 643,
    ];

    /**
     * Validate raw API payload and return normalized product arrays for Product model.
     * Supports both simple strings and translatable arrays for name, description, eligibility.
     * Supports extended fields: rate ranges, term ranges, rate_tiers, fee_structure.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     *
     * @throws ValidationException
     */
    public function validateAndNormalize(array $payload): array
    {
        $validator = Validator::make($payload, [
            'products' => ['required', 'array'],
            'products.*' => ['required', 'array'],
            // Name can be string or array (translatable)
            'products.*.name' => ['required'],
            'products.*.type' => ['required', 'string', 'in:' . implode(',', self::PRODUCT_TYPES)],
            'products.*.currency' => ['required', 'string', 'in:' . implode(',', self::CURRENCIES)],

            // Rate fields (support both single value and ranges)
            'products.*.interest_rate' => ['nullable', 'numeric'],
            'products.*.interest_rate_min' => ['nullable', 'numeric'],
            'products.*.interest_rate_max' => ['nullable', 'numeric'],

            // Term fields (support both single value and ranges)
            'products.*.term_months' => ['nullable', 'integer'],
            'products.*.term_months_min' => ['nullable', 'integer'],
            'products.*.term_months_max' => ['nullable', 'integer'],

            // Rate tiers for term/amount-based rates
            'products.*.rate_tiers' => ['nullable', 'array'],
            'products.*.rate_tiers.tiers' => ['nullable', 'array'],

            // Amount range
            'products.*.min_amount' => ['nullable', 'numeric'],
            'products.*.max_amount' => ['nullable', 'numeric'],

            // Fees
            'products.*.fees' => ['nullable', 'numeric'],
            'products.*.fee_structure' => ['nullable', 'array'],

            // Product categorization
            'products.*.purpose' => ['nullable', 'string'],

            // Card-specific
            'products.*.grace_period_days' => ['nullable', 'integer'],

            // Application info
            'products.*.online_application' => ['nullable', 'boolean'],
            'products.*.approval_time' => ['nullable', 'string'],

            // Eligibility
            'products.*.eligibility' => ['nullable'],
            'products.*.required_documents' => ['nullable', 'array'],
            'products.*.min_age' => ['nullable', 'integer'],
            'products.*.max_age' => ['nullable', 'integer'],

            // General
            'products.*.description' => ['nullable'],
            'products.*.extra' => ['nullable', 'array'],
            'products.*.updated_at' => ['nullable', 'string'],
        ]);

        $validator->validate();

        $products = [];
        foreach ($payload['products'] as $item) {
            $products[] = $this->normalizeProduct($item);
        }

        return $products;
    }

    /**
     * Normalize a single product from API data to model format.
     */
    private function normalizeProduct(array $item): array
    {
        // Handle interest rate: support both single value and ranges
        $interestRateMin = $item['interest_rate_min'] ?? $item['interest_rate'] ?? null;
        $interestRateMax = $item['interest_rate_max'] ?? $item['interest_rate'] ?? null;

        // Handle term: support both single value and ranges
        $termMonthsMin = $item['term_months_min'] ?? $item['term_months'] ?? null;
        $termMonthsMax = $item['term_months_max'] ?? $item['term_months'] ?? null;

        return [
            'name' => $this->normalizeTranslatable($item['name']),
            'type' => $item['type'],
            'currency_code' => self::CURRENCY_MAP[$item['currency']],

            // Rate ranges
            'interest_rate_min' => $interestRateMin !== null ? (float) $interestRateMin : null,
            'interest_rate_max' => $interestRateMax !== null ? (float) $interestRateMax : null,

            // Term ranges
            'term_months_min' => $termMonthsMin !== null ? (int) $termMonthsMin : null,
            'term_months_max' => $termMonthsMax !== null ? (int) $termMonthsMax : null,

            // Rate tiers
            'rate_tiers' => $item['rate_tiers'] ?? null,

            // Amount range
            'min_amount' => $item['min_amount'] ?? null,
            'max_amount' => $item['max_amount'] ?? $item['coverage_amount'] ?? null,

            // Fees
            'fee_structure' => $item['fee_structure'] ?? null,
            'fees' => $item['fees'] ?? $item['premium'] ?? null,

            // Product categorization
            'purpose' => $item['purpose'] ?? null,

            // Card-specific
            'grace_period_days' => isset($item['grace_period_days']) ? (int) $item['grace_period_days'] : null,

            // Application info
            'online_application' => $item['online_application'] ?? false,
            'approval_time' => $item['approval_time'] ?? null,

            // Eligibility
            'eligibility' => $this->normalizeTranslatable($item['eligibility'] ?? null),
            'required_documents' => $item['required_documents'] ?? null,
            'min_age' => isset($item['min_age']) ? (int) $item['min_age'] : null,
            'max_age' => isset($item['max_age']) ? (int) $item['max_age'] : null,

            // General
            'description' => $this->normalizeTranslatable($item['description'] ?? null),
            'extra' => $item['extra'] ?? null,
            'remote_updated_at' => $item['updated_at'] ?? null,
        ];
    }

    /**
     * Normalize a translatable field.
     * If already an array with language keys, return as-is.
     * If a string, convert to array with 'ru' as default locale.
     *
     * @param string|array<string, string>|null $value
     * @return array<string, string|null>|null
     */
    private function normalizeTranslatable(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            // Already in translatable format
            return $value;
        }

        // Convert string to translatable format with 'ru' as default
        return ['ru' => (string) $value];
    }
}
