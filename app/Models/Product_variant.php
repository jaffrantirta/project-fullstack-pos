<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'product_id', 
        'sku', 
        'purchase_price', 
        'selling_price', 
        'stock',
        'is_empty',
        'is_notification_alert'
    ];
}
