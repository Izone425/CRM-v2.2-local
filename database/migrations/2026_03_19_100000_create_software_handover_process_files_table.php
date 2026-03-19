<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_handover_process_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->index('shpf_lead_id_index');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->string('file_name');
            $table->string('file_path');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique(['lead_id', 'version'], 'shpf_lead_version_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_handover_process_files');
    }
};
