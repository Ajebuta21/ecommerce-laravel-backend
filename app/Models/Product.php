<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'quantity',
        'rating',
        'people_rated',
        'slug',
        'image_one',
        'image_two',
        'category',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'name');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
