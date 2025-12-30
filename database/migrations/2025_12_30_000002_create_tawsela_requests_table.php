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
        Schema::create('tawsela_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('tawsela_rides')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Pickup details
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);
            $table->string('pickup_address');
            
            // Destination details (can be different from ride's final destination)
            $table->decimal('dropoff_latitude', 10, 8)->nullable();
            $table->decimal('dropoff_longitude', 11, 8)->nullable();
            $table->string('dropoff_address')->nullable();
            
            $table->integer('passengers_count')->default(1);
            $table->decimal('offered_price', 10, 2)->nullable();
            $table->text('message')->nullable();
            
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            
            $table->timestamps();
            
            // Indexes
            $table->index('ride_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tawsela_requests');
    }
};
