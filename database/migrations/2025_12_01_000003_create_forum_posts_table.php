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
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_approved')->default(true);
            $table->enum('status', ['active', 'pending', 'rejected', 'edited'])->default('active');
            $table->integer('helpful_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['forum_thread_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
