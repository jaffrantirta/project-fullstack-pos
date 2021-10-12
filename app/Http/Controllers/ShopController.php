<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Shop;
use App\Models\Shop_user;
use App\Models\Shop_category_owneds;
use App\Models\Verify_shop;
use App\Util\ResponseJson;
use App\Util\Checker;
use Validator;
use App\Mail\Main;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{
    public function register(Request $request)
    {
        $check = Checker::valid($request, array('shop_name' => 'required','address' => 'required','shop_phone' => 'required|numeric|digits:12','shop_email' => 'required|email','shop_category_id' => 'required','user_name' => 'required','email' => 'required|email|unique:users','user_phone' => 'required|numeric|digits:12',));
        if($check==null){
            DB::beginTransaction();
            try {
                $shop = new Shop();
                $shop->name = $request->shop_name;
                $shop->address = $request->address;
                $shop->phone = $request->shop_phone;
                $shop->email = $request->shop_email;
                $shop->is_active = false; 
                $shop->save();
                $shop_id = $shop->id;

                $shop_category_owned = new Shop_category_owneds();
                $shop_category_owned->shop_id = $shop_id;
                $shop_category_owned->shop_category_id = $request->shop_category_id;
                $shop_category_owned->save();

                $password = rand(10000000,99999999);
                $user = new User();
                $user->role_id = 1;
                $user->name = $request->user_name;
                $user->email = $request->email;
                $user->phone = $request->user_phone;
                $user->password = bcrypt($password);
                $user->save();
                $user_id = $user->id;

                $shop_user = new Shop_user();
                $shop_user->user_id = $user_id;
                $shop_user->shop_id = $shop_id;
                $shop_user->save();

                $token = sha1(time());
                $verify_shop = new Verify_shop();
                $verify_shop->shop_id = $shop_id;
                $verify_shop->token = $token;
                $verify_shop->save();

                DB::commit();
                $config = array(
                    'user_name'=>$request->user_name,
                    'email'=>$request->email,
                    'password'=>$password,
                );
        
                $data = array(
                    'title'=>'Registrasi Berhasil ',
                    'opening'=>'Hai, '.$config['user_name'].' Terimakasi sudah melalukan registrasi silahkan login pada link berikut https://franweb.my.id dan gunakan kridensial berikut :',
                    'content'=>'email : '.$config['email'].' password : '.$config['password'],
                    'closing'=>'sebelum login mohon lakukan aktivasi terlebih dahulu dengan link berikut ',
                    'closing_content'=>url('u/verify', $token),
                    'email'=>$config['email'],
                    'name'=>$config['user_name']
                );
                Mail::send('email_template', ['mail' => $data], function ($m) use ($data) {
                    $m->from('drivebali2016@gmail.com', 'POS');
                    $m->to($data['email'], $data['name'])->subject('Registrasi Sukses');
                });
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
        }else{
            return response()->json(ResponseJson::response($check), 401);
        }
    }
    public function verify($token)
    {
        $verifyShop = Verify_shop::where('token', $token)->first();
        if(isset($verifyShop) ){
            $shop = $verifyShop->shop;
            if(!$shop->is_active) {
            $verifyShop->shop->is_active = true;
            $verifyShop->shop->save();
            $status = "Your e-mail is verified. You can now login.";
            } else {
            $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
            return "Sorry your email cannot be identified.";
        }
        return $status;
    }
}
