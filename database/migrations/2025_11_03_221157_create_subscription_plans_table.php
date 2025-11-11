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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Basic, Premium
            $table->string('slug')->unique(); // free, basic, premium
            $table->text('description');
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->integer('max_shops')->default(1); // Maximum shops allowed
            $table->integer('max_products_per_shop')->default(10);
            $table->integer('max_services_per_shop')->default(5);
            $table->integer('max_images_per_shop')->default(5);
            $table->boolean('analytics_access')->default(false);
            $table->boolean('priority_listing')->default(false);
            $table->boolean('verified_badge')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('social_media_integration')->default(false);
            $table->boolean('email_marketing')->default(false);
            $table->boolean('advanced_seo')->default(false);
            $table->boolean('customer_support')->default(false);
            $table->json('features')->nullable(); // Additional features as JSON
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
