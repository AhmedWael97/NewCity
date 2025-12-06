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
            // Contact Information
            if (!Schema::hasColumn('cities', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'contact_address')) {
                $table->text('contact_address')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'contact_whatsapp')) {
                $table->string('contact_whatsapp')->nullable()->after('name_ar');
            }
            
            // SEO Settings
            if (!Schema::hasColumn('cities', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'meta_title_ar')) {
                $table->string('meta_title_ar')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'meta_description_ar')) {
                $table->text('meta_description_ar')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'meta_keywords_ar')) {
                $table->string('meta_keywords_ar')->nullable()->after('name_ar');
            }
            
            // Branding
            if (!Schema::hasColumn('cities', 'logo')) {
                $table->string('logo')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'favicon')) {
                $table->string('favicon')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'og_image')) {
                $table->string('og_image')->nullable()->after('name_ar');
            }
            
            // Social Media
            if (!Schema::hasColumn('cities', 'facebook_url')) {
                $table->string('facebook_url')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'twitter_url')) {
                $table->string('twitter_url')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'instagram_url')) {
                $table->string('instagram_url')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('name_ar');
            }
            
            // Analytics
            if (!Schema::hasColumn('cities', 'google_analytics_id')) {
                $table->string('google_analytics_id')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('cities', 'facebook_pixel_id')) {
                $table->string('facebook_pixel_id')->nullable()->after('name_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'contact_phone',
                'contact_email',
                'contact_address',
                'contact_whatsapp',
                'meta_title',
                'meta_title_ar',
                'meta_description',
                'meta_description_ar',
                'meta_keywords',
                'meta_keywords_ar',
                'logo',
                'favicon',
                'og_image',
                'primary_color',
                'secondary_color',
                'accent_color',
                'facebook_url',
                'twitter_url',
                'instagram_url',
                'youtube_url',
                'google_analytics_id',
                'facebook_pixel_id',
            ]);
        });
    }
};
