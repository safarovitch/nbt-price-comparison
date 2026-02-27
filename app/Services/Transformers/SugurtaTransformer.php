<?php

namespace App\Services\Transformers;

/**
 * Transforms Сугуртаи Аввалини Миллӣ (SAM) API response to the standard product format.
 *
 * SAM API endpoint: /webhook/v1/products
 * Auth: x-api-key header
 * Base URL: https://api-sam01-sugurta.4rd07a.easypanel.host/webhook/v1
 */
class SugurtaTransformer
{
  /**
   * Transform products response.
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
   * Build a single product.
   *
   * @param array<string, mixed> $item
   * @return array<string, mixed>
   */
  private function buildProduct(array $item): array
  {
    return [
      'name' => $item['name'] ?? $item['title'] ?? 'Unknown Product',
      'type' => $this->normalizeType($item['type'] ?? $item['product_type'] ?? 'insurance'),
      'currency' => $item['currency'] ?? 'TJS',

      'interest_rate_min' => $this->extractFloat($item, 'interest_rate_min', 'interest_rate'),
      'interest_rate_max' => $this->extractFloat($item, 'interest_rate_max', 'interest_rate'),

      'term_months_min' => $this->extractInt($item, 'term_months_min', 'term_months'),
      'term_months_max' => $this->extractInt($item, 'term_months_max', 'term_months'),

      'min_amount' => isset($item['min_amount']) ? (float) $item['min_amount'] : null,
      'max_amount' => isset($item['max_amount']) ? (float) $item['max_amount'] : (isset($item['coverage_amount']) ? (float) $item['coverage_amount'] : null),

      'fees' => isset($item['fees']) ? (float) $item['fees'] : (isset($item['premium']) ? (float) $item['premium'] : null),
      'fee_structure' => $item['fee_structure'] ?? null,

      'eligibility' => $item['eligibility'] ?? null,
      'description' => $item['description'] ?? null,

      'extra' => $item['extra'] ?? null,
      'updated_at' => $item['updated_at'] ?? null,
    ];
  }

  /**
   * Normalize product type.
   */
  private function normalizeType(string $type): string
  {
    $type = strtolower(trim($type));

    return match (true) {
      in_array($type, ['credit', 'deposit', 'insurance', 'transfer', 'mortgage', 'card', 'islamic', 'other']) => $type,
      str_contains($type, 'insurance'), str_contains($type, 'sugurta') => 'insurance',
      str_contains($type, 'credit') => 'credit',
      str_contains($type, 'deposit') => 'deposit',
      default => 'insurance',
    };
  }

  /**
   * Extract float with fallback.
   */
  private function extractFloat(array $item, string $primaryKey, string $fallbackKey): ?float
  {
    if (isset($item[$primaryKey])) {
      return (float) $item[$primaryKey];
    }

    if (isset($item[$fallbackKey])) {
      return (float) $item[$fallbackKey];
    }

    return null;
  }

  /**
   * Extract int with fallback.
   */
  private function extractInt(array $item, string $primaryKey, string $fallbackKey): ?int
  {
    if (isset($item[$primaryKey])) {
      return (int) $item[$primaryKey];
    }

    if (isset($item[$fallbackKey])) {
      return (int) $item[$fallbackKey];
    }

    return null;
  }
}
