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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable()->default(null);
            $table->string('session_id')->nullable()->default(null);
            $table->unsignedBigInteger('contact_country_id')->nullable()->default(null)->index();
            $table->string('contact_number', 24)->nullable()->default(null);
            $table->string('full_contact_number', 30)->nullable()->default(null);
            $table->longText('message')->nullable()->default(null);
            $table->string('ip_address', 45)->nullable()->default(null);
            $table->timestamps();

            $table->foreign('admin_id')
                ->references('id')->on('admins')
                ->onDelete('set null');

            $table->foreign('contact_country_id')
                ->references('id')->on('countries')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
