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
        // Update existing records where is_bounce is null
        // Set is_bounce = true if pages_viewed = 1 (single page visit)
        // Set is_bounce = false if pages_viewed > 1 (multiple pages)
        DB::table('website_visits')
            ->whereNull('is_bounce')
            ->update([
                'is_bounce' => DB::raw('CASE WHEN pages_viewed <= 1 THEN 1 ELSE 0 END')
            ]);
        
        // Also update visits with duration < 5 seconds as bounces
        DB::table('website_visits')
            ->where('duration_seconds', '<', 5)
            ->where('pages_viewed', '<=', 1)
            ->update(['is_bounce' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data migration
    }
};
