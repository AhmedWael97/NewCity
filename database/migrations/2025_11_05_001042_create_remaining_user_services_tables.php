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
        // User Services table
        if (!Schema::hasTable('user_services')) {
            Schema::create('user_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_category_id')->constrained()->onDelete('cascade');
                $table->foreignId('city_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->enum('pricing_type', ['fixed', 'hourly', 'distance', 'negotiable'])->default('fixed');
                $table->decimal('base_price', 10, 2)->nullable();
                $table->decimal('hourly_rate', 10, 2)->nullable();
                $table->decimal('distance_rate', 10, 2)->nullable();
                $table->decimal('minimum_charge', 10, 2)->nullable();
                $table->json('availability_schedule')->nullable();
                $table->string('contact_phone');
                $table->string('contact_whatsapp')->nullable();
                $table->json('service_area')->nullable();
                $table->json('requirements')->nullable();
                $table->json('images')->nullable();
                $table->json('vehicle_info')->nullable();
                $table->integer('experience_years')->nullable();
                $table->json('certifications')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_verified')->default(false);
                $table->timestamp('featured_until')->nullable();
                $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('set null');
                $table->timestamp('subscription_expires_at')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['user_id', 'is_active']);
                $table->index(['service_category_id', 'city_id']);
                $table->index(['status', 'is_active']);
            });
        }

        // Service Analytics table
        if (!Schema::hasTable('service_analytics')) {
            Schema::create('service_analytics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_service_id')->constrained()->onDelete('cascade');
                $table->string('metric_type'); // view, contact, booking_request, etc.
                $table->string('metric_value')->nullable(); // phone, whatsapp, booking, etc.
                $table->integer('value')->default(1);
                $table->date('date');
                $table->integer('hour')->unsigned();
                $table->string('user_agent')->nullable();
                $table->string('ip_address')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['user_service_id', 'metric_type', 'date']);
                $table->index(['date', 'hour']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_analytics');
        Schema::dropIfExists('user_services');
    }
};
