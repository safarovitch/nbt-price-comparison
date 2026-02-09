<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Product;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone number.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:9|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->send($request->input('phone'));

        return response()->json($result, $result['success'] ? 200 : 429);
    }

    /**
     * Verify OTP for phone number.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:9|max:20',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verify(
            $request->input('phone'),
            $request->input('otp')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Store a new application.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_uuid' => 'required|uuid|exists:products,uuid',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|min:9|max:20',
            'phone_verified' => 'required|boolean|accepted',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_term' => 'nullable|integer|min:1',
            'extra' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create application
        $application = Application::create([
            'product_uuid' => $request->input('product_uuid'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'phone_verified_at' => now(), // Already verified via OTP
            'status' => 'verified',
            'loan_amount' => $request->input('loan_amount'),
            'loan_term' => $request->input('loan_term'),
            'extra' => $request->input('extra'),
        ]);

        // Load product for response
        $application->load('product.organization');

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'application' => [
                'uuid' => $application->uuid,
                'status' => $application->status,
                'product_name' => $application->product ? $application->product->name : null,
            ],
        ], 201);
    }

    /**
     * Get product fields for application form.
     */
    public function getProductFields(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'product' => [
                'uuid' => $product->uuid,
                'name' => $product->name,
                'type' => $product->type,
                'min_amount' => $product->min_amount,
                'max_amount' => $product->max_amount,
                'term_months_min' => $product->term_months_min,
                'term_months_max' => $product->term_months_max,
                'purpose' => $product->purpose,
            ],
            'organization' => [
                'uuid' => $product->organization->uuid,
                'name' => $product->organization->name,
            ],
        ]);
    }
    // boshluk
}
