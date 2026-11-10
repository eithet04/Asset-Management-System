<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('asset_returns')) {
            return;
        }

        Schema::create('asset_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_assignment_id')->unique()->constrained();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('returned_at');
            $table->string('returned_condition');
            $table->string('next_status')->default('available');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_returns');
    }
};
