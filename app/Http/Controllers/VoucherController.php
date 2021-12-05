<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
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
                'data' => Voucher::find($id),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else if(isset($_GET['all'])){
            if($_GET['all']){
                return Voucher::where('shop_id', $shop_id)->get();
            }else{
                return response()->json(['404'], 404);
            }
        }else{
            return Voucher::where('shop_id', $shop_id)->latest()->paginate(5);
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
        $check = Checker::valid($request, array(
            'name'=>'required',
            'valid_start_at'=>'required|date',
            'valid_end_at'=>'required|date|after_or_equal:valid_start_at',
            'value'=>'required|numeric',
            'description'=>'required',
            'type'=>'required|numeric',
            'voucher_code'=>'required|unique:vouchers,voucher_code'
        ));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $add = new Voucher();
                $add->name= $request->name;
                $add->valid_start_at= $request->valid_start_at;
                $add->valid_end_at= $request->valid_end_at;
                $add->value= $request->value;
                $add->description= $request->description;
                $add->type= $request->type;
                $add->voucher_code= $request->voucher_code;
                $add->shop_id = $shop[0]->shop_id;
                $add->save();

                Log::create($shop, array('name'=>'voucher added', 'description'=>'voucher '.(string) $add.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Voucher Ditambahkan',
                    'english' => 'Voucher Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Voucher',
                    'english' => 'Failed to Add Voucher',
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
     * @param  \App\Models\Voucher $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Voucher $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Voucher $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $check = Checker::valid($request, array(
            'name'=>'required',
            'valid_start_at'=>'required|date',
            'valid_end_at'=>'required|date|after_or_equal:valid_start_at',
            'value'=>'required|numeric',
            'description'=>'required',
            'type'=>'required|numeric',
            'voucher_code'=>'required'
        ));
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        if($check==null){
            DB::beginTransaction();
            try {
                $old = Voucher::where('id', $id)->get();
                Voucher::where('id', $id)->update($request->all());
                $new = Voucher::where('id', $id)->get();

                Log::create($shop, array('name'=>'voucher updated', 'description'=>'voucher '.(string) $old .' has been updated to be '.(string) $new.' by '.$user->name));

                DB::commit();

                $data = array(
                    'indonesia' => 'Voucher Telah Diperbaharui',
                    'english' => 'Voucher Updated',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Mengubah Voucher',
                    'english' => 'Failed to Update Voucher',
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
     * @param  \App\Models\Voucher $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $old = Voucher::where('id', $id)->get();
            Voucher::where('id', $id)->delete();

            Log::create($shop, array('name'=>'voucher deleted', 'description'=>'voucher '.(string) $old.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Voucher Dihapus',
                'english' => 'Voucher Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Voucher Gagal Dihapus',
                'english' => 'Voucher Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
}