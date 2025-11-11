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
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('device_token')->unique();
            $table->string('device_type')->nullable(); // ios, android
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('device_type');
        });

        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->string('type')->default('general'); // general, alert, promo, update
            $table->string('target')->default('all'); // all, specific_users, city, category
            $table->json('target_ids')->nullable();
            $table->string('image_url')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('sent_count')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->string('status')->default('pending'); // pending, sending, sent, failed
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
            $table->index('type');
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('push_notification_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_token_id')->constrained()->onDelete('cascade');
            $table->string('status'); // sent, failed, opened
            $table->text('error_message')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();

            $table->index(['push_notification_id', 'status']);
            $table->index('device_token_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
