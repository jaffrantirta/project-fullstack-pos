<?php

namespace App\Http\Controllers;

use App\Models\Payment_method;
use Illuminate\Http\Request;

use App\Util\ResponseJson;
use App\Util\Checker;
use App\Util\Log;

use App\Models\Shop_user;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
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
                'data' => Payment_method::find($id),
            );
            return response()->json(ResponseJson::response($data), 200);
        }else if(isset($_GET['all'])){
            if($_GET['all']){
                return Payment_method::where('shop_id', $shop_id)->get();
            }else{
                return response()->json(['404'], 404);
            }
        }else{
            return Payment_method::where('shop_id', $shop_id)->latest()->paginate(5);
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
                $add = new Payment_method();
                $add->name= $request->name;
                $add->shop_id = $shop[0]->shop_id;
                $add->save();

                Log::create($shop, array('name'=>'payment method added', 'description'=>'payment method '.(string) $add.' successful added by '.$user->name));

                DB::commit();
                        
                $data = array(
                    'indonesia' => 'Metode Pembayaran Ditambahkan',
                    'english' => 'Payment Method Added',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Menambah Metode Pembayaran',
                    'english' => 'Failed to Add Payment Method',
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
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function show(Payment_method $payment_method)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment_method $payment_method)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment_method  $payment_method
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
                $old = Payment_method::where('id', $id)->get();
                Payment_method::where('id', $id)->update($request->all());
                $new = Payment_method::where('id', $id)->get();

                Log::create($shop, array('name'=>'payment method updated', 'description'=>'payment method '.(string) $old .' has been updated to be '.(string) $new.' by '.$user->name));

                DB::commit();

                $data = array(
                    'indonesia' => 'Metode Pembayaran Telah Diperbaharui',
                    'english' => 'Payment Method Updated',
                );
                return response()->json(ResponseJson::response($data), 200);

            } catch (\Exception $e) {
                DB::rollback();
                $data = array(
                    'status' => false,
                    'indonesia' => 'Gagal Mengubah Metode Pembayaran',
                    'english' => 'Failed to Update Payment Method',
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
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user(); 
        $shop = Shop_user::with('shop')->where('user_id', $user->id)->get();
        DB::beginTransaction();
        try {
            $old = Payment_method::where('id', $id)->get();
            Payment_method::where('id', $id)->delete();

            Log::create($shop, array('name'=>'payment method deleted', 'description'=>'payment method '.(string) $old.' has been deleted by '.$user->name));

            DB::commit();
            $data = array(
                'indonesia' => 'Metode Pembayaran Dihapus',
                'english' => 'Payment Method Deleted',
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Metode Pembayaran Gagal Dihapus',
                'english' => 'Payment Method Failed to Delete',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
}
