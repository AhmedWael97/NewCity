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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable(); // السعر الأصلي قبل التخفيض
            $table->integer('discount_percentage')->default(0); // نسبة التخفيض
            $table->json('images')->nullable(); // صور الخدمة
            $table->integer('duration_minutes')->nullable(); // مدة الخدمة بالدقائق
            $table->string('duration_text')->nullable(); // نص مدة الخدمة (ساعة، يومين...)
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false); // خدمة مميزة
            $table->boolean('requires_appointment')->default(false); // تحتاج موعد
            $table->json('requirements')->nullable(); // متطلبات الخدمة
            $table->json('benefits')->nullable(); // فوائد الخدمة
            $table->string('category')->nullable(); // فئة الخدمة
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps();
            
            $table->index(['shop_id', 'is_available']);
            $table->index(['shop_id', 'is_featured']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
