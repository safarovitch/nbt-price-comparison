<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MerchantProductApiClient
{
    public function __construct(
        private ?int $timeout = null,
    ) {
        $this->timeout ??= config('merchants.sync.http_timeout', 15);
    }

    /**
     * Fetch products from merchant's OpenAPI /products endpoint.
     *
     * @return array{products: array<int, array<string, mixed>>, updated_at?: string}
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function fetchProducts(Merchant $merchant): array
    {
        $url = rtrim($merchant->base_url, '/') . '/products';
        $headers = $this->buildHeaders($merchant);

        Log::info('Fetching merchant products', ['merchant_id' => $merchant->id, 'url' => $url]);

        $response = Http::timeout($this->timeout)
            ->withHeaders($headers)
            ->get($url);

        $response->throw();

        $data = $response->json();
        if (! is_array($data)) {
            throw new \InvalidArgumentException('Merchant API returned non-JSON or non-object response.');
        }

        return $data;
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(Merchant $merchant): array
    {
        $authType = $merchant->auth_type;
        $authValue = $merchant->auth_value;

        if (empty($authValue)) {
            return [];
        }

        return match ($authType) {
            'api_key' => ['X-API-Key' => $authValue],
            'bearer' => ['Authorization' => 'Bearer ' . $authValue],
            default => [],
        };
    }
}
