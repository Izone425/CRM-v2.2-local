<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_data_migration_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('section');
            $table->string('item');
            $table->unsignedInteger('version')->default(1);
            $table->string('file_name');
            $table->string('file_path');
            $table->text('remark')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['lead_id', 'section', 'item', 'version'], 'cdmf_lead_section_item_version_unique');
            $table->index(['lead_id', 'section', 'item'], 'cdmf_lead_section_item_index');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_data_migration_files');
    }
};
