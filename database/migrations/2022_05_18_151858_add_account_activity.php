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
        Schema::create('account_login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model', 64)->index();
            $table->unsignedBigInteger('model_id')->index();
            $table->string('login_type', 64)->index();
            $table->string('ip_address', 64);
            $table->timestamps();
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->default(null)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_login_logs');
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
