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
        Schema::create('tawsela_rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            
            // Car details
            $table->string('car_model');
            $table->integer('car_year');
            $table->string('car_color');
            $table->integer('available_seats');
            
            // Location details
            $table->decimal('start_latitude', 10, 8);
            $table->decimal('start_longitude', 11, 8);
            $table->string('start_address');
            
            $table->decimal('destination_latitude', 10, 8);
            $table->decimal('destination_longitude', 11, 8);
            $table->string('destination_address');
            
            // Stop points (stored as JSON array)
            $table->json('stop_points')->nullable();
            
            // Pricing details
            $table->decimal('price', 10, 2);
            $table->enum('price_type', ['fixed', 'negotiable'])->default('fixed');
            $table->enum('price_unit', ['per_person', 'per_trip'])->default('per_person');
            
            // Ride details
            $table->dateTime('departure_time');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('active');
            
            // Statistics
            $table->integer('views_count')->default(0);
            $table->integer('requests_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('city_id');
            $table->index('status');
            $table->index('departure_time');
            $table->index(['start_latitude', 'start_longitude']);
            $table->index(['destination_latitude', 'destination_longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tawsela_rides');
    }
};
