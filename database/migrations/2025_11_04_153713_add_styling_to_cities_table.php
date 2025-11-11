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
            // City Styling Options
            $table->json('theme_config')->nullable()->after('image');
            $table->string('primary_color', 7)->default('#667eea')->after('theme_config');
            $table->string('secondary_color', 7)->default('#764ba2')->after('primary_color');
            $table->string('accent_color', 7)->default('#f093fb')->after('secondary_color');
            $table->string('background_style', 50)->default('gradient')->after('accent_color'); // gradient, solid, image
            $table->text('custom_css')->nullable()->after('background_style');
            $table->string('font_family', 100)->default('Cairo')->after('custom_css');
            $table->string('hero_image')->nullable()->after('font_family');
            $table->boolean('enable_custom_styling')->default(false)->after('hero_image');
            
            $table->index(['enable_custom_styling']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'theme_config',
                'primary_color',
                'secondary_color', 
                'accent_color',
                'background_style',
                'custom_css',
                'font_family',
                'hero_image',
                'enable_custom_styling'
            ]);
        });
    }
};
