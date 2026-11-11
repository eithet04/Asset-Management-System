<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('maintenance_records') && ! Schema::hasColumn('maintenance_records', 'deleted_at')) {
            Schema::table('maintenance_records', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('maintenance_records') && Schema::hasColumn('maintenance_records', 'deleted_at')) {
            Schema::table('maintenance_records', function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }
    }
};
