<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website_url',
        'published',
        'seo_title',
        'seo_description',
        'seo_keywords'
    ];

    protected $casts = [
        'published' => 'boolean',
        'seo_keywords' => 'array',
    ];
}
