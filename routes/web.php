<?php

use App\Helpers\LocaleHelper;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$supportedLocales = LocaleHelper::getSupportedLocales();
$localePattern = implode('|', $supportedLocales);

// Locale switcher route (must be outside locale group to avoid conflicts)
Route::middleware('web')->group(function () {
    Route::get('locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])
        ->name('locale.switch');

    Route::get('api/exchange-rates', [\App\Http\Controllers\ExchangeRateController::class, 'index'])
        ->name('api.exchange-rates');

    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::get('insurance', [\App\Http\Controllers\InsuranceController::class, 'index'])->name('insurance');

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Merchant product sync webhook (external callers, no locale)
    Route::post('api/webhooks/merchants/{merchant}/sync', [\App\Http\Controllers\Api\WebhookMerchantSyncController::class, 'store'])
        ->name('api.webhooks.merchants.sync')
        ->middleware('throttle:60,1');
});


// Routes with mandatory locale prefix
// The middleware will handle locale detection and validation
Route::middleware('web')->group(function () use ($localePattern) {
    Route::prefix('{locale}')
        ->where(['locale' => $localePattern])
        ->group(function () {
            Route::get('/', [HomeController::class, 'index'])->name('index.localized');

            Route::get('dashboard', function () {
                return Inertia::render('Dashboard');
            })->middleware(['auth', 'verified'])->name('dashboard.localized');

            Route::get('insurance', [\App\Http\Controllers\InsuranceController::class, 'index'])->name('insurance.localized');

            // Exchange rates API route (uses current locale from app context)
            Route::get('api/exchange-rates', [\App\Http\Controllers\ExchangeRateController::class, 'index'])
                ->name('api.exchange-rates.localized');

            require __DIR__ . '/settings.php';
        });
});
