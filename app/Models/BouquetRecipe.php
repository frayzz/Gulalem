<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BouquetRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'bouquet_product_id',
        'name',
    ];

    public function bouquet()
    {
        return $this->belongsTo(Product::class, 'bouquet_product_id');
    }

    public function items()
    {
        return $this->hasMany(BouquetRecipeItem::class, 'recipe_id');
    }
}
