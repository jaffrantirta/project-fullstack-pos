<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Category;
use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
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
                'data' => Category::find($id),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            return Category::where('shop_id', $shop_id)->where('is_active', true)->paginate(5);
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
        $check = Checker::valid($request, array('name'=>'required', 'file' => 'required|mimes:jpg,jpeg,png|max:2048'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            if ($file = $request->file('file')) {
                $path = 'files/images/categories/';
                $name = time().$file->getClientOriginalName();
    
                DB::beginTransaction();
                try {
                    $vCategory = new Category();
                    $vCategory->name = $request->name;
                    $vCategory->picture= $path.$name;
                    $vCategory->is_active= true;
                    $vCategory->shop_id= $shop[0]->shop_id;
                    $vCategory->save();
                    $file->move($path,$name);

                    Log::create($shop, array('name'=>'category added', 'description'=>'category '.(string) $vCategory.' successful added by '.$user->name));

                    DB::commit();
                        
                    $data = array(
                        'indonesia' => 'Kategori Ditambahkan',
                        'english' => 'Category Added',
                        'data' => null,
                    );
                    return response()->json(ResponseJson::response($data), 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Gagal Menambah Kategori',
                        'english' => 'Failed to Add Category',
                        'data' => array('error_message'=>$e->errorInfo[2])
                    );
                    return response()->json(ResponseJson::response($data), 500);
                }
            }
        }else{
            return response()->json(ResponseJson::response($check), 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('name'=>'required', 'file' => 'mimes:jpg,jpeg,png|max:2048'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            if ($file = $request->file('file')) {
                DB::beginTransaction();
                try {
                    $update = Category::find($id);
                    $old_name = $update->name;
                    $path = 'files/images/categories/';
                    $name = time().$file->getClientOriginalName();
                    $path_remove = $update[0]->picture;
        
                    $update->update(array(
                        'name'=>$request->name,
                        'picture'=>$path.$name,
                    ));
                    $file->move($path,$name);
                    // unlink(public_path($path_remove));

                    Log::create($shop, array('name'=>'category updated', 'description'=>'category name or icon '.$old_name.' has been updated to be '.$request->name.' by '.$user->name));

                    $data = array(
                        'indonesia' => 'Kategori Telah Diperbaharui',
                        'english' => 'Category Updated',
                        'data' => null,
                    );
                    return response()->json(ResponseJson::response($data), 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Gagal Mengubah Kategori',
                        'english' => 'Failed to Update Category',
                        'data' => array('error_message'=>$e->errorInfo[2])
                    );
                    return response()->json(ResponseJson::response($data), 500);
                }
            }else{
                DB::beginTransaction();
                try {

                    $update = Category::find($id);
                    $old_name = $update->name;
                    $update->update(array(
                        'name'=>$request->name,
                    ));

                    Log::create($shop, array('name'=>'category updated', 'description'=>'category '.$old_name.' has been updated to be '.$request->name.' by '.$user->name));

                    DB::commit();

                    $data = array(
                        'indonesia' => 'Kategori Telah Diperbaharui',
                        'english' => 'Category Updated',
                        'data' => null,
                    );
                    return response()->json(ResponseJson::response($data), 200);

                } catch (\Exception $e) {
                    DB::rollback();
                    $data = array(
                        'status' => false,
                        'indonesia' => 'Gagal Mengubah Kategori',
                        'english' => 'Failed to Update Category',
                        'data' => array('error_message'=>$e->errorInfo[2])
                    );
                    return response()->json(ResponseJson::response($data), 500);
                }
            }
        }else{
            return response()->json(ResponseJson::response($check), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $delete = Category::find($id);
            $old_name = $delete;
            $path = $delete->logo;
            $delete->delete();
            // unlink(public_path($path));

            Log::create($shop, array('name'=>'category deleted', 'description'=>'category '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Kategori Dihapus',
                'english' => 'Category Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Kategori Gagal Dihapus',
                'english' => 'Category Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        }
    }
}
