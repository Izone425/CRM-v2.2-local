<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_data_migration_files', function (Blueprint $table) {
            $table->string('uploaded_by_type', 20)->default('customer')->after('status');
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable()->after('uploaded_by_type');
            $table->unsignedBigInteger('source_ticket_reply_id')->nullable()->after('uploaded_by_user_id');
            $table->string('source_attachment_path', 500)->nullable()->after('source_ticket_reply_id');

            $table->index(['lead_id', 'uploaded_by_type'], 'cdmf_lead_uploaded_by_idx');
        });
    }

    public function down(): void
    {
        Schema::table('customer_data_migration_files', function (Blueprint $table) {
            $table->dropIndex('cdmf_lead_uploaded_by_idx');
            $table->dropColumn([
                'uploaded_by_type',
                'uploaded_by_user_id',
                'source_ticket_reply_id',
                'source_attachment_path',
            ]);
        });
    }
};
