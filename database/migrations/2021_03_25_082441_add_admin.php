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
        Schema::create('admin_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->timestamps();
        });

        Schema::create('admin_group_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_group_id');
            $table->string('permission_tag');
            $table->timestamps();

            $table->foreign('admin_group_id')->references('id')->on('admin_groups')->onDelete('cascade');
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->index()->unique();
            $table->string('email', 255)->nullable()->default(null);
            $table->string('name', 255)->nullable()->default(null);
            $table->string('password');
            $table->unsignedBigInteger('admin_group_id')->nullable()->default(null);
            $table->unsignedInteger('type')->default(0);
            $table->string('lang', 12)->default(config('app.locale'));
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('admin_group_id')->references('id')->on('admin_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_group_permissions');
        Schema::dropIfExists('admin_groups');
    }
};
