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
        Schema::create('shop_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending'])->default('pending');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('cancelled_at')->nullable();
            $table->string('payment_method')->nullable(); // credit_card, paypal, etc.
            $table->string('transaction_id')->nullable();
            $table->json('payment_details')->nullable(); // Store payment gateway details
            $table->text('cancellation_reason')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('next_billing_date')->nullable();
            $table->timestamps();
            
            $table->index(['shop_id', 'status']);
            $table->index(['ends_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_subscriptions');
    }
};
