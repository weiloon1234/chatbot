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
            $table->longText('content_en')->nullable()->default(null)->change();
            $table->longText('content_zh')->nullable()->after('content_en')->change();
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->longText('content_en')->nullable()->default(null)->change();
            $table->longText('content_zh')->nullable()->after('content_en')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('content_en')->nullable()->default(null)->change();
            $table->json('content_zh')->nullable()->after('content_en')->change();
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->json('content_en')->nullable()->default(null)->change();
            $table->json('content_zh')->nullable()->after('content_en')->change();
        });
    }
};
