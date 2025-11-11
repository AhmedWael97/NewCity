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
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('user_type', ['regular', 'shop_owner', 'admin'])->default('regular');
            $table->boolean('is_verified')->default(false);
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Add indexes for better performance
            $table->index(['user_type', 'is_verified']);
            $table->index(['city_id', 'user_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropIndex(['user_type', 'is_verified']);
            $table->dropIndex(['city_id', 'user_type']);
            $table->dropColumn([
                'city_id',
                'user_type', 
                'is_verified',
                'address',
                'date_of_birth'
            ]);
        });
    }
};
