<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductDataValidator
{
    private const PRODUCT_TYPES = [
        'credit', 'deposit', 'insurance', 'transfer',
        'mortgage', 'card', 'islamic', 'other',
    ];

    private const CURRENCIES = ['TJS', 'USD', 'EUR', 'RUB'];

    /**
     * Validate raw API payload and return normalized product arrays for Product model.
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
            'products.*.name' => ['required', 'string'],
            'products.*.type' => ['required', 'string', 'in:' . implode(',', self::PRODUCT_TYPES)],
            'products.*.currency' => ['required', 'string', 'in:' . implode(',', self::CURRENCIES)],
            'products.*.interest_rate' => ['nullable', 'numeric'],
            'products.*.fees' => ['nullable', 'numeric'],
            'products.*.term_months' => ['nullable', 'integer'],
            'products.*.min_amount' => ['nullable', 'numeric'],
            'products.*.max_amount' => ['nullable', 'numeric'],
            'products.*.eligibility' => ['nullable', 'string'],
            'products.*.description' => ['nullable', 'string'],
            'products.*.extra' => ['nullable', 'array'],
            'products.*.updated_at' => ['nullable', 'string'],
        ]);

        $validator->validate();

        $products = [];
        foreach ($payload['products'] as $item) {
            $products[] = [
                'name' => $item['name'],
                'type' => $item['type'],
                'currency' => $item['currency'],
                'interest_rate' => $item['interest_rate'] ?? null,
                'fees' => $item['fees'] ?? null,
                'term_months' => isset($item['term_months']) ? (int) $item['term_months'] : null,
                'min_amount' => $item['min_amount'] ?? null,
                'max_amount' => $item['max_amount'] ?? null,
                'eligibility' => $item['eligibility'] ?? null,
                'description' => $item['description'] ?? null,
                'extra' => $item['extra'] ?? null,
                'remote_updated_at' => isset($item['updated_at']) ? $item['updated_at'] : null,
            ];
        }

        return $products;
    }
}
