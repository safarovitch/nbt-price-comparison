<?php

use App\Helpers\LocaleHelper;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$supportedLocales = LocaleHelper::getSupportedLocales();
$localePattern = implode('|', $supportedLocales);

// Locale switcher route (must be outside locale group to avoid conflicts)
Route::middleware('web')->group(function () {
    Route::get('locale/{new_locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])
        ->name('locale.switch');

    Route::get('api/exchange-rates', [\App\Http\Controllers\ExchangeRateController::class, 'index'])
        ->name('api.exchange-rates');

    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::get('insurance', [\App\Http\Controllers\InsuranceController::class, 'index'])->name('insurance');
    Route::get('credits', [\App\Http\Controllers\CreditsController::class, 'index'])->name('credits');
    Route::get('credits/{product}', [\App\Http\Controllers\CreditsController::class, 'show'])->name('credits.show');

    // Calculators
    Route::get('loan-calculator', [\App\Http\Controllers\CalculatorController::class, 'loan'])->name('calculator.loan');
    Route::get('credit-calculator', [\App\Http\Controllers\CalculatorController::class, 'credit'])->name('calculator.credit');
    Route::get('deposit-calculator', [\App\Http\Controllers\CalculatorController::class, 'deposit'])->name('calculator.deposit');

    // Ratings
    Route::get('ratings', [\App\Http\Controllers\RatingsController::class, 'index'])->name('ratings');
    Route::post('ratings', [\App\Http\Controllers\RatingsController::class, 'store'])->name('ratings.store');

    // ATMs & Branches
    Route::get('atms', [\App\Http\Controllers\DeviceLocationController::class, 'index'])->name('atms');

    // Organization search for dropdown
    Route::get('api/ratings/organizations', [\App\Http\Controllers\RatingsController::class, 'searchOrganizations'])->name('api.ratings.organizations');

    // News Routes
    Route::get('news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news');
    Route::get('news/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

    // Application API routes
    Route::post('api/otp/send', [\App\Http\Controllers\ApplicationController::class, 'sendOtp'])->name('api.otp.send');
    Route::post('api/otp/verify', [\App\Http\Controllers\ApplicationController::class, 'verifyOtp'])->name('api.otp.verify');
    Route::post('api/applications', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('api.applications.store');

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Admin routes
    // require __DIR__ . '/admin.php';

    // Organization product sync webhook (external callers, no locale)
    Route::post('api/webhooks/organizations/{organization}', [\App\Http\Controllers\Api\WebhookOrganizationSyncController::class, 'store'])
        ->name('api.webhooks.organizations.sync')
        ->middleware('throttle:60,1');

    Route::get('login', fn() => redirect('/' . app()->getLocale() . '/login'));
    Route::get('register', fn() => redirect('/' . app()->getLocale() . '/register'));

    // Admin routes (no locale prefix needed)
    require __DIR__ . '/admin.php';
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
            Route::get('credits', [\App\Http\Controllers\CreditsController::class, 'index'])->name('credits.localized');
            Route::get('credits/{product}', [\App\Http\Controllers\CreditsController::class, 'show'])->name('credits.show.localized');

            // Calculators
            Route::get('loan-calculator', [\App\Http\Controllers\CalculatorController::class, 'loan'])->name('calculator.loan.localized');
            Route::get('credit-calculator', [\App\Http\Controllers\CalculatorController::class, 'credit'])->name('calculator.credit.localized');
            Route::get('deposit-calculator', [\App\Http\Controllers\CalculatorController::class, 'deposit'])->name('calculator.deposit.localized');

            // Ratings
            Route::get('ratings', [\App\Http\Controllers\RatingsController::class, 'index'])->name('ratings.localized');

            // Exchange
            Route::get('exchange', [\App\Http\Controllers\ExchangeController::class, 'index'])->name('exchange.localized');

            // ATMs & Branches
            Route::get('atms', [\App\Http\Controllers\DeviceLocationController::class, 'index'])->name('atms.localized');

            // News
            Route::get('news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.localized');
            Route::get('news/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show.localized');

            require base_path('vendor/laravel/fortify/routes/routes.php');

            // Exchange rates API route (uses current locale from app context)
            Route::get('api/exchange-rates', [\App\Http\Controllers\ExchangeRateController::class, 'index'])
                ->name('api.exchange-rates.localized');

            require __DIR__ . '/settings.php';
        });
});
