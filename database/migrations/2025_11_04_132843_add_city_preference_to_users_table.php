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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_city_id')->nullable()->after('email')->constrained('cities')->onDelete('set null');
            $table->string('preferred_city_name')->nullable()->after('preferred_city_id');
            $table->timestamp('city_updated_at')->nullable()->after('preferred_city_name');
            
            $table->index(['preferred_city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['preferred_city_id']);
            $table->dropColumn(['preferred_city_id', 'preferred_city_name', 'city_updated_at']);
        });
    }
};
