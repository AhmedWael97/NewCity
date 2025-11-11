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
        // Service Categories Table (only if doesn't exist)
        if (!Schema::hasTable('service_categories')) {
            Schema::create('service_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->text('description_ar')->nullable();
                $table->string('icon')->nullable();
                $table->string('color', 7)->default('#007bff');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index(['is_active', 'sort_order']);
            });
        }

        // Subscription Plans Table (only if doesn't exist)
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar');
                $table->text('description')->nullable();
                $table->text('description_ar')->nullable();
                $table->decimal('price', 10, 2);
                $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly']);
                $table->integer('max_services')->default(1);
                $table->integer('max_images')->default(5);
                $table->boolean('featured_listing')->default(false);
                $table->boolean('priority_support')->default(false);
                $table->boolean('analytics_access')->default(false);
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index(['is_active', 'sort_order']);
            });
        }

        // User Services Table
        if (!Schema::hasTable('user_services')) {
            Schema::create('user_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('city_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_category_id')->constrained()->onDelete('restrict');
                $table->string('title');
                $table->text('description');
                $table->decimal('price_from', 10, 2)->nullable();
                $table->decimal('price_to', 10, 2)->nullable();
                $table->enum('pricing_type', ['fixed', 'hourly', 'per_km', 'negotiable']);
                $table->string('currency', 3)->default('EGP');
                $table->json('images')->nullable();
                $table->string('phone')->nullable();
                $table->string('whatsapp')->nullable();
                $table->string('location')->nullable(); // Will store lat,lng as string
                $table->string('address')->nullable();
                $table->json('availability')->nullable(); // Working hours
                $table->json('service_areas')->nullable(); // Areas they serve
                $table->text('requirements')->nullable(); // Special requirements
                $table->boolean('is_active')->default(true);
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->timestamp('featured_until')->nullable();
                $table->decimal('rating', 3, 2)->default(0);
                $table->integer('total_reviews')->default(0);
                $table->integer('total_views')->default(0);
                $table->integer('total_contacts')->default(0);
                $table->timestamp('last_active')->nullable();
                $table->timestamps();
                
                $table->index(['city_id', 'service_category_id', 'is_active']);
                $table->index(['is_featured', 'featured_until']);
                $table->index(['rating', 'total_reviews']);
            });
        }

        // User Subscriptions Table
        if (!Schema::hasTable('user_subscriptions')) {
            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('subscription_plan_id')->constrained()->onDelete('restrict');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->enum('status', ['active', 'expired', 'cancelled', 'pending']);
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('EGP');
                $table->string('payment_method')->nullable();
                $table->string('transaction_id')->nullable();
                $table->json('plan_features')->nullable(); // Snapshot of plan features
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index(['ends_at', 'status']);
            });
        }

        // Service Reviews Table
        if (!Schema::hasTable('service_reviews')) {
            Schema::create('service_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_service_id')->constrained()->onDelete('cascade');
                $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
                $table->integer('rating'); // 1-5 stars
                $table->text('comment')->nullable();
                $table->json('images')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_approved')->default(true);
                $table->timestamp('service_date')->nullable();
                $table->timestamps();
                
                $table->unique(['user_service_id', 'reviewer_id']);
                $table->index(['user_service_id', 'is_approved']);
                $table->index(['rating', 'created_at']);
            });
        }

        // Service Analytics Table
        if (!Schema::hasTable('service_analytics')) {
            Schema::create('service_analytics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_service_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->integer('views')->default(0);
                $table->integer('contacts')->default(0);
                $table->integer('phone_clicks')->default(0);
                $table->integer('whatsapp_clicks')->default(0);
                $table->integer('unique_visitors')->default(0);
                $table->json('referrer_sources')->nullable();
                $table->json('visitor_locations')->nullable();
                $table->timestamps();
                
                $table->unique(['user_service_id', 'date']);
                $table->index(['date', 'views']);
            });
        }

        // Service Bookmarks Table
        if (!Schema::hasTable('service_bookmarks')) {
            Schema::create('service_bookmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_service_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['user_id', 'user_service_id']);
            });
        }

        // Service Reports Table
        if (!Schema::hasTable('service_reports')) {
            Schema::create('service_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_service_id')->constrained()->onDelete('cascade');
                $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
                $table->enum('reason', ['inappropriate', 'spam', 'fraud', 'other']);
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed']);
                $table->text('admin_notes')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                
                $table->index(['status', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reports');
        Schema::dropIfExists('service_bookmarks');
        Schema::dropIfExists('service_analytics');
        Schema::dropIfExists('service_reviews');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('user_services');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('service_categories');
    }
};
