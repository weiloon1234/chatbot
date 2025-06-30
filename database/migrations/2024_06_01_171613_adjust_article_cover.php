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
        Schema::table('article_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('main_display_show_title')->default(0)->after('main_display_show_more');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->longText('cover_en')->nullable()->default(null)->after('cover');
            $table->longText('cover_zh')->nullable()->default(null)->after('cover_en');
        });

        \DB::table('articles')->where('id', '>', 0)
            ->update([
                'cover_en' => \DB::raw('`cover`'),
                'cover_zh' => \DB::raw('`cover`'),
            ]);

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('cover');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->longText('cover')->nullable()->default(null)->after('cover_zh');
        });

        \DB::table('articles')->where('id', '>', 0)
            ->update([
                'cover' => \DB::raw('`cover_en`'),
            ]);

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('cover_en');
            $table->dropColumn('cover_zh');
        });

        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropColumn('main_display_show_title');
        });
    }
};
