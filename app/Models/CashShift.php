<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'opened_at',
        'closed_at',
        'cash_start',
        'cash_end',
        'cash_diff',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'shift_id');
    }
}
