<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'image',
        'price',
        'sale_price',
        'stock',
        'published'
    ];

    protected $casts = [
        'published' => 'boolean'
    ];
}
