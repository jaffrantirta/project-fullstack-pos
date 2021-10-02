<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Shop;
use App\Models\Shop_user;
use App\Models\Shop_category_owneds;
use App\Util\ResponseJson;
use Validator;

class ShopController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_name' => 'required',
            'address' => 'required',
            'shop_phone' => 'required|numeric|digits:12',
            'shop_email' => 'required|email',
            'logo' => 'required',
            'shop_category_id' => 'required',
            'role_id' => 'required',
            'user_name' => 'required',
            'email' => 'required|email|unique:users',
            'user_phone' => 'required|numeric|digits:12',
        ]);

        if ($validator->fails()) {
            $error_message="";
            $i=0;
            foreach($validator->errors()->all() as $error){
                $error_message = $error_message." ".$error;
                $i++;
            }
            $data = array(
                'status' => false,
                'indonesia' => $error_message,
                'english' => $error_message
            );
            return response()->json(ResponseJson::response($data), 401);            
        }
        DB::beginTransaction();
        try {
            $shop = new Shop();
            $shop->name = $request->shop_name;
            $shop->address = $request->address;
            $shop->phone = $request->shop_phone;
            $shop->email = $request->shop_email;
            $shop->logo = $request->logo;
            $shop->is_active = false; 
            $shop->save();
            $shop_id = $shop->id;

            $shop_category_owned = new Shop_category_owneds();
            $shop_category_owned->shop_id = $shop_id;
            $shop_category_owned->shop_category_id = $request->shop_category_id;
            $shop_category_owned->save();

            $user = new User();
            $user->role_id = $request->role_id;
            $user->name = $request->user_name;
            $user->email = $request->email;
            $user->phone = $request->user_phone;
            $user->password = bcrypt(rand(10000000,99999999));
            $user->save();
            $user_id = $user->id;

            $shop_user = new Shop_user();
            $shop_user->user_id = $user_id;
            $shop_user->shop_id = $shop_id;
            $shop_user->save();

            DB::commit();
            $data = array(
                'indonesia' => 'Registrasi Berhasil, mohon untuk cek email anda untuk verifikasi akun',
                'english' => 'You are registered now, please chack your email to verify your account'
            );
            return response()->json(ResponseJson::response($data), 200);
        } catch (\Exception $e) {
            DB::rollback();
            $data = array(
                'status' => false,
                'indonesia' => 'Registrasi Gagal',
                'english' => 'Your Registration is Failed',
                'data' => array('error_message'=>$e->errorInfo[2])
            );
            return response()->json(ResponseJson::response($data), 500);
        }
    }
}
