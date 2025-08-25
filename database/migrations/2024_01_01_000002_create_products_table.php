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
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('base_price', 15, 2)->comment('Base price from Digiflazz');
            $table->decimal('selling_price', 15, 2)->comment('Price after profit margin');
            $table->decimal('profit_percentage', 5, 2)->default(0)->comment('Profit margin percentage');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_flash_sale')->default(false);
            $table->decimal('flash_sale_price', 15, 2)->nullable();
            $table->timestamp('flash_sale_start')->nullable();
            $table->timestamp('flash_sale_end')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('digiflazz_code')->nullable()->comment('Product code from Digiflazz');
            $table->boolean('requires_game_id')->default(true);
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('slug');
            $table->index('sku');
            $table->index('is_active');
            $table->index('is_flash_sale');
            $table->index('sort_order');
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