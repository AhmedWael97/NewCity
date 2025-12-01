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
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#3490dc');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->integer('threads_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            $table->index(['city_id', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_categories');
    }
};
