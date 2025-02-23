<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;

class Catalogue extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'parent_id',
        'published',
        'seo_title',
        'seo_description',
        'seo_keywords'
    ];

    protected $casts = [
        'published' => 'boolean',
        'seo_keywords' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Catalogue::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Catalogue::class, 'parent_id');
    }

    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }

    protected static function booted()
    {
        static::saved(function ($catalogue) {
            // Xóa cache liên quan khi bản ghi được lưu
            Cache::forget('catalogues_list');
        });

        static::deleted(function ($catalogue) {
            // Xóa cache khi bản ghi bị xóa
            Cache::forget('catalogues_list');
        });
    }
}
