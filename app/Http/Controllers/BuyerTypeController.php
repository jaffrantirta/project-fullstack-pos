<?php

namespace App\Http\Controllers;

use App\Models\Buyer_type;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
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
        //
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
    public function update(Request $request, Buyer_type $buyer_type)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Buyer_type  $buyer_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Buyer_type $buyer_type)
    {
        //
    }
}
