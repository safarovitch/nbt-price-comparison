<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncOrganizationProductsJob;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookOrganizationSyncController extends Controller
{
    /**
     * Handle inbound webhook: queue sync for the given Organization.
     */
    public function store(Request $request, Organization $organization): JsonResponse
    {
        if (! $organization->is_active) {
            abort(404);
        }

        $secret = $organization->webhook_secret;
        if ($secret !== null && $secret !== '') {
            $signature = $request->header('X-Webhook-Signature');
            $payload = $request->getContent();
            $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

            if (! is_string($signature) || ! hash_equals($expected, $signature)) {
                return response()->json(['message' => 'Invalid webhook signature'], 401);
            }
        }

        SyncOrganizationProductsJob::dispatch($organization);

        return response()->json(['message' => 'Sync queued'], 202);
    }
}
