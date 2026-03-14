<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('merged_into_ticket_id')->nullable()->after('is_overdue');
            $table->timestamp('merged_at')->nullable()->after('merged_into_ticket_id');
            $table->unsignedBigInteger('merged_by')->nullable()->after('merged_at');
        });
    }

    public function down(): void
    {
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->dropColumn(['merged_into_ticket_id', 'merged_at', 'merged_by']);
        });
    }
};
