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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('role', 16)->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('admin_id')->nullable()->default(null);
            $table->unsignedTinyInteger('credit_type')->index();
            $table->decimal('credit_amount', 18, \App\Config::DECIMAL_POINT)->default(0);
            $table->decimal('admin_fees', 18, \App\Config::DECIMAL_POINT)->default(0);
            $table->decimal('conversion_rate', 18, \App\Config::DECIMAL_POINT)->default(0);
            $table->decimal('currency_amount', 18, \App\Config::DECIMAL_POINT)->default(0);
            $table->unsignedBigInteger('country_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('company_bank_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('bank_id')->nullable()->default(null)->index();
            $table->string('bank_account_holder_name', 64)->nullable()->default(null);
            $table->string('bank_account_number', 64)->nullable()->default(null);
            $table->unsignedInteger('deposit_method')->nullable()->default(null)->index();
            $table->unsignedTinyInteger('status')->default(0);
            $table->uuid('related_key')->nullable()->default(null);
            $table->json('params')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('company_bank_id')->references('id')->on('company_banks')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::create('deposit_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id')->nullable()->default(null)->index();
            $table->string('file_path', 255);
            $table->timestamps();
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_receipts');
        Schema::dropIfExists('deposits');
    }
};
