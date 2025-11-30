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
        Schema::create('marketplace_sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Sponsorship Package Details
            $table->string('package_type'); // basic, standard, premium
            $table->integer('duration_days'); // 7, 15, 30 days
            $table->decimal('price_paid', 10, 2);
            $table->integer('views_boost'); // Extra views granted
            $table->integer('priority_level'); // 1-10 for sorting
            
            // Timing
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            // Payment
            $table->string('payment_method')->nullable(); // cash, card, wallet
            $table->string('transaction_id')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            
            // Status
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->text('cancellation_reason')->nullable();
            
            // Performance Tracking
            $table->integer('views_gained')->default(0);
            $table->integer('contacts_gained')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['marketplace_item_id', 'status'], 'idx_mps_item_status');
            $table->index(['user_id', 'payment_status'], 'idx_mps_user_payment');
            $table->index(['status', 'ends_at'], 'idx_mps_status_ends');
            $table->index(['starts_at', 'ends_at'], 'idx_mps_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_sponsorships');
    }
};
