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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso2', 2)->unique()->index();
            $table->string('iso3', 3)->unique()->index();
            $table->string('name', 100)->unique()->index();
            $table->string('phone_code', 10)->nullable()->default(null);
            $table->string('currency_code', 3)->nullable()->default(null);
            $table->string('currency_symbol_prefix', 10)->nullable()->default(null);
            $table->string('currency_symbol_suffix', 10)->nullable()->default(null);
            $table->unsignedInteger('currency_decimal')->default(2);
            $table->decimal('rate_to_base', 18, \App\Config::DECIMAL_POINT)->nullable()->default(null);
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
