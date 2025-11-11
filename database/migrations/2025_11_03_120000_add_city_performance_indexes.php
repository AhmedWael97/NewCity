<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to optimize city-based queries.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Composite index for city selection modal (active cities with shop counts)
            $table->index(['is_active', 'featured'], 'idx_cities_active_featured');
            
            // Index for city search functionality
            $table->index(['name', 'is_active'], 'idx_cities_name_active');
            
            // Index for slug-based lookups (critical for routing)
            $table->index(['slug', 'is_active'], 'idx_cities_slug_active');
            
            // Index for location-based queries (nearby cities)
            if (Schema::hasColumn('cities', 'latitude') && Schema::hasColumn('cities', 'longitude')) {
                $table->index(['latitude', 'longitude', 'is_active'], 'idx_cities_location_active');
            }
        });

        Schema::table('shops', function (Blueprint $table) {
            // Composite index for city-specific shop queries (most common)
            $table->index(['city_id', 'is_active', 'is_verified'], 'idx_shops_city_active_verified');
            
            // Index for featured shops in cities
            $table->index(['city_id', 'is_featured', 'is_active'], 'idx_shops_city_featured_active');
            
            // Index for city and category filtering
            $table->index(['city_id', 'category_id', 'is_active'], 'idx_shops_city_category_active');
            
            // Index for search functionality
            $table->index(['name', 'is_active'], 'idx_shops_name_active');
            
            // Index for rating-based sorting
            $table->index(['rating', 'is_active', 'is_verified'], 'idx_shops_rating_active_verified');
            
            // Index for location-based queries
            $table->index(['latitude', 'longitude', 'is_active'], 'idx_shops_location_active');
            
            // Index for shop owner's shops
            $table->index(['user_id', 'is_active'], 'idx_shops_user_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Index for active categories with sorting
            $table->index(['is_active', 'sort_order'], 'idx_categories_active_sort');
            
            // Index for category search
            $table->index(['name', 'is_active'], 'idx_categories_name_active');
            
            // Index for parent-child relationships
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->index(['parent_id', 'is_active'], 'idx_categories_parent_active');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            // Index for shop's products
            $table->index(['shop_id', 'is_available'], 'idx_products_shop_available');
            
            // Index for featured products
            $table->index(['shop_id', 'is_featured', 'is_available'], 'idx_products_shop_featured_available');
            
            // Index for product search
            $table->index(['name', 'is_available'], 'idx_products_name_available');
            
            // Index for price-based filtering
            $table->index(['price', 'is_available'], 'idx_products_price_available');
        });

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                // Index for shop's services
                $table->index(['shop_id', 'is_available'], 'idx_services_shop_available');
                
                // Index for featured services
                $table->index(['shop_id', 'is_featured', 'is_available'], 'idx_services_shop_featured_available');
                
                // Index for service search
                $table->index(['name', 'is_available'], 'idx_services_name_available');
            });
        }

        if (Schema::hasTable('ratings')) {
            Schema::table('ratings', function (Blueprint $table) {
                // Index for shop ratings
                $table->index(['shop_id', 'created_at'], 'idx_ratings_shop_created');
                
                // Index for user ratings
                $table->index(['user_id', 'shop_id'], 'idx_ratings_user_shop');
                
                // Index for rating value filtering
                $table->index(['rating', 'shop_id'], 'idx_ratings_value_shop');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex('idx_cities_active_featured');
            $table->dropIndex('idx_cities_name_active');
            $table->dropIndex('idx_cities_slug_active');
            if (Schema::hasColumn('cities', 'latitude') && Schema::hasColumn('cities', 'longitude')) {
                $table->dropIndex('idx_cities_location_active');
            }
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropIndex('idx_shops_city_active_verified');
            $table->dropIndex('idx_shops_city_featured_active');
            $table->dropIndex('idx_shops_city_category_active');
            $table->dropIndex('idx_shops_name_active');
            $table->dropIndex('idx_shops_rating_active_verified');
            $table->dropIndex('idx_shops_location_active');
            $table->dropIndex('idx_shops_user_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_active_sort');
            $table->dropIndex('idx_categories_name_active');
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropIndex('idx_categories_parent_active');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_shop_available');
            $table->dropIndex('idx_products_shop_featured_available');
            $table->dropIndex('idx_products_name_available');
            $table->dropIndex('idx_products_price_available');
        });

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropIndex('idx_services_shop_available');
                $table->dropIndex('idx_services_shop_featured_available');
                $table->dropIndex('idx_services_name_available');
            });
        }

        if (Schema::hasTable('ratings')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropIndex('idx_ratings_shop_created');
                $table->dropIndex('idx_ratings_user_shop');
                $table->dropIndex('idx_ratings_value_shop');
            });
        }
    }
};