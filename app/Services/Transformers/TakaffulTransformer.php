<?php

namespace App\Services\Transformers;

/**
 * Transforms Takafful (Islamic Insurance) API response to the standard product format.
 *
 * Takafful API endpoint: /nbt/financial-products
 * Auth: X-API-Key header
 * Expected response: { "products": [...] } with insurance products.
 */
class TakaffulTransformer
{
    /**
     * Transform financial products response.
     *
     * @param array<string, mixed> $data
     * @return array{products: array<int, array<string, mixed>>}
     */
    public function transformProducts(array $data): array
    {
        $products = $data['products'] ?? $data['data'] ?? [];

        if (empty($products)) {
            // If data is a flat array of products (no wrapper key)
            if (isset($data[0]) && is_array($data[0])) {
                $products = $data;
            } else {
                return ['products' => []];
            }
        }

        $result = [];

        foreach ($products as $item) {
            if (!is_array($item)) {
                continue;
            }

            $result[] = $this->buildProduct($item);
        }

        return ['products' => $result];
    }

    /**
     * Build a product from Takafful data.
     *
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function buildProduct(array $item): array
    {
        return [
            'name' => $this->extractName($item),
            'type' => $this->mapType($item['type'] ?? $item['product_type'] ?? 'insurance'),
            'currency' => $item['currency'] ?? 'TJS',

            'interest_rate_min' => isset($item['interest_rate_min']) ? (float) $item['interest_rate_min'] : (isset($item['interest_rate']) ? (float) $item['interest_rate'] : null),
            'interest_rate_max' => isset($item['interest_rate_max']) ? (float) $item['interest_rate_max'] : (isset($item['interest_rate']) ? (float) $item['interest_rate'] : null),

            'term_months_min' => isset($item['term_months_min']) ? (int) $item['term_months_min'] : (isset($item['term_months']) ? (int) $item['term_months'] : null),
            'term_months_max' => isset($item['term_months_max']) ? (int) $item['term_months_max'] : (isset($item['term_months']) ? (int) $item['term_months'] : null),

            'min_amount' => isset($item['min_amount']) ? (float) $item['min_amount'] : null,
            'max_amount' => isset($item['max_amount']) ? (float) $item['max_amount'] : (isset($item['coverage_amount']) ? (float) $item['coverage_amount'] : null),

            'fees' => isset($item['fees']) ? (float) $item['fees'] : (isset($item['premium']) ? (float) $item['premium'] : null),
            'fee_structure' => $item['fee_structure'] ?? null,

            'eligibility' => $this->extractTranslatable($item, 'eligibility'),
            'description' => $this->extractTranslatable($item, 'description'),

            'extra' => $item['extra'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
        ];
    }

    /**
     * Extract translatable name field.
     */
    private function extractName(array $item): array|string
    {
        if (isset($item['name']) && is_array($item['name'])) {
            return $item['name'];
        }

        return $item['name'] ?? 'Unknown Product';
    }

    /**
     * Extract translatable field.
     */
    private function extractTranslatable(array $item, string $field): array|string|null
    {
        if (!isset($item[$field])) {
            return null;
        }

        return $item[$field];
    }

    /**
     * Map product type to standard types.
     */
    private function mapType(string $type): string
    {
        $normalized = strtolower(trim($type));

        return match (true) {
            str_contains($normalized, 'insurance'), str_contains($normalized, 'sugurta'), str_contains($normalized, 'takafful') => 'insurance',
            str_contains($normalized, 'credit'), str_contains($normalized, 'loan') => 'credit',
            str_contains($normalized, 'deposit') => 'deposit',
            str_contains($normalized, 'card') => 'card',
            str_contains($normalized, 'transfer') => 'transfer',
            default => $normalized,
        };
    }
}
