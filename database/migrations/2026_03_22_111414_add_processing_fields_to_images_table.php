<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->boolean('is_processed')->default(false)->after('url');
            $table->string('original_url')->nullable()->after('is_processed');
            $table->unsignedInteger('width')->nullable()->after('original_url');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->unsignedInteger('file_size')->nullable()->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn(['is_processed', 'original_url', 'width', 'height', 'file_size']);
        });
    }
};
