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
        Schema::table('pages', function (Blueprint $table) {
            $table->json('content_en')->nullable()->default(null)->after('content');
            $table->json('content_zh')->nullable()->default(null)->after('content_en');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('content')->nullable()->default(null)->after('content_zh');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('content_en');
            $table->dropColumn('content_zh');
        });
    }
};
