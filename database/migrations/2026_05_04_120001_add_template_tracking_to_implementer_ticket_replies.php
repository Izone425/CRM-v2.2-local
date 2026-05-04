<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('implementer_ticket_replies', function (Blueprint $table) {
            $table->unsignedBigInteger('email_template_id')->nullable()->after('sender_id');
            $table->string('thread_label', 80)->nullable()->after('email_template_id');
        });

        // Per ultrathink: the mirror helper does
        //   ImplementerTicket::where('software_handover_id', X)
        //     ->orderBy('id', 'asc')
        //     ->lockForUpdate()
        // on every send. The current implementer_tickets table has no index
        // on software_handover_id (only customer_id+status and
        // implementer_user_id+status), so the lock would table-scan.
        // Index name "implementer_tickets_software_handover_id_index" = 47 chars,
        // well under MySQL's 64-char limit per CLAUDE.md gotcha.
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->index('software_handover_id');
        });
    }

    public function down(): void
    {
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->dropIndex(['software_handover_id']);
        });
        Schema::table('implementer_ticket_replies', function (Blueprint $table) {
            $table->dropColumn(['email_template_id', 'thread_label']);
        });
    }
};
