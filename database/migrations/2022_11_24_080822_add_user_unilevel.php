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
        Schema::create('user_unilevels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('user_unilevel')->index();
            $table->unsignedBigInteger('introducer_user_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('introducer_unilevel')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('introducer_user_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->default(null)->after('username')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_unilevels');
    }
};
