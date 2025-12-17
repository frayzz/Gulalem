<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_phone',
        'recipient_phone',
        'address',
        'selected_time',
        'custom_time',
        'card_message',
        'payment_method',
        'status',
        'customer_id',
        'delivery_type',
        'delivery_address',
        'delivery_time',
        'total',
        'discount',
        'paid_total',
        'payment_status',
        'store_id',
        'city',
        'region',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
