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
            if (!Schema::hasColumn('cities', 'theme_config')) {
                $table->json('theme_config')->nullable();
            }
            if (!Schema::hasColumn('cities', 'featured_shops_count')) {
                $table->integer('featured_shops_count')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'theme_config')) {
                $table->dropColumn('theme_config');
            }
            if (Schema::hasColumn('cities', 'featured_shops_count')) {
                $table->dropColumn('featured_shops_count');
            }
        });
    }
};
