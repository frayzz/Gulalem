<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'store_id',
        'buy_price',
        'qty_in',
        'qty_left',
        'arrived_at',
        'expires_at',
    ];

    protected $casts = [
        'arrived_at' => 'date',
        'expires_at' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'batch_id');
    }
}
