<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'store_id'];

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
}
