<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('category')->nullable()->after('type');
        });

        // Seed the 3 existing hardcoded templates
        DB::table('email_templates')->insert([
            [
                'name' => 'First Response',
                'subject' => "Re: Your Support Request - We're On It!",
                'content' => "Dear [Client Name],\n\nThank you for reaching out to our support team. We have received your request and our team is currently reviewing the details.\n\nWe aim to provide you with a comprehensive response within [X hours/days] as per our SLA agreement. If you have any additional information that might help us resolve this faster, please feel free to share it.\n\nBest regards,\n[Implementer Name]\nSupport Team",
                'type' => 'implementer_thread',
                'category' => 'First Response',
                'created_by' => null,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'name' => 'Require More Time',
                'subject' => 'Update: Additional Time Required for Your Request',
                'content' => "Dear [Client Name],\n\nWe are writing to update you on the status of your support ticket [Ticket ID].\n\nAfter thorough review, our team requires additional time to properly address your request. This will ensure we provide you with the best possible solution. We now estimate completion by [New Date/Time].\n\nWe appreciate your patience and understanding. Should you have any questions, please don't hesitate to reach out.\n\nBest regards,\n[Implementer Name]\nSupport Team",
                'type' => 'implementer_thread',
                'category' => 'Follow-up',
                'created_by' => null,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'name' => 'R&D Escalation',
                'subject' => 'Escalation Notice: Your Ticket Requires R&D Investigation',
                'content' => "Dear [Client Name],\n\nThank you for your patience. Your support ticket [Ticket ID] has been escalated to our Research & Development team for further investigation.\n\nThis escalation allows our technical experts to conduct a deeper analysis and develop a comprehensive solution. We will keep you updated on the progress and provide an estimated resolution timeline shortly.\n\nWe appreciate your understanding as we work to resolve this matter.\n\nBest regards,\n[Implementer Name]\nSupport Team",
                'type' => 'implementer_thread',
                'category' => 'Escalation',
                'created_by' => null,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('email_templates')->where('type', 'implementer_thread')->delete();

        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
