<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Services\OrganizationProductApiClient;
use App\Services\ProductDataValidator;
use App\Services\Transformers\PayvandTransformer;
use App\Services\Transformers\ShukrMoliyaTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SyncOrganizationProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Organization $organization
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OrganizationProductApiClient $apiClient, ProductDataValidator $validator): void
    {
        $organization = $this->organization;

        Log::info('SyncOrganizationProductsJob started', ['organization_uuid' => $organization->uuid]);

        // Mark as syncing
        $organization->update([
            'last_sync_status' => 'syncing',
            'sync_started_at' => now(),
            'last_sync_error' => null,
        ]);

        try {
            $endpoints = $organization->endpoints ?? [];
            if (empty($endpoints)) {
                Log::warning('SyncOrganizationProductsJob: no endpoints defined for organization', [
                    'organization_uuid' => $organization->uuid
                ]);
                $organization->update(['last_sync_status' => 'idle']);
                return;
            }

            $allProducts = [];
            $syncErrors = [];

            foreach ($endpoints as $type => $path) {
                try {
                    Log::info('Syncing endpoint', [
                        'organization_uuid' => $organization->uuid,
                        'type' => $type,
                        'path' => $path
                    ]);

                    $data = $apiClient->fetchProducts($organization, $path);

                    // Transform data for organizations with custom API formats
                    $data = $this->transformDataIfNeeded($organization, $type, $data);

                    $products = $validator->validateAndNormalize($data);

                    $allProducts = array_merge($allProducts, $products);
                } catch (\Throwable $e) {
                    $errorMessage = "Endpoint '{$path}' failed: " . $e->getMessage();
                    Log::error('SyncOrganizationProductsJob: failed to sync endpoint', [
                        'organization_uuid' => $organization->uuid,
                        'endpoint_type' => $type,
                        'endpoint_path' => $path,
                        'error' => $e->getMessage(),
                    ]);
                    $syncErrors[] = $errorMessage;
                }
            }

            if (empty($allProducts) && !empty($endpoints)) {
                Log::warning('SyncOrganizationProductsJob: no products found across all endpoints', [
                    'organization_uuid' => $organization->uuid
                ]);
            }

            $organization->products()->delete();

            foreach ($allProducts as $attrs) {
                $organization->products()->create(array_merge($attrs, [
                    'organization_uuid' => $organization->uuid,
                ]));
            }

            $organization->last_synced_at = now();

            if (!empty($syncErrors)) {
                $organization->last_sync_status = 'failed';
                $organization->last_sync_error = implode("\n", $syncErrors);
            } else {
                $organization->last_sync_status = 'success';
                $organization->last_sync_error = null;
            }

            $organization->save();

            Log::info('SyncOrganizationProductsJob completed', [
                'organization_uuid' => $organization->uuid,
                'products_count' => count($allProducts),
            ]);
        } catch (\Throwable $e) {
            Log::error('SyncOrganizationProductsJob: fatal error', [
                'organization_uuid' => $organization->uuid,
                'error' => $e->getMessage(),
            ]);
            $organization->update([
                'last_sync_status' => 'failed',
                'last_sync_error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Transform raw API data for organizations with custom API formats.
     */
    private function transformDataIfNeeded(Organization $organization, string $endpointType, array $data): array
    {
        $baseUrl = strtolower($organization->base_url ?? '');

        // Shukr Moliya uses a custom API format
        if (str_contains($baseUrl, 'shukr.tj')) {
            $transformer = new ShukrMoliyaTransformer();

            return match ($endpointType) {
                'credits' => $transformer->transformCredits($data),
                'transfers' => $transformer->transformTransfers($data),
                'cards' => $transformer->transformCards($data),
                default => $data,
            };
        }

        // Payvand uses a services grouped by category format
        if (str_contains($baseUrl, 'payvand')) {
            $transformer = new PayvandTransformer();
            return $transformer->transformServices($data);
        }

        return $data;
    }
}
