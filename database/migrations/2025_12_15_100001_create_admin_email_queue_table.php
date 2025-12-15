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
        Schema::create('admin_email_queue', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // shop_suggestion, city_suggestion, shop_rate, service_rate, new_service, new_marketplace, new_user
            $table->string('subject');
            $table->text('body');
            $table->json('recipients'); // Array of admin emails
            $table->json('event_data')->nullable(); // Related data (shop_id, user_id, etc.)
            $table->enum('status', ['pending', 'processing', 'sent', 'failed'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_queue');
    }
};
