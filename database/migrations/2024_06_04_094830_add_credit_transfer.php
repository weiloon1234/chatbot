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
        Schema::create('user_credit_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user_id')->nullable()->default(null);
            $table->unsignedBigInteger('to_user_id')->nullable()->default(null);
            $table->unsignedInteger('from_credit_type');
            $table->unsignedInteger('to_credit_type');
            $table->decimal('conversion_rate', 18, \App\Config::DECIMAL_POINT);
            $table->decimal('from_amount', 18, \App\Config::DECIMAL_POINT);
            $table->decimal('to_amount', 18, \App\Config::DECIMAL_POINT);
            $table->uuid('related_key')->nullable()->default(null);
            $table->json('params')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credit_transfers');
    }
};
