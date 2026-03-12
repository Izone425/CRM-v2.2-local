<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('implementer_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('implementer_ticket_id');
            $table->string('sender_type');
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal_note')->default(false);
            $table->timestamps();

            $table->foreign('implementer_ticket_id')->references('id')->on('implementer_tickets')->onDelete('cascade');
            $table->index(['implementer_ticket_id', 'created_at'], 'imp_ticket_replies_ticket_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('implementer_ticket_replies');
    }
};
