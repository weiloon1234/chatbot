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
        Schema::create('bot_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('channel', 30); // whatsapp, telegram, instagram, etc.
            $table->enum('direction', ['incoming', 'outgoing']);

            $table->string('from_id', 100)->nullable()->index(); // sender id
            $table->string('from_name', 100)->nullable();

            $table->string('to_id', 100)->nullable()->index(); // recipient id
            $table->string('to_name', 100)->nullable();

            $table->json('message'); // full structured message (e.g. messages: [{type, ...}], send_sequence)
            $table->json('raw_payload')->nullable();

            $table->string('type', 20)->nullable()->default(null); // text, image, video, document, poll, etc.

            $table->enum('status', ['PENDING', 'SENT', 'FAILED', 'RECEIVED', 'READ'])->default('PENDING');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->string('external_message_id', 100)->nullable()->index(); // e.g. WhatsApp ID
            $table->json('response')->nullable(); // response payload from API if any

            $table->string('reference_number', 100)->nullable()->index(); // user-defined ref
            $table->ipAddress()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_messages');
    }
};
