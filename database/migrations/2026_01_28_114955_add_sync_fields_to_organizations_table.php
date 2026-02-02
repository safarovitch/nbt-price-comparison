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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('last_sync_status')->default('idle')->after('sync_type');
            $table->text('last_sync_error')->nullable()->after('last_sync_status');
            $table->timestamp('sync_started_at')->nullable()->after('last_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['last_sync_status', 'last_sync_error', 'sync_started_at']);
        });
    }
};
