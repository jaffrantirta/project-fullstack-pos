<?php

namespace App\Http\Controllers;

use App\Models\Buyer_type;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuyerTypeController extends Controller
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
            $data = array(
                'indonesia' => 'Ditemukan',
                'english' => 'Founded',
                'data' => Buyer_type::find($id),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else{
            return Buyer_type::where('shop_id', $shop_id)->latest()->paginate(5);
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
        $check = Checker::valid($request, array('name'=>'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $add = new Buyer_type();
                $add->name= $request->name;
                $add->shop_id = $shop[0]->shop_id;
                $add->save();

                Log::create($shop, array('name'=>'buyer type added', 'description'=>'buyer type '.(string) $add.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Tipe Pembeli Ditambahkan',
                    'english' => 'Buyer Type Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Tipe Pembeli',
                    'english' => 'Failed to Add Buyer Type',
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
     * @param  \App\Models\Buyer_type  $buyer_type
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer_type $buyer_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Buyer_type  $buyer_type
     * @return \Illuminate\Http\Response
     */
    public function edit(Buyer_type $buyer_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Buyer_type  $buyer_type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array('name' => 'required'));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $old = Buyer_type::where('id', $id)->get();
                Buyer_type::where('id', $id)->update($request->all());
                $new = Buyer_type::where('id', $id)->get();

                Log::create($shop, array('name'=>'buyer type updated', 'description'=>'buyer type '.(string) $old .' has been updated to be '.(string) $new.' by '.$user->name));

                DB::commit();

                $data = array(
                    'indonesia' => 'Tipe Pembeli Telah Diperbaharui',
                    'english' => 'Buyer Type Updated',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Mengubah Tipe Pembeli',
                    'english' => 'Failed to Update Buyer Type',
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
     * @param  \App\Models\Buyer_type  $buyer_type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $old = Buyer_type::where('id', $id)->get();
            Buyer_type::where('id', $id)->delete();

            Log::create($shop, array('name'=>'buyer type deleted', 'description'=>'buyer type '.(string) $old.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Tipe Pembeli Dihapus',
                'english' => 'Buyer Type Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Tipe Pembeli Gagal Dihapus',
                'english' => 'Buyer Type Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
}
