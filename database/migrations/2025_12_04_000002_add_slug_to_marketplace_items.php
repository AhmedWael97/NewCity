<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MarketplaceItem;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->unique('slug');
        });

        // Generate slugs for existing items
        MarketplaceItem::withTrashed()->chunk(100, function ($items) {
            foreach ($items as $item) {
                $slug = Str::slug($item->title);
                $originalSlug = $slug;
                $counter = 1;

                // Ensure uniqueness
                while (MarketplaceItem::withTrashed()->where('slug', $slug)->where('id', '!=', $item->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $item->update(['slug' => $slug]);
            }
        });

        // Make slug non-nullable after data is populated
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
