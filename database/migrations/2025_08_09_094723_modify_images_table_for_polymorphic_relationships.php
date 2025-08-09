<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('imageable_type')->after('id');
            $table->unsignedBigInteger('imageable_id')->after('imageable_type');
            
            // Add index for polymorphic relationship
            $table->index(['imageable_type', 'imageable_id']);
        });

        // Migrate existing data to new structure
        DB::table('images')->update([
            'imageable_type' => 'App\\Models\\Product',
            'imageable_id' => DB::raw('product_id')
        ]);

        Schema::table('images', function (Blueprint $table) {
            // Drop the old product_id column
            $table->dropColumn('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // Add back the product_id column
            $table->integer('product_id')->unsigned()->index()->after('id');
        });

        // Migrate data back (only for Product images)
        DB::table('images')
            ->where('imageable_type', 'App\\Models\\Product')
            ->update(['product_id' => DB::raw('imageable_id')]);

        Schema::table('images', function (Blueprint $table) {
            // Drop polymorphic columns
            $table->dropIndex(['imageable_type', 'imageable_id']);
            $table->dropColumn(['imageable_type', 'imageable_id']);
        });
    }
};
