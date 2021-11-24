<?php

namespace App\Http\Controllers;

use App\Models\Product_photo;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;
use App\Models\Shop_user;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class ProductPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo "halo";
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
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        $check = Checker::valid($request, array('file' => 'required', 'product_id' => 'required'));
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
                    if ($files = $request->file('file')) {
                        $path = 'files/images/products/';
                        foreach($files as $file){
                            $name = time().$file->getClientOriginalName();
                            $vPhoto = new Product_photo();
                            $vPhoto->product_id = $request->product_id;
                            $vPhoto->picture = $path.$name;
                            $vPhoto->save();
                            $file->move($path,$name);
                            Log::create($shop, array('name'=>'product photo added', 'description'=>'photo '.(string) $name.' successful added by '.$user->name));
                        }
                        DB::commit();
                        
                        $data = array(
                            'indonesia' => 'Produk Ditambahkan',
                            'english' => 'Product Added',
                        );
                        return response()->json(ResponseJson::response($data), 200);
                    }else{
                        $data = array(
                            'status' => false,
                            'indonesia' => 'Tidak Ada Gambar Terupload',
                            'english' => 'No One Picture Upload',
                        );
                        return response()->json(ResponseJson::response($data), 404);
                    }
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
     * @param  \App\Models\Product_photo  $product_photo
     * @return \Illuminate\Http\Response
     */
    public function show(Product_photo $product_photo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product_photo  $product_photo
     * @return \Illuminate\Http\Response
     */
    public function edit(Product_photo $product_photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product_photo  $product_photo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product_photo $product_photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product_photo  $product_photo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $delete = Product_photo::find($id);
            $old_name = $delete;
            $delete->delete();

            Log::create($shop, array('name'=>'product photo deleted', 'description'=>'product '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Foto Produk Dihapus',
                'english' => 'Product Photo Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Foto Produk Gagal Dihapus',
                'english' => 'Product Photo Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        }
    }
}
