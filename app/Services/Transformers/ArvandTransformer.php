<?php

namespace App\Services\Transformers;

/**
 * Transforms Arvand bank API response to the standard product format.
 *
 * Arvand API endpoint: /Deposits/Products/AvailableProducts
 * Auth: Basic auth (username: NBT_Abad, password: BSekwLSxBh6k3Pp)
 * Base URL: https://192.168.100.127/OnlineBank.Webapi/api
 */
class ArvandTransformer
{
  /**
   * Transform available products response.
   *
   * @param array<string, mixed> $data
   * @return array{products: array<int, array<string, mixed>>}
   */
  public function transformProducts(array $data): array
  {
    $items = $data['products'] ?? $data['data'] ?? $data['items'] ?? [];

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
   * Build a single product from Arvand data.
   *
   * @param array<string, mixed> $item
   * @return array<string, mixed>
   */
  private function buildProduct(array $item): array
  {
    return [
      'name' => $item['name'] ?? $item['Name'] ?? $item['title'] ?? $item['Title'] ?? 'Unknown Product',
      'type' => $this->normalizeType($item['type'] ?? $item['Type'] ?? 'deposit'),
      'currency' => $this->normalizeCurrency($item['currency'] ?? $item['Currency'] ?? $item['currencyCode'] ?? 'TJS'),

      'interest_rate_min' => $this->extractFloatMulti($item, ['interest_rate_min', 'InterestRateMin', 'interest_rate', 'InterestRate', 'Rate']),
      'interest_rate_max' => $this->extractFloatMulti($item, ['interest_rate_max', 'InterestRateMax', 'interest_rate', 'InterestRate', 'Rate']),

      'term_months_min' => $this->extractIntMulti($item, ['term_months_min', 'TermMonthsMin', 'term_months', 'TermMonths', 'MinTerm']),
      'term_months_max' => $this->extractIntMulti($item, ['term_months_max', 'TermMonthsMax', 'term_months', 'TermMonths', 'MaxTerm']),

      'min_amount' => $this->extractFloatMulti($item, ['min_amount', 'MinAmount', 'MinSum']),
      'max_amount' => $this->extractFloatMulti($item, ['max_amount', 'MaxAmount', 'MaxSum']),

      'fees' => $this->extractFloatMulti($item, ['fees', 'Fees', 'Fee']),
      'fee_structure' => $item['fee_structure'] ?? null,

      'eligibility' => $item['eligibility'] ?? $item['Eligibility'] ?? null,
      'description' => $item['description'] ?? $item['Description'] ?? null,

      'extra' => $item['extra'] ?? null,
      'updated_at' => $item['updated_at'] ?? $item['UpdatedAt'] ?? null,
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
      str_contains($type, 'deposit') => 'deposit',
      str_contains($type, 'credit'), str_contains($type, 'loan') => 'credit',
      str_contains($type, 'card') => 'card',
      default => 'deposit',
    };
  }

  /**
   * Normalize currency code.
   */
  private function normalizeCurrency(string $currency): string
  {
    $currency = strtoupper(trim($currency));

    return match ($currency) {
      'TJS', 'USD', 'EUR', 'RUB' => $currency,
      '972' => 'TJS',
      '840' => 'USD',
      '978' => 'EUR',
      '643' => 'RUB',
      default => 'TJS',
    };
  }

  /**
   * Extract float from multiple possible keys.
   */
  private function extractFloatMulti(array $item, array $keys): ?float
  {
    foreach ($keys as $key) {
      if (isset($item[$key]) && is_numeric($item[$key])) {
        return (float) $item[$key];
      }
    }

    return null;
  }

  /**
   * Extract int from multiple possible keys.
   */
  private function extractIntMulti(array $item, array $keys): ?int
  {
    foreach ($keys as $key) {
      if (isset($item[$key]) && is_numeric($item[$key])) {
        return (int) $item[$key];
      }
    }

    return null;
  }
}
