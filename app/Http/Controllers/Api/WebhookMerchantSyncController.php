<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncMerchantProductsJob;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookMerchantSyncController extends Controller
{
    /**
     * Handle inbound webhook: queue sync for the given merchant.
     */
    public function store(Request $request, Merchant $merchant): JsonResponse
    {
        if (! $merchant->is_active) {
            abort(404);
        }

        $secret = $merchant->webhook_secret;
        if ($secret !== null && $secret !== '') {
            $signature = $request->header('X-Webhook-Signature');
            $payload = $request->getContent();
            $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

            if (! is_string($signature) || ! hash_equals($expected, $signature)) {
                return response()->json(['message' => 'Invalid webhook signature'], 401);
            }
        }

        SyncMerchantProductsJob::dispatch($merchant);

        return response()->json(['message' => 'Sync queued'], 202);
    }
}
