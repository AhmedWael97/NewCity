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
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('endpoint')->index(); // /api/v1/shops/123
            $table->string('method', 10)->index(); // GET, POST, PUT, DELETE
            $table->json('request_data')->nullable(); // POST/PUT body
            $table->json('query_params')->nullable(); // URL query parameters
            $table->json('headers')->nullable(); // Selected headers (auth, content-type, etc.)
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type', 20)->nullable(); // mobile, tablet, desktop
            $table->integer('response_status')->nullable()->index(); // 200, 404, 500, etc.
            $table->decimal('response_time', 8, 3)->nullable(); // milliseconds
            $table->text('error_message')->nullable();
            $table->string('action_type', 50)->nullable()->index(); // list, view, create, update, delete, search
            $table->string('resource_type', 50)->nullable()->index(); // shop, city, product, etc.
            $table->string('resource_id')->nullable(); // ID of the resource being accessed
            $table->string('session_id')->nullable()->index();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['endpoint', 'method']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['resource_type', 'created_at']);
            $table->index(['response_status', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_requests');
    }
};
