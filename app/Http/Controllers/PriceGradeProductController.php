<?php

namespace App\Http\Controllers;

use App\Models\Price_grade_product;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;
use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceGradeProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $data = array(
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Price_grade_product::with('product_variant')
                ->with('product')
                ->with('buyer_type')
                ->where('id', $id)
                ->first(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            $data = array(
                'status' => false,
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Price_grade_product::with('product_variant')->with('buyer_type')->with('product')->first(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('product_id'=>'required', 'buyer_type_id'=>'required', 'minimum_qty'=>'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            if(!$request->is_all_variant){
                if($request->product_variant_id == "" || $request->product_variant_id == null){
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Mohon Pilih Varian',
                        'english' => 'Please Fill Velue of Discount',
                    );
                    return response()->json(ResponseJson::response($data), 401);
                }else{
                    return $this->insert_data($request, $shop, $user);
                }
            }else{
                return $this->insert_data($request, $shop, $user);
            }
        }else{
            return response()->json(ResponseJson::response($check), 401);
        }
    }
    public function insert_data($request, $shop, $user)
    {
        // return array($request, $shop);
        DB::beginTransaction();
            try {
                $add = new Price_grade_product();
                $add->buyer_type_id = $request->buyer_type_id;
                $add->selling_price = $request->selling_price;
                $add->product_id = $request->product_id;
                $add->minimum_qty = $request->minimum_qty;
                $add->is_all_variant = $request->is_all_variant;
                $add->product_variant_id = $request->product_variant_id;
                $add->save();

                Log::create($shop, array('name'=>'price grade product added', 'description'=>'price grade product '.(string) $add.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Tingkatan Harga Ditambahkan',
                    'english' => 'Price Grade Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Tingkatan Harga',
                    'english' => 'Failed to Add Price Grade',
                    'data' => array('error_message'=>$e)
                );
                return response()->json(ResponseJson::response($data), 500);
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function show(Price_grade_product $price_grade_product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function edit(Price_grade_product $price_grade_product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('product_id'=>'required', 'buyer_type_id'=>'required', 'minimum_qty'=>'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
            if($check==null){
                if(!$request->is_all_variant){
                    if($request->product_variant_id == "" || $request->product_variant_id == null){
                        $data = array(
                            'status' => false,
                            'indonesia' => 'Mohon Pilih Varian',
                            'english' => 'Please Fill Velue of Discount',
                        );
                        return response()->json(ResponseJson::response($data), 401);
                    }else{
                        return $this->update_data($request, $shop, $id, $user);
                    }
                }else{
                    return $this->update_data($request, $shop, $id, $user);
                }
            }else{
                return response()->json(ResponseJson::response($check), 401);
            }
    }
    public function update_data($request, $shop, $id, $user)
    {
        DB::beginTransaction();
                try {
                    $old = Price_grade_product::find($id);
                    $update = Price_grade_product::find($id)->update($request->all());
                    $new = Price_grade_product::find($id);

                    Log::create($shop, array('name'=>'Price Grade Product updated', 'description'=>'Price Grade Product '.(string) $old.' successful update to be '.(string) $new.' by '.$user->name));

                    DB::commit();
                        
                    $data = array(
                        'indonesia' => 'Tingkatan Harga Diperbaharui',
                        'english' => 'Price Grade Updated',
                        'data' => array('request'=>$request->all(), 'id'=>$id, 'user'=>$user, 'shop'=>$shop)
                    );
                    return response()->json(ResponseJson::response($data), 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Gagal Memperbaharui Tingkatan Harga',
                        'english' => 'Failed to Update Price Grade',
                        'data' => array('error_message'=>$e, 'request'=>$request->all(), 'id'=>$id, 'user'=>$user, 'shop'=>$shop)
                    );
                    return response()->json(ResponseJson::response($data), 500);
                } catch (\Throwable $th) {
                    DB::rollback();
                    return $th;
                }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $delete = Price_grade_product::find($id);
            $old_name = $delete;
            $delete->delete();

            Log::create($shop, array('name'=>'Price Grade deleted', 'description'=>'Price Grade '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Tingkatan Harga Dihapus',
                'english' => 'Price Grade Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Tingkatan Harga Gagal Dihapus',
                'english' => 'Price Grade Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        }
    }
}
