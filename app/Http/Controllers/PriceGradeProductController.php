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
                'data' => Price_grade_product::with('product')->with('buyer_type')->first(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            $data = array(
                'status' => false,
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Price_grade_product::with('product')->with('buyer_type')->first(),
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
        $check = Checker::valid($request, array('product_id'=>'required', 'buyer_type_id'=>'required', 'minimum_qty'=>'required', 'type'=>'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $add = new Price_grade_product();
                $add->buyer_type_id = $request->buyer_type_id;
                $add->type = $request->type;
                $add->selling_price = $request->selling_price;
                $add->discount = $request->discount;
                $add->product_obtained = $request->product_obtained ;
                $add->product_id = $request->product_id;
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
                    'data' => array('error_message'=>$e->errorInfo[2])
                );
                return response()->json(ResponseJson::response($data), 500);
            }
        }else{
            return response()->json(ResponseJson::response($check), 401);
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
    public function update(Request $request, Price_grade_product $price_grade_product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Price_grade_product  $price_grade_product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Price_grade_product $price_grade_product)
    {
        //
    }
}
