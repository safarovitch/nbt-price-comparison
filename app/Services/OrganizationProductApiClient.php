<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrganizationProductApiClient
{
    public function __construct(
        private ?int $timeout = null,
    ) {
        $this->timeout ??= config('organizations.sync.http_timeout', 15);
    }

    /**
     * Fetch products from organization's specific endpoint.
     *
     * @return array{products: array<int, array<string, mixed>>, updated_at?: string}
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function fetchProducts(Organization $organization, string $endpointPath): array
    {
        $url = rtrim($organization->base_url, '/') . '/' . ltrim($endpointPath, '/');
        $headers = $this->buildHeaders($organization);

        Log::info('Fetching organization products', [
            'organization_uuid' => $organization->uuid,
            'url' => $url
        ]);

        $response = Http::timeout($this->timeout)
            ->withHeaders($headers)
            ->get($url);

        $response->throw();

        $data = $response->json();
        if (! is_array($data)) {
            throw new \InvalidArgumentException('Organization API returned non-JSON or non-object response.');
        }

        return $data;
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(Organization $organization): array
    {
        $authType = $organization->auth_type;
        $authValue = $organization->auth_value;

        if (empty($authValue)) {
            return [];
        }

        return match ($authType) {
            'api_key' => ['X-API-Key' => $authValue],
            'api_key_header' => ['Api-Key' => $authValue], // Payvand uses Api-Key header
            'bearer' => ['Authorization' => 'Bearer ' . $authValue],
            'token' => ['Authorization' => 'token ' . $authValue],
            'sha256_signature' => $this->buildSha256SignatureHeaders($authValue),
            default => [],
        };
    }

    /**
     * Build X-Key and X-Sign headers for SHA256 signature authentication.
     * Auth value format: "key:secret"
     *
     * @return array<string, string>
     */
    private function buildSha256SignatureHeaders(string $authValue): array
    {
        $parts = explode(':', $authValue, 2);
        if (count($parts) !== 2) {
            Log::warning('Invalid sha256_signature auth value format. Expected "key:secret"');
            return [];
        }

        [$key, $secret] = $parts;
        $signature = hash('sha256', $key . $secret);

        return [
            'X-Key' => $key,
            'X-Sign' => $signature,
        ];
    }
}
