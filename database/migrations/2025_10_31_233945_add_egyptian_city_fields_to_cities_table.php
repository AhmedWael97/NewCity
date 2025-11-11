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
        Schema::table('cities', function (Blueprint $table) {
            // Geographic data
            $table->integer('elevation')->nullable()->after('longitude')->comment('Elevation in meters');
            $table->decimal('area', 8, 2)->nullable()->after('elevation')->comment('Area in km²');
            
            // Administrative data
            $table->string('governorate')->nullable()->after('area');
            $table->string('postal_code')->nullable()->after('governorate');
            $table->string('phone_code')->nullable()->after('postal_code');
            $table->string('timezone')->default('Africa/Cairo')->after('phone_code');
            $table->string('website')->nullable()->after('timezone');
            
            // Demographic data
            $table->bigInteger('population')->nullable()->after('website');
            $table->year('founded_year')->nullable()->after('population');
            
            // Development data
            $table->enum('status', ['planned', 'under_development', 'established', 'expanding'])->default('established')->after('founded_year');
            $table->enum('development_type', ['traditional', 'new_city', 'satellite', 'resort', 'industrial'])->default('traditional')->after('status');
            $table->json('key_features')->nullable()->after('development_type');
            $table->json('coordinates')->nullable()->after('key_features');
            
            // Add featured flag for homepage display
            $table->boolean('featured')->default(false)->after('coordinates');
            
            // Add index for geographic searches
            $table->index(['latitude', 'longitude'], 'idx_city_coordinates');
            $table->index(['governorate', 'is_active'], 'idx_city_governorate_active');
            $table->index(['featured', 'is_active'], 'idx_city_featured_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex('idx_city_featured_active');
            $table->dropIndex('idx_city_governorate_active');
            $table->dropIndex('idx_city_coordinates');
            
            $table->dropColumn([
                'latitude', 'longitude', 'elevation', 'area',
                'governorate', 'governorate_ar', 'postal_code', 'phone_code', 'timezone', 'website',
                'population', 'founded_year',
                'status', 'development_type', 'key_features', 'coordinates',
                'featured'
            ]);
        });
    }
};
