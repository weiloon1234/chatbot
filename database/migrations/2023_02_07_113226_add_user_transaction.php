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
        Schema::create('user_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->unsignedInteger('credit_type');
            $table->unsignedInteger('transaction_type');
            $table->decimal('amount', 18, \App\Config::DECIMAL_POINT)->default(0);
            $table->uuid('related_key')->nullable()->default(null);
            $table->json('params')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::create('admin_adjust_user_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->unsignedBigInteger('admin_id')->nullable()->default(null);
            $table->unsignedBigInteger('user_credit_transaction_id')->nullable()->default(null);
            $table->unsignedInteger('credit_type');
            $table->unsignedInteger('transaction_type');
            $table->decimal('amount', 18, \App\Config::DECIMAL_POINT)->default(0);
            $table->uuid('related_key')->nullable()->default(null);
            $table->json('params')->nullable()->default(null);
            $table->text('remark')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('user_credit_transaction_id')->references('id')->on('user_credit_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_adjust_user_credits');
        Schema::dropIfExists('user_credit_transactions');
    }
};
