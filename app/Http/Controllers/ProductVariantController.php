<?php

namespace App\Http\Controllers;

use App\Models\Product_variant;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $shop_id = Shop_user::where('user_id', $user->id)->first()->shop_id;
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $data = array(
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Product_variant::find($id),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            $data = array(
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Product_variant::where('product_id', $product_id)->paginate(5),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            return Product_variant::paginate(5);
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
        $check = Checker::valid($request, array('name'=>'required', 'product_id' => 'required|numeric', 'sku' => 'required', 'purchase_price' => 'required', 'selling_price' => 'required', 'stock' => 'required|numeric'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $add = new Product_variant();
                $add->name = $request->name;
                $add->product_id= $request->product_id;
                $add->sku= $request->sku;
                $add->purchase_price = $request->purchase_price;
                $add->selling_price = $request->selling_price;
                $add->stock = $request->stock;
                $add->is_empty = false;
                $add->is_notification_alert = true;
                $add->save();

                Log::create($shop, array('name'=>'product variant added', 'description'=>'product variant '.(string) $add.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Varian Produk Ditambahkan',
                    'english' => 'Product Variant Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Varian Produk',
                    'english' => 'Failed to Add Product Variant',
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
     * @param  \App\Models\Product_variant  $product_variant
     * @return \Illuminate\Http\Response
     */
    public function show(Product_variant $product_variant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product_variant  $product_variant
     * @return \Illuminate\Http\Response
     */
    public function edit(Product_variant $product_variant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product_variant  $product_variant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('name'=>'required', 'product_id' => 'required|numeric', 'sku' => 'required', 'purchase_price' => 'required', 'selling_price' => 'required', 'stock' => 'required|numeric', 'is_empty' => 'required', 'is_notification_alert' => 'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $update = Product_variant::find($id);
                $old_name = $update->name;
                $update->update($request->all());

                Log::create($shop, array('name'=>'product variant updated', 'description'=>'product variant '.(string) $old_name .' has been updated to be '.(string) json_encode($request->all()).' by '.$user->name));

                DB::commit();

                $data = array(
                    'indonesia' => 'Produk Varian Telah Diperbaharui',
                    'english' => 'Product Variant Updated',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Mengubah Produk Varian',
                    'english' => 'Failed to Update Product Variant',
                    'data' => array('error_message'=>$e->errorInfo[2])
                );
                return response()->json(ResponseJson::response($data), 500);
            } catch (\Throwable $th) {
                DB::rollback();
                return $th;
            }
        }else{
            return response()->json(ResponseJson::response($check), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product_variant  $product_variant
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $delete = Product_variant::find($id);
            $old_name = $delete;
            $delete->delete();

            Log::create($shop, array('name'=>'product variant deleted', 'description'=>'product Variant '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Produk Varian Dihapus',
                'english' => 'Product Variant Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Produk Varian Gagal Dihapus',
                'english' => 'Product Variant Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        }
    }
}
