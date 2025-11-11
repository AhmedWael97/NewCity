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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false); // For verified purchases
            $table->json('helpful_votes')->nullable(); // Track who found this helpful
            $table->timestamps();
            
            // Ensure one rating per user per shop
            $table->unique(['user_id', 'shop_id']);
            
            // Add indexes for performance
            $table->index(['shop_id', 'rating']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
