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
        Schema::create('tawsela_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('tawsela_rides')->onDelete('cascade');
            $table->foreignId('request_id')->nullable()->constrained('tawsela_requests')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            
            $table->text('message');
            $table->boolean('is_read')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('ride_id');
            $table->index('request_id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tawsela_messages');
    }
};
