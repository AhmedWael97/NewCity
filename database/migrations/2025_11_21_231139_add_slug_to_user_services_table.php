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
        Schema::table('user_services', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
        });

        // Generate slugs for existing records
        $services = \App\Models\UserService::whereNull('slug')->orWhere('slug', '')->get();
        foreach ($services as $service) {
            $slug = \Illuminate\Support\Str::slug($service->title);
            $count = 1;
            $originalSlug = $slug;
            
            // Ensure uniqueness
            while (\App\Models\UserService::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            
            $service->slug = $slug;
            $service->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_services', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
