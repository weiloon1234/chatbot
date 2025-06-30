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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->engine = 'innodb';
            $table->id();
            $table->string('operation', 64);
            $table->unsignedBigInteger('admin_id')->nullable()->default(null);
            $table->string('model_class', 64)->index();
            $table->unsignedBigInteger('model_id');
            $table->text('description');
            $table->json('created_data')->nullable()->default(null);
            $table->json('old_data')->nullable()->default(null);
            $table->json('edited_data')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
