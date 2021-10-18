<?php

namespace App\Http\Controllers;

use App\Models\Product_tax;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Shop_user;
use App\Models\Product;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class ProductTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            $data = array(
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Product_tax::where('product_id', $product_id)->get(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            return Datatables::of(Product_variant::all())->make(true);
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
        $check = Checker::valid($request, array('product_id'=>'required', 'tax' => 'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $add = new Product_tax();
                $add->product_id= $request->product_id;
                $add->tax= $request->tax;
                $add->save();

                Product::find($request->product_id)->update(array('is_tax'=>true));

                Log::create($shop, array('name'=>'product tax added', 'description'=>'product tax '.(string) $add.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Pajak Produk Ditambahkan',
                    'english' => 'Product Tax Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Pajak Produk',
                    'english' => 'Failed to Add Product Tax',
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
     * @param  \App\Models\Product_tax  $product_tax
     * @return \Illuminate\Http\Response
     */
    public function show(Product_tax $product_tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product_tax  $product_tax
     * @return \Illuminate\Http\Response
     */
    public function edit(Product_tax $product_tax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product_tax  $product_tax
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $product_id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('tax' => 'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $old = Product_tax::where('product_id', $product_id)->get();
                Product_tax::where('product_id', $product_id)->update($request->all());
                $new = Product_tax::where('product_id', $product_id)->get();

                Log::create($shop, array('name'=>'product tax updated', 'description'=>'product tax '.(string) $old .' has been updated to be '.(string) $new.' by '.$user->name));

                DB::commit();

                $data = array(
                    'indonesia' => 'Pajak Produk Telah Diperbaharui',
                    'english' => 'Product Tax Updated',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Mengubah Pajak Produk',
                    'english' => 'Failed to Update Product Tax',
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
     * @param  \App\Models\Product_tax  $product_tax
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $delete = Product_tax::where('product_id', $product_id)->get();
            $old_name = $delete;
            Product_tax::where('product_id', $product_id)->delete();

            Product::find($product_id)->update(array('is_tax'=>false));

            Log::create($shop, array('name'=>'product tax deleted', 'description'=>'product tax '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Tax Produk Dihapus',
                'english' => 'Product Tax Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Tax Produk Gagal Dihapus',
                'english' => 'Product Tax Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
}
