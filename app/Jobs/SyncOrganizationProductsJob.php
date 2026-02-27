<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Services\OrganizationProductApiClient;
use App\Services\ProductDataValidator;
use App\Services\Transformers\AlifTransformer;
use App\Services\Transformers\AmonatbonkTransformer;
use App\Services\Transformers\ArvandTransformer;
use App\Services\Transformers\EminTransformer;
use App\Services\Transformers\IcbTransformer;
use App\Services\Transformers\MadoMatinTransformer;
use App\Services\Transformers\OxusTransformer;
use App\Services\Transformers\PayvandTransformer;
use App\Services\Transformers\SanoatsodirotbonkTransformer;
use App\Services\Transformers\ShukrMoliyaTransformer;
use App\Services\Transformers\SugurtaTransformer;
use App\Services\Transformers\TakaffulTransformer;
use App\Services\Transformers\TavhidbonkTransformer;
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

    // Takafful
    if (str_contains($baseUrl, 'takafful')) {
      $transformer = new TakaffulTransformer();
      return $transformer->transformProducts($data);
    }

    // Shukr Moliya
    if (str_contains($baseUrl, 'shukr.tj')) {
      $transformer = new ShukrMoliyaTransformer();

      return match ($endpointType) {
        'credits' => $transformer->transformCredits($data),
        'transfers' => $transformer->transformTransfers($data),
        'cards' => $transformer->transformCards($data),
        default => $data,
      };
    }

    // Payvand
    if (str_contains($baseUrl, 'payvand')) {
      $transformer = new PayvandTransformer();
      return $transformer->transformServices($data);
    }

    // MDO OXUS
    if (str_contains($baseUrl, 'lolcfinance') || str_contains($baseUrl, 'oxus')) {
      $transformer = new OxusTransformer();
      return $transformer->transformProducts($data);
    }

    // MADO MATIN
    if (str_contains($baseUrl, 'mtpay') || str_contains($baseUrl, '192.168.10.117')) {
      $transformer = new MadoMatinTransformer();

      return match ($endpointType) {
        'credits' => $transformer->transformCredits($data),
        'deposits' => $transformer->transformDeposits($data),
        'plastic_cards' => $transformer->transformPlasticCards($data),
        default => $data,
      };
    }

    // Emin
    if (str_contains($baseUrl, 'emin.tj')) {
      $transformer = new EminTransformer();

      return match ($endpointType) {
        'credits' => $transformer->transformCredits($data),
        'deposits' => $transformer->transformDeposits($data),
        default => $data,
      };
    }

    // Alif
    if (str_contains($baseUrl, 'alif.tj')) {
      $transformer = new AlifTransformer();
      return $transformer->transformProducts($data);
    }

    // Amonatbonk
    if (str_contains($baseUrl, 'amonatbonk')) {
      $transformer = new AmonatbonkTransformer();
      return $transformer->transformProducts($data);
    }

    // Сугуртаи Аввалини Миллӣ (SAM)
    if (str_contains($baseUrl, 'sam01-sugurta') || str_contains($baseUrl, 'sugurta')) {
      $transformer = new SugurtaTransformer();
      return $transformer->transformProducts($data);
    }

    // Arvand
    if (str_contains($baseUrl, '192.168.100.127') || str_contains($baseUrl, 'arvand')) {
      $transformer = new ArvandTransformer();
      return $transformer->transformProducts($data);
    }

    // Tavhidbonk
    if (str_contains($baseUrl, 'tawhid') || str_contains($baseUrl, 'tavhid')) {
      $transformer = new TavhidbonkTransformer();
      return $transformer->transformProducts($data);
    }

    // Sanoatsodirotbonk
    if (str_contains($baseUrl, '109.74.66.233') || str_contains($baseUrl, 'sanoat')) {
      $transformer = new SanoatsodirotbonkTransformer();
      return $transformer->transformProducts($data);
    }

    // ICB
    if (str_contains($baseUrl, 'icb.tj')) {
      $transformer = new IcbTransformer();
      return $transformer->transformProducts($data);
    }

    return $data;
  }
}
