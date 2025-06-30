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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 64)->index()->unique()->after('id');
            $table->string('password2')->after('password');

            $table->unsignedBigInteger('country_id')->nullable()->default(null)->after('password2');
            $table->unsignedBigInteger('contact_country_id')->nullable()->default(null)->after('country_id');
            $table->string('contact_number', 24)->nullable()->default(null)->after('contact_country_id');
            $table->string('full_contact_number', 24)->nullable()->default(null)->after('contact_number');

            $table->unsignedBigInteger('introducer_user_id')->nullable()->default(null)->after('full_contact_number');
            $table->unsignedBigInteger('unilevel')->nullable()->default(null)->after('introducer_user_id');
            $table->string('lang', 12)->default(config('app.locale'))->after('introducer_user_id');
            $table->text('avatar')->nullable()->default(null)->after('lang');

            $table->decimal('credit_1', 18, \App\Config::DECIMAL_POINT)->default(0)->after('avatar');
            $table->decimal('credit_2', 18, \App\Config::DECIMAL_POINT)->default(0)->after('credit_1');
            $table->decimal('credit_3', 18, \App\Config::DECIMAL_POINT)->default(0)->after('credit_2');
            $table->decimal('credit_4', 18, \App\Config::DECIMAL_POINT)->default(0)->after('credit_3');
            $table->decimal('credit_5', 18, \App\Config::DECIMAL_POINT)->default(0)->after('credit_4');

            $table->unsignedBigInteger('bank_id')->nullable()->default(null)->after('credit_5');
            $table->string('bank_account_name', 255)->nullable()->default(null)->after('bank_id');
            $table->string('bank_account_number', 255)->nullable()->default(null)->after('bank_account_name');
            $table->string('national_id', 255)->nullable()->default(null)->after('bank_account_number');
            $table->unsignedTinyInteger('first_login')->default(0)->after('national_id');
            $table->timestamp('ban_until')->nullable()->default(null)->after('first_login');
            $table->dateTime('new_login_at')->nullable()->default(null)->after('ban_until');
            $table->dateTime('last_login_at')->nullable()->default(null)->after('new_login_at');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('introducer_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('contact_country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_country_id_foreign');
            $table->dropForeign('users_introducer_user_id_foreign');
            $table->dropForeign('users_bank_id_foreign');

            $table->dropColumn('username');
            $table->dropColumn('password2');

            $table->dropColumn('country_id');
            $table->dropColumn('contact_country_id');
            $table->dropColumn('contact_number');
            $table->dropColumn('full_contact_number');

            $table->dropColumn('introducer_user_id');
            $table->dropColumn('unilevel');
            $table->dropColumn('lang');
            $table->dropColumn('avatar');

            $table->dropColumn('credit_1');
            $table->dropColumn('credit_2');
            $table->dropColumn('credit_3');
            $table->dropColumn('credit_4');
            $table->dropColumn('credit_5');

            $table->dropColumn('bank_id');
            $table->dropColumn('bank_account_name');
            $table->dropColumn('bank_account_number');
            $table->dropColumn('national_id');
            $table->dropColumn('first_login');
            $table->dropColumn('ban_until');
            $table->dropColumn('new_login_at');
            $table->dropColumn('last_login_at');
        });
    }
};
