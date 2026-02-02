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
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->uuid('product_uuid');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->timestamp('phone_verified_at')->nullable();
            $table->enum('status', ['pending', 'verified', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->integer('loan_term')->nullable(); // in months
            $table->json('extra')->nullable(); // for product-specific fields
            $table->timestamps();

            $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('cascade');
            $table->index(['phone', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
