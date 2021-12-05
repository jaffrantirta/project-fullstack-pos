<?php
namespace App\Order;
use Validator;
use Illuminate\Http\Request;

use App\Models\Voucher;
use Carbon\Carbon;

class Cart {
    public static function accumulation($request, $result)
    {
        $voucher = Voucher::where('voucher_code', $request->voucher_code)
        ->whereDate('valid_start_at', '<=', Carbon::now())
        ->whereDate('valid_end_at', '>=', Carbon::now())
        ->first();

        if($voucher != null){
            $total = $result->grand_total;
            if($voucher->type == 1){
                $grand_total = $total - $voucher->value;
            }else if($voucher->type == 2){
                $grand_total = $total - ($total * $voucher->value / 100);
            }
        }
    }
}