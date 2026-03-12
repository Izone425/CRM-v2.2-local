<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_data_migration_files', function (Blueprint $table) {
            $table->text('implementer_remark')->nullable()->after('remark');
        });
    }

    public function down(): void
    {
        Schema::table('customer_data_migration_files', function (Blueprint $table) {
            $table->dropColumn('implementer_remark');
        });
    }
};
