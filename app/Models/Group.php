<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'category_id', 'is_pos', 'is_active', 'shop_id'
    ];
    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
