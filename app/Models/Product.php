<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Artisan;

class Product extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'is_active',
        'name',
        'description',
        'price',
        'store_id',
        'category_id',
        'slug'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'product_promotion');
    }

    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted()
    {
        static::created(function ($product) {
            Artisan::call('sitemap:generate');
        });

        static::deleted(function ($product) {
            Artisan::call('sitemap:generate');
        });
    }

}
