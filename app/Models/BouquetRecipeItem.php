<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BouquetRecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'product_id',
        'qty',
    ];

    public function recipe()
    {
        return $this->belongsTo(BouquetRecipe::class, 'recipe_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
