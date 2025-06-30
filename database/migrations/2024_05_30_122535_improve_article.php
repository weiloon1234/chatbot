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
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 64);
            $table->string('name_zh', 64);
            $table->unsignedTinyInteger('main_display_style')->nullable()->default(null);
            $table->unsignedTinyInteger('main_display_show_more')->nullable()->default(null);
            $table->unsignedTinyInteger('list_display_style')->nullable()->default(null);
            $table->unsignedTinyInteger('details_show_article_cover')->nullable()->default(null);
            $table->unsignedTinyInteger('details_show_article_datetime')->nullable()->default(null);
            $table->unsignedInteger('sorting')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('sorting')->nullable()->default(null)->after('id');
            $table->unsignedBigInteger('article_category_id')->nullable()->default(null)->after('sorting');

            $table->foreign('article_category_id')->references('id')->on('article_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign('articles_article_category_id_foreign');
            $table->dropColumn('sorting');
            $table->dropColumn('article_category_id');
        });
        Schema::dropIfExists('article_categories');
    }
};
