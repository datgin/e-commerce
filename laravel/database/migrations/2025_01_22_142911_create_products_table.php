<?php

use App\Models\Brand;
use App\Models\Catalogue;
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
            $table->foreignIdFor(Brand::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Catalogue::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('sku', 100)->nullable()->unique();
            $table->decimal('price', 15, 2)->default(0)->comment('Giá chỉ dùng cho sản phẩm đơn giản');
            $table->decimal('sale_price', 15, 2)->nullable()->comment('Giá khuyến mãi cho sản phẩm đơn giản');
            $table->integer('stock')->default(0)->comment('Tồn kho chỉ áp dụng cho sản phẩm đơn giản');
            $table->string('image');
            $table->enum('product_type', ['simple', 'variant'])->default('simple')->comment('Loại sản phẩm');
            $table->string('excerpt')->nullable()->comment('Mô tả tóm tắt');
            $table->longText('description')->nullable()->comment('Mô tả chi tiết');
            $table->json('affiliated_ids')->nullable()->comment('Danh sách ID sản phẩm liên kết');
            $table->boolean('featured')->default(false);
            $table->enum('shipping_type', ['pickup', 'delivery'])->default('pickup')->comment('Loại vận chuyển');
            $table->decimal('width', 10, 2)->nullable()->comment('Chiều rộng (cm)');
            $table->decimal('height', 10, 2)->nullable()->comment('Chiều cao (cm)');
            $table->decimal('length', 10, 2)->nullable()->comment('Chiều dài (cm)');
            $table->decimal('weight', 10, 2)->nullable()->comment('Trọng lượng (kg)');
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->boolean('published')->default(true);
            $table->softDeletes();
            $table->timestamps();
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
