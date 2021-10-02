<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_user extends Model
{
    use HasFactory;
    public $timestamps = true;
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id');
    }
}
