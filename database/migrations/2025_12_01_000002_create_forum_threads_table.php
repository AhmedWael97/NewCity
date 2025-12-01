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
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->enum('status', ['active', 'pending', 'rejected', 'closed'])->default('active');
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->foreignId('last_post_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['forum_category_id', 'status', 'is_pinned']);
            $table->index(['user_id', 'status']);
            $table->index(['city_id', 'status']);
            $table->index('slug');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_threads');
    }
};
