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
        Schema::table('device_tokens', function (Blueprint $table) {
            // Operating System details
            $table->string('os_version')->nullable()->after('device_type');
            $table->string('device_model')->nullable()->after('os_version');
            $table->string('device_manufacturer')->nullable()->after('device_model');
            
            // App and device identifiers
            $table->string('device_id')->nullable()->after('device_manufacturer'); // Unique device identifier
            $table->string('app_build_number')->nullable()->after('app_version');
            
            // Network and locale information
            $table->string('language')->default('ar')->after('app_build_number');
            $table->string('timezone')->nullable()->after('language');
            
            // Push notification settings
            $table->boolean('notifications_enabled')->default(true)->after('is_active');
            
            // Additional metadata
            $table->ipAddress('ip_address')->nullable()->after('timezone');
            $table->json('device_metadata')->nullable()->after('ip_address'); // For any additional data
            
            // Add indexes for better query performance
            $table->index('device_id');
            $table->index('os_version');
            $table->index('notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->dropIndex(['device_id']);
            $table->dropIndex(['os_version']);
            $table->dropIndex(['notifications_enabled']);
            
            $table->dropColumn([
                'os_version',
                'device_model',
                'device_manufacturer',
                'device_id',
                'app_build_number',
                'language',
                'timezone',
                'notifications_enabled',
                'ip_address',
                'device_metadata',
            ]);
        });
    }
};
