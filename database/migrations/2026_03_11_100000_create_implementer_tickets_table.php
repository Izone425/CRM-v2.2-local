<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('implementer_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('implementer_user_id')->nullable();
            $table->string('implementer_name')->nullable();
            $table->bigInteger('lead_id')->nullable();
            $table->bigInteger('software_handover_id')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open');
            $table->string('priority')->default('medium');
            $table->string('category')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->string('closed_by_type')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('implementer_user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['customer_id', 'status']);
            $table->index(['implementer_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('implementer_tickets');
    }
};
