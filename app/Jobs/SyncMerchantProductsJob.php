<?php

namespace App\Jobs;

use App\Models\Merchant;
use App\Services\MerchantProductApiClient;
use App\Services\ProductDataValidator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SyncMerchantProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Merchant $merchant
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MerchantProductApiClient $apiClient, ProductDataValidator $validator): void
    {
        $merchant = $this->merchant;

        Log::info('SyncMerchantProductsJob started', ['merchant_id' => $merchant->id]);

        try {
            $data = $apiClient->fetchProducts($merchant);
        } catch (\Throwable $e) {
            Log::error('SyncMerchantProductsJob: failed to fetch products', [
                'merchant_id' => $merchant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        try {
            $products = $validator->validateAndNormalize($data);
        } catch (ValidationException $e) {
            Log::warning('SyncMerchantProductsJob: validation failed', [
                'merchant_id' => $merchant->id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        $merchant->products()->delete();

        foreach ($products as $attrs) {
            $merchant->products()->create($attrs);
        }

        $merchant->last_synced_at = now();
        $merchant->save();

        Log::info('SyncMerchantProductsJob completed', [
            'merchant_id' => $merchant->id,
            'products_count' => count($products),
        ]);
    }
}
