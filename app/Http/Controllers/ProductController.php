<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Product_photo;
use App\Models\Product_variant;
use App\Models\Product_tax;
use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $shop_id = Shop_user::with('shop')->where('user_id', $user->id)->first()->shop_id;
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            if($id == "null" || $id == "undefined"){
                $data = array(
                    'status' => false,
                    'indonesia' => 'Tidak Ada Produk Ditemukan',
                    'english' => 'No One Product Found',
                );
                return response()->json(ResponseJson::response($data), 404);
            }else{
                $product = array(
                    'product'=>Product::where('products.id', $id)
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->leftJoin('product_photos', 'product_photos.product_id', '=', 'products.id')
                    ->select('groups.name as group_name', 'products.*', 'product_photos.picture')
                    ->groupBy('products.id')
                    ->first(),
                    'pictures'=>Product_photo::where('product_id', $id)->get(),
                    'tax'=>Product_tax::where('product_id', $id)->first(),
                    'variants'=>Product_variant::where('product_id', $id)->latest()->get(),
                );
                $data = array(
                    'indonesia' => 'Ditemukan',
                    'english' => 'Founded',
                    'data' => $product,
                );
                return response()->json(ResponseJson::response($data), 200);
            }
        }else{
            return Product::where('products.shop_id', $shop_id)
            ->join('groups', 'groups.id', '=', 'products.group_id')
            ->leftJoin('product_photos', 'product_photos.product_id', '=', 'products.id')
            ->select('groups.name as group_name', 'products.*', 'product_photos.picture')
            ->groupBy('products.id')
            ->where('products.is_active', true)
            ->latest()
            ->paginate(5);
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
        $check = Checker::valid($request, array('name'=>'required', 'group_id' => 'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($files = $request->file('file')){
            $ext = $request->file('file');
            $ext_allowed = array();
            $ext_disallowed = array();
            foreach($ext as $x){
                if($x->getClientOriginalExtension() == 'jpg' || $x->getClientOriginalExtension() == 'jpeg' || $x->getClientOriginalExtension() == 'png'){
                    $ext_allowed[] = $x->getClientOriginalExtension();
                }else{
                    $ext_disallowed[] = $x->getClientOriginalExtension();
                }
            }
        }else{
            $ext_allowed[] = true;
            $ext[] = true;
        }
        
        if(count($ext_allowed) == count($ext)){
            if($check==null){
                DB::beginTransaction();
                try {
                    
                    $vProduct = new Product();
                    $vProduct->group_id = $request->group_id;
                    $vProduct->name = $request->name;
                    $vProduct->maximum_purchase = $request->maximum_purchase;
                    $vProduct->description = $request->description;
                    $vProduct->note = $request->note;
                    $vProduct->shop_id = $shop[0]->shop_id;
                    if(isset($request->minimum_purchase)){
                        $vProduct->minimum_purchase = $request->minimum_purchase;
                    }
                    if(isset($request->is_dynamic_price)){
                        $vProduct->is_dynamic_price = $request->is_dynamic_price;
                    }
                    if(isset($request->is_tax)){
                        $vProduct->is_tax = $request->is_tax;
                    }
                    if(isset($request->is_dynamic_price)){
                        $vProduct->is_dynamic_price = $request->is_dynamic_price;
                    }
                    $vProduct->save();
                    $product_id = $vProduct->id;

                    if ($files = $request->file('file')) {
                        $path = 'files/images/products/';
                        foreach($files as $file){
                            $name = time().$file->getClientOriginalName();
                            $vPhoto = new Product_photo();
                            $vPhoto->product_id = $product_id;
                            $vPhoto->picture = $path.$name;
                            $vPhoto->save();
                            $file->move($path,$name);
                        }
                    }

                    Log::create($shop, array('name'=>'Product added', 'description'=>'product '.(string) $vProduct.' successful added by '.$user->name));

                    DB::commit();
                        
                    $data = array(
                        'indonesia' => 'Produk Ditambahkan',
                        'english' => 'Product Added',
                    );
                    return response()->json(ResponseJson::response($data), 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Gagal Menambah Produk',
                        'english' => 'Failed to Add Product',
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
        }else{
            $data = array(
                'status' => false,
                'indonesia' => count($ext_disallowed).' Format Gambar Tidak sesuai. Gunakan Format Gambar JPG, JPEG atau PNG',
                'english' => count($ext_disallowed).' Extension Picture are disallowed. Please Use Extension JPG, JPEG or PNG of the picture',
                'data' => array('error_message'=>$ext_disallowed)
            );
            return response()->json(ResponseJson::response($data), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('name'=>'required', 'group_id' => 'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
            if($check==null){
                DB::beginTransaction();
                try {
                    $old_product = Product::find($id);
                    $update = Product::find($id)->update($request->all());
                    $new_product = Product::find($id);

                    Log::create($shop, array('name'=>'Product updated', 'description'=>'product '.(string) $old_product.' successful update to be '.(string) $new_product.' by '.$user->name));

                    DB::commit();
                        
                    $data = array(
                        'indonesia' => 'Produk Diperbaharui',
                        'english' => 'Product Updated',
                    );
                    return response()->json(ResponseJson::response($data), 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Gagal Memperbaharui Produk',
                        'english' => 'Failed to Update Product',
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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $delete = Product::find($id);
            $old_name = $delete;
            $delete->delete();

            Log::create($shop, array('name'=>'product deleted', 'description'=>'product '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Produk Dihapus',
                'english' => 'Product Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Produk Gagal Dihapus',
                'english' => 'Product Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        }
    }
}
