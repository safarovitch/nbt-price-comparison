<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * OTP length.
     */
    protected int $length = 6;

    /**
     * OTP TTL in seconds (5 minutes).
     */
    protected int $ttl = 300;

    /**
     * Rate limit: max attempts per phone per hour.
     */
    protected int $maxAttempts = 5;

    /**
     * Generate and send OTP to phone number.
     */
    public function send(string $phone): array
    {
        // Normalize phone number
        $phone = $this->normalizePhone($phone);

        // Check rate limit
        if ($this->isRateLimited($phone)) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please try again later.',
            ];
        }

        // Generate OTP
        $otp = $this->generateOtp();

        // Store in cache
        $this->storeOtp($phone, $otp);

        // Increment rate limit counter
        $this->incrementAttempts($phone);

        // Send SMS (stub - logs for now)
        $this->sendSms($phone, $otp);

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            // Return OTP in development for testing
            'otp_dev' => config('app.debug') ? $otp : null,
        ];
    }

    /**
     * Verify OTP for phone number.
     */
    public function verify(string $phone, string $otp): array
    {
        $phone = $this->normalizePhone($phone);
        $cacheKey = $this->getCacheKey($phone);

        $storedData = Cache::get($cacheKey);

        if (!$storedData) {
            return [
                'success' => false,
                'message' => 'OTP expired or not found. Please request a new one.',
            ];
        }

        // Check attempts
        if ($storedData['attempts'] >= 3) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please request a new OTP.',
            ];
        }

        // Verify OTP
        if ($storedData['otp'] !== $otp) {
            // Increment failed attempts
            $storedData['attempts']++;
            Cache::put($cacheKey, $storedData, $this->ttl);

            return [
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
                'attempts_remaining' => 3 - $storedData['attempts'],
            ];
        }

        // OTP verified - remove from cache
        Cache::forget($cacheKey);

        return [
            'success' => true,
            'message' => 'Phone verified successfully',
        ];
    }

    /**
     * Generate random OTP.
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), $this->length, '0', STR_PAD_LEFT);
    }

    /**
     * Store OTP in cache.
     */
    protected function storeOtp(string $phone, string $otp): void
    {
        $cacheKey = $this->getCacheKey($phone);

        Cache::put($cacheKey, [
            'otp' => $otp,
            'attempts' => 0,
            'created_at' => now()->timestamp,
        ], $this->ttl);
    }

    /**
     * Get cache key for phone.
     */
    protected function getCacheKey(string $phone): string
    {
        return 'otp:' . $phone;
    }

    /**
     * Get rate limit cache key.
     */
    protected function getRateLimitKey(string $phone): string
    {
        return 'otp_rate:' . $phone;
    }

    /**
     * Check if phone is rate limited.
     */
    protected function isRateLimited(string $phone): bool
    {
        $key = $this->getRateLimitKey($phone);
        return Cache::get($key, 0) >= $this->maxAttempts;
    }

    /**
     * Increment rate limit counter.
     */
    protected function incrementAttempts(string $phone): void
    {
        $key = $this->getRateLimitKey($phone);
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, 3600); // 1 hour
    }

    /**
     * Normalize phone number.
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters except +
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Send SMS (stub - integrate provider later).
     */
    protected function sendSms(string $phone, string $otp): void
    {
        // TODO: Integrate actual SMS provider here
        // For now, just log the OTP
        Log::info("OTP sent to {$phone}: {$otp}");

        // In production, this would call an SMS API like:
        // - Twilio
        // - MessageBird
        // - AWS SNS
        // - Local Tajik SMS provider
    }
}
