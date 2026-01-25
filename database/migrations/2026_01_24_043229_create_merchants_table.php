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
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('name');
            $table->text('description', 5000)->nullable();
            $table->text('legal_address', 500)->nullable();
            $table->string('registration_number')->nullable();
            $table->string('website')->nullable();
            $table->string('tin')->nullable();
            $table->json('emails')->nullable();
            $table->json('phones')->nullable();
            $table->json('mobile_apps')->nullable();
            $table->json('social_media')->nullable();

            // sync credentials
            $table->string('auth_type');
            $table->text('auth_value')->nullable();
            $table->string('base_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('sync_type')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
