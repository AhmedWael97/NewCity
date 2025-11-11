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
        Schema::table('shops', function (Blueprint $table) {
            // Check if columns don't already exist before adding
            if (!Schema::hasColumn('shops', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('shops', 'featured_priority')) {
                $table->integer('featured_priority')->default(0);
            }
            if (!Schema::hasColumn('shops', 'featured_until')) {
                $table->timestamp('featured_until')->nullable();
            }
        });
        
        // Add index separately using raw SQL to avoid duplicate index errors
        try {
            DB::statement('CREATE INDEX idx_featured_shops ON shops(city_id, is_featured, featured_priority)');
        } catch (\Exception $e) {
            // Index might already exist, ignore error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index first
        try {
            DB::statement('DROP INDEX idx_featured_shops ON shops');
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }
        
        Schema::table('shops', function (Blueprint $table) {
            $columns = ['featured_until', 'featured_priority', 'is_featured'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('shops', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
