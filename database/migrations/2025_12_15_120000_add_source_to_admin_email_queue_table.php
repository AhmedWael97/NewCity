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
        Schema::table('admin_email_queue', function (Blueprint $table) {
            $table->string('source')->default('web')->after('event_type'); // web, api, system
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_email_queue', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
