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
        Schema::create('website_visits', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->string('device_type', 50)->nullable(); // mobile, tablet, desktop
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('landing_page', 500)->nullable();
            $table->string('current_page', 500)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->boolean('is_unique_visit')->default(true); // First visit from this IP today
            $table->boolean('is_bounce')->default(false); // Single page visit
            $table->integer('pages_viewed')->default(1);
            $table->integer('duration_seconds')->default(0); // Session duration
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('ip_address');
            $table->index('user_id');
            $table->index('device_type');
            $table->index('created_at');
            $table->index(['ip_address', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_visits');
    }
};
