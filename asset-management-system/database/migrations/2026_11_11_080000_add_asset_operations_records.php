<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_purchase_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('supplier');
            $table->string('purchase_order_reference')->nullable();
            $table->string('invoice_path')->nullable();
            $table->decimal('purchase_cost', 12, 2);
            $table->date('received_at');
            $table->timestamps();
            $table->index(['asset_id', 'received_at']);
        });

        Schema::create('asset_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider');
            $table->date('starts_at');
            $table->date('expires_at')->index();
            $table->string('document_path')->nullable();
            $table->json('reminder_days_sent')->nullable();
            $table->timestamps();
        });

        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_warranty_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('claim_reference')->nullable();
            $table->text('description');
            $table->date('submitted_at');
            $table->string('status')->default('submitted')->index();
            $table->text('resolution')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_verification_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verified_by_user_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('verified_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_verification_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_verification_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('observed_asset_code')->nullable();
            $table->string('finding')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason');
            $table->string('status')->default('pending')->index();
            $table->text('reviewer_remarks')->nullable();
            $table->dateTime('disposed_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'status']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->string('attachment_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn('attachment_path');
        });
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('asset_disposals');
        Schema::dropIfExists('asset_verification_items');
        Schema::dropIfExists('asset_verification_sessions');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('asset_warranties');
        Schema::dropIfExists('asset_purchase_records');
    }
};
