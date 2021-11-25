<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer_type extends Model
{
    use HasFactory;
    public function price_grade_product()
    {
        return $this->hasOne(Price_grade_product::class, 'id', 'buyer_type_id');
    }
}
