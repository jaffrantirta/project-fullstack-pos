<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Group;
use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
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
                'data' => Group::where('id', $id)->with('category')->first(),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            return Group::where('shop_id', $shop_id)
            ->join('categories', 'categories.id', '=', 'groups.category_id')
            ->select('groups.*', 'categories.name as category_name')
            ->where('groups.is_active', true)
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
        $check = Checker::valid($request, array('name'=>'required', 'category_id' => 'required|numeric'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $vGroup = new Group();
                $vGroup->name = $request->name;
                $vGroup->category_id= $request->category_id;
                $vGroup->is_pos= true;
                $vGroup->is_active= true;
                $vGroup->shop_id= $shop[0]->shop_id;
                $vGroup->save();

                Log::create($shop, array('name'=>'group added', 'description'=>'group '.(string) $vGroup.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Grup Ditambahkan',
                    'english' => 'Group Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Grup',
                    'english' => 'Failed to Add Group',
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
            DB::beginTransaction();
            try {
                $update = Group::find($id);
                $old_name = $update->name;
                $update->update(array(
                    'name'=>$request->name,
                    'category_id'=>$request->category_id,
                ));

                Log::create($shop, array('name'=>'group updated', 'description'=>'group '.(string) $update.' has been updated to be '.(string) json_encode($request->all()).' by '.$user->name));

                DB::commit();

                $data = array(
                    'indonesia' => 'Grup Telah Diperbaharui',
                    'english' => 'Group Updated',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Mengubah Grup',
                    'english' => 'Failed to Update Group',
                    'data' => array('error_message'=>$e->errorInfo[2])
                );
                return response()->json(ResponseJson::response($data), 500);
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
            $delete = Group::find($id);
            $old_name = $delete;
            $delete->delete();

            Log::create($shop, array('name'=>'group deleted', 'description'=>'group '.(string) $old_name.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Grup Dihapus',
                'english' => 'Group Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Grup Gagal Dihapus',
                'english' => 'Group Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        }
    }
}
