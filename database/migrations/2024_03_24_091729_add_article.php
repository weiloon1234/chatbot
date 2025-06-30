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
        Schema::create('articles', function (Blueprint $table) {
            $table->engine = 'innodb';
            $table->id();

            $table->string('subject_en', 48)->nullable()->default(null);
            $table->string('subject_zh', 48)->nullable()->default(null);

            $table->string('description_en', 255)->nullable()->default(null);
            $table->string('description_zh', 255)->nullable()->default(null);

            $table->json('content_en')->nullable()->default(null);
            $table->json('content_zh')->nullable()->default(null);

            $table->text('cover')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
