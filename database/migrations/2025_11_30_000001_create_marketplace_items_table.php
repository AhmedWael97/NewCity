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
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            
            // Item Details
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('condition')->default('new'); // new, like_new, good, fair
            $table->json('images')->nullable();
            
            // Contact Information
            $table->string('contact_phone')->nullable();
            $table->string('contact_whatsapp')->nullable();
            
            // Status & Visibility
            $table->enum('status', ['active', 'sold', 'pending', 'rejected'])->default('active');
            $table->boolean('is_negotiable')->default(true);
            
            // View Management (Non-sponsored items have limited views)
            $table->integer('view_count')->default(0);
            $table->integer('max_views')->default(50); // Default limit for non-sponsored items
            $table->integer('contact_count')->default(0);
            
            // Sponsorship
            $table->boolean('is_sponsored')->default(false);
            $table->timestamp('sponsored_until')->nullable();
            $table->integer('sponsored_priority')->default(0); // Higher = shown first
            $table->integer('sponsored_views_boost')->default(0); // Extra views from sponsorship
            
            // Moderation
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['city_id', 'category_id', 'status'], 'idx_mp_city_cat_status');
            $table->index(['user_id', 'status'], 'idx_mp_user_status');
            $table->index(['is_sponsored', 'sponsored_priority', 'created_at'], 'idx_mp_sponsored');
            $table->index(['status', 'created_at'], 'idx_mp_status_created');
            $table->index(['view_count', 'max_views'], 'idx_mp_views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_items');
    }
};
