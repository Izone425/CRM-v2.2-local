<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->timestamp('pending_client_since')->nullable()->after('first_responded_at');
            $table->timestamp('followup_sent_at')->nullable()->after('pending_client_since');
            $table->boolean('is_overdue')->default(false)->after('followup_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->dropColumn(['pending_client_since', 'followup_sent_at', 'is_overdue']);
        });
    }
};
