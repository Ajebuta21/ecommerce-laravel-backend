<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'cart',
        'subtotal_price',
        'delivery_fee',
        'total_price',
        'user_id',
        'user_email',
        'user_phone_number',
        'user_address',
        'region',
        'reference',
        'status',
        'order_number',
    ];

    protected $casts = [
        'cart' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateOrderNumber()
    {
        return strtoupper(uniqid('ORDER-'));
    }
}
