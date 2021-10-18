<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verify_shop extends Model
{
    use HasFactory;
    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id');
    }
}
