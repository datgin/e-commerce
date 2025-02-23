<?php

use App\Models\Product;
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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->string('sku', 100)->unique();
            $table->string('image');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('width', 10, 2)->nullable()->comment('Chiều rộng (cm)');
            $table->decimal('height', 10, 2)->nullable()->comment('Chiều cao (cm)');
            $table->decimal('length', 10, 2)->nullable()->comment('Chiều dài (cm)');
            $table->decimal('weight', 10, 2)->nullable()->comment('Trọng lượng (kg)');
            $table->boolean('published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
