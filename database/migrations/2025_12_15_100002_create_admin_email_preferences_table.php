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
        Schema::create('admin_email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('shop_suggestion')->default(true);
            $table->boolean('city_suggestion')->default(true);
            $table->boolean('shop_rate')->default(true);
            $table->boolean('service_rate')->default(true);
            $table->boolean('new_service')->default(true);
            $table->boolean('new_marketplace')->default(true);
            $table->boolean('new_user')->default(true);
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_preferences');
    }
};
