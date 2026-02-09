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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->json('name'); // Translatable: {"ru": "...", "en": "...", "tj": "..."}
            $table->json('description')->nullable(); // Translatable
            $table->json('legal_address')->nullable(); // Translatable
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
            $table->string('last_sync_status')->default('idle');
            $table->text('last_sync_error')->nullable();
            $table->string('status')->default('active');
            $table->json('endpoints')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('sync_started_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
