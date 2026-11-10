<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kept as a no-op for databases that already recorded this migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The individual table migrations own their rollback behavior.
    }
};
