<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'sku',
        'price',
        'sale_price',
        'stock',
        'image',
        'product_type',
        'excerpt',
        'description',
        'affiliated_ids',
        'featured',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'published'
    ];

    protected $casts = [
        'affiliated_ids' => 'array',
        'featured' => 'boolean',
        'published' => 'boolean',
    ];
}
