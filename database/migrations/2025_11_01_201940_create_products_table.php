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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable(); // السعر الأصلي قبل التخفيض
            $table->integer('discount_percentage')->default(0); // نسبة التخفيض
            $table->json('images')->nullable(); // صور المنتج
            $table->string('sku')->nullable(); // رقم المنتج
            $table->integer('stock_quantity')->default(0); // الكمية المتوفرة
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false); // منتج مميز
            $table->json('specifications')->nullable(); // المواصفات
            $table->string('unit')->nullable(); // وحدة القياس (كيلو، قطعة، متر...)
            $table->decimal('weight', 8, 2)->nullable(); // الوزن
            $table->string('brand')->nullable(); // الماركة
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
        Schema::dropIfExists('products');
    }
};
