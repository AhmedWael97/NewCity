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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 50); // banner, popup, sidebar, sponsored_listing, hero
            $table->string('placement', 100); // homepage, city_landing, shop_page, category_page, search_results
            
            // Targeting
            $table->string('scope', 20)->default('global'); // global, city_specific
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('target_categories')->nullable(); // category IDs to target
            $table->json('target_demographics')->nullable(); // age, gender, interests
            
            // Content
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->text('html_content')->nullable();
            $table->string('click_url');
            $table->string('button_text', 50)->default('اكتشف المزيد');
            
            // Pricing & Billing
            $table->enum('pricing_model', ['cpm', 'cpc', 'cpa', 'fixed'])->default('cpm'); // Cost per mille, click, action, fixed
            $table->decimal('price_amount', 10, 2); // Price in EGP
            $table->decimal('budget_total', 10, 2)->nullable(); // Total budget
            $table->decimal('budget_daily', 10, 2)->nullable(); // Daily budget limit
            $table->decimal('spent_amount', 10, 2)->default(0); // Amount spent so far
            
            // Scheduling
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->json('schedule_config')->nullable(); // Days of week, hours
            
            // Status & Performance
            $table->enum('status', ['draft', 'pending_review', 'active', 'paused', 'completed', 'rejected'])->default('draft');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('ctr', 5, 2)->default(0); // Click-through rate
            
            // Client Info
            $table->foreignId('advertiser_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('advertiser_name');
            $table->string('advertiser_email');
            $table->string('advertiser_phone')->nullable();
            $table->string('company_name')->nullable();
            
            // Admin Notes
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'start_date', 'end_date']);
            $table->index(['city_id', 'placement', 'status']);
            $table->index(['scope', 'type', 'status']);
            $table->index(['advertiser_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
