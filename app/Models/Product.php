<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'commission', 
        'minimum_purchase', 
        'maximum_purchase', 
        'is_dynamic_price', 
        'is_tax',
        'description',
        'note',
        'is_show',
        'is_active',
        'shop_id',
        'group_id'
    ];
    public function price_grade_product()
    {
        return $this->hasOne(Price_grade_product::class, 'id', 'product_id');
    }
}
