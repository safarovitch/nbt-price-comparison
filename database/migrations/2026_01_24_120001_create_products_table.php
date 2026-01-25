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
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->integer('currency_code');
            $table->float('interest_rate')->nullable();
            $table->float('fees')->nullable();
            $table->integer('term_months')->nullable();
            $table->float('min_amount')->nullable();
            $table->float('max_amount')->nullable();
            $table->text('eligibility', 1000)->nullable();
            $table->text('description', 5000)->nullable();
            $table->json('extra')->nullable();
            $table->timestamp('remote_updated_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'type', 'currency', 'name']);
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
