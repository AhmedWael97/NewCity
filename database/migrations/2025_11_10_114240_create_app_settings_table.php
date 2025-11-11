<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('string'); // string, boolean, json, integer
            $table->timestamps();
        });

        // Insert default settings
        DB::table('app_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'City App',
                'description' => 'Mobile application name',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_icon_url',
                'value' => null,
                'description' => 'URL to app icon image',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_logo_url',
                'value' => null,
                'description' => 'URL to app logo image',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'description' => 'Enable maintenance mode',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'App is under maintenance. Please check back later.',
                'description' => 'Maintenance mode message',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'force_update',
                'value' => 'false',
                'description' => 'Force users to update the app',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'min_app_version',
                'value' => '1.0.0',
                'description' => 'Minimum required app version',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'latest_app_version',
                'value' => '1.0.0',
                'description' => 'Latest available app version',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'update_message',
                'value' => 'A new version is available. Please update to continue.',
                'description' => 'Update required message',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'android_app_url',
                'value' => null,
                'description' => 'Google Play Store URL',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ios_app_url',
                'value' => null,
                'description' => 'Apple App Store URL',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'api_status',
                'value' => 'active',
                'description' => 'API status: active, limited, disabled',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'firebase_enabled',
                'value' => 'false',
                'description' => 'Enable Firebase notifications',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
