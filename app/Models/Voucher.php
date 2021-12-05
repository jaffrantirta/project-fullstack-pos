<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'valid_start_at',
        'valid_end_at',
        'value',
        'description',
        'type',
        'voucher_code',
    ];
}
