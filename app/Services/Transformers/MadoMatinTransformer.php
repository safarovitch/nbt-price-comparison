<?php

namespace App\Services\Transformers;

/**
 * Transforms MADO MATIN API response to the standard product format.
 *
 * MADO MATIN API has separate endpoints:
 *   /credits/   - credit products
 *   /deposits/  - deposit products
 *   /plastic-cards/ - plastic card products
 *
 * Auth: X-API-KEY header
 * Base URL: https://mtpay.tj:4460/api (production)
 */
class MadoMatinTransformer
{
  /**
   * Transform credits endpoint response.
   *
   * @param array<string, mixed> $data
   * @return array{products: array<int, array<string, mixed>>}
   */
  public function transformCredits(array $data): array
  {
    return $this->transformEndpoint($data, 'credit');
  }

  /**
   * Transform deposits endpoint response.
   *
   * @param array<string, mixed> $data
   * @return array{products: array<int, array<string, mixed>>}
   */
  public function transformDeposits(array $data): array
  {
    return $this->transformEndpoint($data, 'deposit');
  }

  /**
   * Transform plastic cards endpoint response.
   *
   * @param array<string, mixed> $data
   * @return array{products: array<int, array<string, mixed>>}
   */
  public function transformPlasticCards(array $data): array
  {
    return $this->transformEndpoint($data, 'card');
  }

  /**
   * Generic endpoint transformer.
   *
   * @param array<string, mixed> $data
   * @param string $defaultType
   * @return array{products: array<int, array<string, mixed>>}
   */
  private function transformEndpoint(array $data, string $defaultType): array
  {
    // Try to extract products array from various response structures
    $items = $data['products'] ?? $data['data'] ?? $data['items'] ?? [];

    if (empty($items)) {
      // If data is flat array of products
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

      $products[] = $this->buildProduct($item, $defaultType);
    }

    return ['products' => $products];
  }

  /**
   * Build a single product from API data.
   *
   * @param array<string, mixed> $item
   * @param string $defaultType
   * @return array<string, mixed>
   */
  private function buildProduct(array $item, string $defaultType): array
  {
    return [
      'name' => $this->extractName($item),
      'type' => $item['type'] ?? $defaultType,
      'currency' => $item['currency'] ?? 'TJS',

      'interest_rate_min' => $this->extractFloat($item, 'interest_rate_min', 'interest_rate'),
      'interest_rate_max' => $this->extractFloat($item, 'interest_rate_max', 'interest_rate'),

      'term_months_min' => $this->extractInt($item, 'term_months_min', 'term_months'),
      'term_months_max' => $this->extractInt($item, 'term_months_max', 'term_months'),

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
   * Extract product name (supports translatable arrays).
   */
  private function extractName(array $item): array|string
  {
    if (isset($item['name'])) {
      return $item['name'];
    }

    if (isset($item['title'])) {
      return $item['title'];
    }

    return 'Unknown Product';
  }

  /**
   * Extract float value with fallback field.
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
   * Extract int value with fallback field.
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
