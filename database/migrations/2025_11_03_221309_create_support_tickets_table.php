<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // AUTO-GENERATED: TICK-2025-001
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('shop_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject');
            $table->text('description');
            $table->enum('category', [
                'technical_issue',
                'shop_complaint', 
                'payment_issue',
                'account_problem',
                'feature_request',
                'bug_report',
                'content_issue',
                'other'
            ]);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'])->default('open');
            $table->json('attachments')->nullable(); // File paths
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('satisfaction_rating')->nullable(); // 1-5 stars
            $table->text('satisfaction_feedback')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['city_id', 'status']);
            $table->index(['assigned_admin_id', 'status']);
            $table->index(['status', 'priority', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
