<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('phone', 13)->nullable()->unique();
            });
        }

        if (! Schema::hasTable('login_otp_challenges')) {
            Schema::create('login_otp_challenges', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('otp_hash');
                $table->timestamp('expires_at');
                $table->timestamp('sent_at');
                $table->unsignedSmallInteger('attempts')->default(0);
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'expires_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('login_otp_challenges');

        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['phone']);
                $table->dropColumn('phone');
            });
        }
    }
};
