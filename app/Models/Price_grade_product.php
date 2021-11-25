<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price_grade_product extends Model
{
    use HasFactory;
    public function buyer_type()
    {
        return $this->belongsTo(Buyer_type::class, 'buyer_type_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
