<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_configurations', function (Blueprint $table) {
            $table->id();

            // First Reply Deadline
            $table->string('first_reply_cutoff_time')->default('17:30');
            $table->boolean('first_reply_enabled')->default(true);

            // Follow-up Automation (pending_client)
            $table->integer('followup_reminder_days')->default(3);
            $table->integer('followup_auto_close_days')->default(2);
            $table->boolean('followup_enabled')->default(true);

            // Business Hours
            $table->string('business_start_time')->default('08:00');
            $table->string('business_end_time')->default('18:00');

            // General SLA
            $table->integer('resolution_sla_hours')->default(48);
            $table->integer('first_response_sla_hours')->default(24);

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // Insert default configuration row
        DB::table('sla_configurations')->insert([
            'first_reply_cutoff_time' => '17:30',
            'first_reply_enabled' => true,
            'followup_reminder_days' => 3,
            'followup_auto_close_days' => 2,
            'followup_enabled' => true,
            'business_start_time' => '08:00',
            'business_end_time' => '18:00',
            'resolution_sla_hours' => 48,
            'first_response_sla_hours' => 24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_configurations');
    }
};
