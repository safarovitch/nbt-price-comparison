<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('organization_uuid')->constrained('organizations', 'uuid')->cascadeOnDelete();

            // Basic info (translatable)
            $table->json('name'); // {"ru": "...", "en": "...", "tj": "..."}
            $table->string('type'); // credit, deposit, card, transfer, insurance, mortgage, islamic, other
            $table->integer('currency_code'); // ISO 4217: 972=TJS, 840=USD, 978=EUR, 643=RUB

            // Interest rate range (for filtering/comparison)
            $table->float('interest_rate_min')->nullable();
            $table->float('interest_rate_max')->nullable();

            // Term range in months
            $table->integer('term_months_min')->nullable();
            $table->integer('term_months_max')->nullable();

            // Rate tiers for term/amount-based rates
            // Format: {"tiers": [{"term_months": 12, "min_amount": 1000, "interest_rate": 24.0}, ...]}
            $table->json('rate_tiers')->nullable();

            // Amount range
            $table->float('min_amount')->nullable();
            $table->float('max_amount')->nullable();

            // Structured fees
            // Format: {"origination": 1.0, "monthly": 0, "annual": 50, "early_repayment": 2.5, "service": 0}
            $table->json('fee_structure')->nullable();

            // Legacy single fee (for backward compatibility)
            $table->float('fees')->nullable();

            // Product purpose/category
            $table->string('purpose')->nullable(); // consumer, auto, mortgage, business, education, personal

            // Card-specific
            $table->integer('grace_period_days')->nullable();

            // Application info
            $table->boolean('online_application')->default(false);
            $table->string('approval_time')->nullable(); // instant, 1_day, 3_days, 1_week

            // Eligibility details
            $table->json('eligibility')->nullable(); // Translatable text
            $table->json('required_documents')->nullable(); // ["passport", "income_proof", ...]
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();

            // General info (translatable)
            $table->json('description')->nullable();

            // Catch-all for API-specific data
            $table->json('extra')->nullable();

            // Sync tracking
            $table->timestamp('remote_updated_at')->nullable();
            $table->timestamps();

            // Indexes for filtering
            $table->index(['organization_uuid', 'type']);
            $table->index(['type', 'currency_code']);
            $table->index(['interest_rate_min', 'interest_rate_max']);
            $table->index(['term_months_min', 'term_months_max']);
            $table->index(['min_amount', 'max_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
