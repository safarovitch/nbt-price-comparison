<?php

namespace App\Services\Transformers;

/**
 * Transforms Alif API response to the standard product format.
 *
 * Alif API endpoint: /v1/products?type={type}&currency={currency}
 * Auth: X-API-Key header
 * Base URL: https://products4nbt-api.alif.tj/v1
 *
 * Response format:
 * {
 *   "products": [
 *     {
 *       "name": "string",
 *       "type": "string",
 *       "currency": "string",
 *       "interest_rate": float,
 *       "fees": float,
 *       "term_months": int,
 *       "min_amount": float,
 *       "max_amount": float,
 *       "eligibility": "string",
 *       "description": "string",
 *       "updated_at": "datetime",
 *       "extra": {}
 *     }
 *   ],
 *   "updated_at": "datetime"
 * }
 */
class AlifTransformer
{
  /**
   * Transform products response.
   * Alif uses mostly-standard format but with single interest_rate / term_months values.
   *
   * @param array<string, mixed> $data
   * @return array{products: array<int, array<string, mixed>>}
   */
  public function transformProducts(array $data): array
  {
    $items = $data['products'] ?? $data['data'] ?? [];

    if (empty($items)) {
      if (isset($data[0]) && is_array($data[0])) {
        $items = $data;
      } else {
        return ['products' => []];
      }
    }

    $products = [];

    foreach ($items as $item) {
      if (!is_array($item)) {
        continue;
      }

      $products[] = $this->buildProduct($item);
    }

    return ['products' => $products];
  }

  /**
   * Build a single product from Alif API data.
   *
   * @param array<string, mixed> $item
   * @return array<string, mixed>
   */
  private function buildProduct(array $item): array
  {
    $type = strtolower($item['type'] ?? 'other');

    return [
      'name' => $item['name'] ?? 'Unknown Product',
      'type' => $this->normalizeType($type),
      'currency' => $item['currency'] ?? 'TJS',

      // Alif uses single interest_rate field
      'interest_rate_min' => isset($item['interest_rate']) ? (float) $item['interest_rate'] : (isset($item['interest_rate_min']) ? (float) $item['interest_rate_min'] : null),
      'interest_rate_max' => isset($item['interest_rate']) ? (float) $item['interest_rate'] : (isset($item['interest_rate_max']) ? (float) $item['interest_rate_max'] : null),

      // Alif uses single term_months field
      'term_months_min' => isset($item['term_months']) ? (int) $item['term_months'] : (isset($item['term_months_min']) ? (int) $item['term_months_min'] : null),
      'term_months_max' => isset($item['term_months']) ? (int) $item['term_months'] : (isset($item['term_months_max']) ? (int) $item['term_months_max'] : null),

      'min_amount' => isset($item['min_amount']) ? (float) $item['min_amount'] : null,
      'max_amount' => isset($item['max_amount']) ? (float) $item['max_amount'] : null,

      'fees' => isset($item['fees']) ? (float) $item['fees'] : null,
      'fee_structure' => $item['fee_structure'] ?? null,

      'eligibility' => $item['eligibility'] ?? null,
      'description' => $item['description'] ?? null,

      'extra' => $item['extra'] ?? null,
      'updated_at' => $item['updated_at'] ?? null,
    ];
  }

  /**
   * Normalize product type to standard types.
   */
  private function normalizeType(string $type): string
  {
    return match (true) {
      in_array($type, ['credit', 'deposit', 'insurance', 'transfer', 'mortgage', 'card', 'islamic', 'other']) => $type,
      str_contains($type, 'credit'), str_contains($type, 'loan') => 'credit',
      str_contains($type, 'deposit') => 'deposit',
      str_contains($type, 'card') => 'card',
      str_contains($type, 'transfer') => 'transfer',
      str_contains($type, 'insurance') => 'insurance',
      default => 'other',
    };
  }
}
