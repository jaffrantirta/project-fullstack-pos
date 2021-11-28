<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price_grade_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 
        'buyer_type_id', 
        'minimum_qty', 
        'selling_price', 
        'product_variant_id', 
        'is_all_variant',
    ];
    public function buyer_type()
    {
        return $this->belongsTo(Buyer_type::class, 'buyer_type_id', 'id');
    }
    public function product_variant()
    {
        return $this->belongsTo(Product_variant::class, 'product_variant_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
