<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    public $timestamps = true;
    public function shop_user()
    {
        return $this->belongsTo(Shop_user::class, 'shop_id', 'id');
    }
}
