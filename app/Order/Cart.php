<?php
namespace App\Order;
use Validator;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Product_photo;
use App\Models\Product_variant;
use App\Models\Price_grade_product;
use App\Models\Voucher;
use Carbon\Carbon;

class Cart {
    public static $total = 0;
    public static $total_items = 0;
    public static $items = null;
    public static function count($cart, $buyer_type_id, $request)
    {
        if (is_array($cart) || is_object($cart)){
            foreach($cart as $item){
                // $items[] = Cart::price_grade_variant($item, $buyer_type_id)->selling_price;
                $price_grade_variant = Cart::price_grade_variant($item, $buyer_type_id);
                if($price_grade_variant == null){
                    $price_grade_all_variant = Cart::price_grade_all_variant($item, $buyer_type_id);
                    if($price_grade_all_variant['price_grade_product'] == null){
                        $product_detail = Cart::product_detail($item);
                        if($product_detail['product'] == null || $product_detail['product_variant'] == null){
                            self::$items[] = array(
                                'status' => false,
                                'item_id' => $item->product_id,
                                'item_variant_id' => $item->product_variant_id
                            );
                        }else{
                            self::$total = self::$total + ($product_detail['product_variant']->selling_price*$item->qty);
                            self::$total_items = self::$total_items + $item->qty;
                            self::$items[] = Cart::product('product', $product_detail, $item->qty);
                        }
                    }else{
                        // return $price_grade_all_variant;
                        self::$total = self::$total + ($price_grade_all_variant['price_grade_product']->selling_price*$item->qty);
                        self::$total_items = self::$total_items + $item->qty;
                        self::$items[] = Cart::product('price_grade_all_variant', $price_grade_all_variant, $item->qty);
                    }
                }else{
                    self::$total = self::$total + ($price_grade_variant->selling_price*$item->qty);
                    self::$total_items = self::$total_items + $item->qty;
                    self::$items[] = Cart::product('price_grade_variant', $price_grade_variant, $item->qty);
                }
            }
            if(isset($request->voucher_code)){
                return Cart::accumulation_voucher($request);
            }else if(isset($request->discount_value)){
                return Cart::discount_custom($request);
            }else{
                return array('cart'=>self::$items, 'grand_total'=>self::$total, 'total_items'=>self::$total_items);
            }
        }else{
            return "non-object";
        }
    }
    public static function accumulation_voucher($request)
    {
        $voucher = Voucher::where('voucher_code', $request->voucher_code)
        ->whereDate('valid_start_at', '<=', Carbon::now())
        ->whereDate('valid_end_at', '>=', Carbon::now())
        ->first();

        if($voucher != null){
            $discount = 0;
            $grand_total = self::$total;
            if($voucher->type == 1){
                $discount = $voucher->value;
                $grand_total = self::$total - $discount;
            }else if($voucher->type == 2){
                $discount = self::$total * $voucher->value / 100;
                $grand_total = self::$total - $discount;
            }
            return array('voucher_code'=>$voucher->voucher_code, 'discount'=>'-'.$discount, 'cart'=>self::$items, 'grand_total'=>$grand_total, 'total_items'=>self::$total_items);
        }else{
            return array('cart'=>self::$items, 'grand_total'=>self::$total, 'total_items'=>self::$total_items);
        }
    }
    public static function discount_custom($request)
    {
        $discount = $request->discount_value;
        $grand_total = self::$total - $discount;
        return array('discount'=>'-'.$discount, 'cart'=>self::$items, 'grand_total'=>$grand_total, 'total_items'=>self::$total_items);
    }
    public static function product_detail($item)
    {
        $product['product_variant'] = Product_variant::find($item->product_variant_id);
        $product['product'] = Product::find($item->product_id);
        return $product;
    }
    public static function price_grade_all_variant($item, $buyer_type_id)
    {
        $data['price_grade_product'] = Price_grade_product::where('is_all_variant', true)
        ->where('product_id', $item->product_id)
        ->where('buyer_type_id', $buyer_type_id)
        ->where('minimum_qty', '<=', $item->qty)
        ->orderByRaw('minimum_qty DESC')
        ->with('product')
        ->first();
        if($data['price_grade_product'] == null){
            return $data;
        }else{
            $data['product_variant'] = Product_variant::find($item->product_variant_id);
            return $data;
        }
    }
    public static function price_grade_variant($item, $buyer_type_id)
    {
        return $data = Price_grade_product::where('product_variant_id', $item->product_variant_id)
        ->where('minimum_qty', '<=', $item->qty)
        ->where('buyer_type_id', $buyer_type_id)
        ->orderByRaw('minimum_qty DESC')
        ->with('product')
        ->with('product_variant')
        ->first();
    }
    public static function picture($data)
    {
        if(count($pict = Product_photo::where('product_id', $data['product']->id)->get()) > 0 ){
            return $pict[0]->picture;
        }else{
            return 'files/images/products/no_img.jpg';
        }
    }
    public static function product($type, $data, $qty)
    {
        if($type == 'product'){
            return $product = array(
                'status' => true,
                'id' => $data['product']->id,
                'name' => $data['product']->name,
                'description' => $data['product']->description,
                'note' => $data['product']->note,
                'variant_id' => $data['product_variant']->id,
                'variant_name' => $data['product_variant']->name,
                'variant_price' => $data['product_variant']->selling_price,
                'variant_sku' => $data['product_variant']->sku,
                'variant_stock' => $data['product_variant']->stock,
                'variant_is_empty' => $data['product_variant']->is_empty,
                'picture' => Cart::picture($data),
                'qty' => $qty,
                'sub_total' => $data['product_variant']->selling_price * $qty,
            );
        }else if($type == 'price_grade_variant'){
            return $product = array(
                'status' => true,
                'id' => $data['product']->id,
                'name' => $data['product']->name,
                'description' => $data['product']->description,
                'note' => $data['product']->note,
                'variant_id' => $data['product_variant']->id,
                'variant_name' => $data['product_variant']->name,
                'variant_price' => $data->selling_price,
                'variant_sku' => $data['product_variant']->sku,
                'variant_stock' => $data['product_variant']->stock,
                'variant_is_empty' => $data['product_variant']->is_empty,
                'picture' => Cart::picture($data),
                'qty' => $qty,
                'sub_total' => $data->selling_price * $qty,
            );
        }else if($type == 'price_grade_all_variant'){
            return $product = array(
                'status' => true,
                'id' => $data['price_grade_product']['product']->id,
                'name' => $data['price_grade_product']['product']->name,
                'description' => $data['price_grade_product']['product']->description,
                'note' => $data['price_grade_product']['product']->note,
                'variant_id' => $data['product_variant']->id,
                'variant_name' => $data['product_variant']->name,
                'variant_price' => $data['price_grade_product']->selling_price,
                'variant_sku' => $data['product_variant']->sku,
                'variant_stock' => $data['product_variant']->stock,
                'variant_is_empty' => $data['product_variant']->is_empty,
                'picture' => Cart::picture($data['price_grade_product']),
                'qty' => $qty,
                'sub_total' => $data['price_grade_product']->selling_price * $qty,
            );
        }
    }
}