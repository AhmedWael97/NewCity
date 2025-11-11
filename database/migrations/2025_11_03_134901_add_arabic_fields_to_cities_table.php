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
            // Add Arabic name field
            $table->string('name_ar')->nullable()->after('name')->comment('Arabic name of the city');
            
            // Add Arabic description field (if description exists)
            if (Schema::hasColumn('cities', 'description')) {
                $table->text('description_ar')->nullable()->after('description')->comment('Arabic description of the city');
            }
            
            // Add Arabic governorate field (if governorate exists)
            if (Schema::hasColumn('cities', 'governorate')) {
                $table->string('governorate_ar')->nullable()->after('governorate')->comment('Arabic governorate name');
            }
            
            // Add indexes for Arabic fields
            $table->index(['name_ar', 'is_active'], 'idx_cities_name_ar_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex('idx_cities_name_ar_active');
            $table->dropColumn(['name_ar', 'description_ar', 'governorate_ar']);
        });
    }
};
