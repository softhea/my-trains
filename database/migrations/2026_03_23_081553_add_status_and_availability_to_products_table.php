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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('user_id');
            // availability: 'standalone' = only alone, 'bundle_only' = only in bundles, 'both' = both ways
            $table->enum('availability', ['standalone', 'bundle_only', 'both'])->default('standalone')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'availability']);
        });
    }
};
