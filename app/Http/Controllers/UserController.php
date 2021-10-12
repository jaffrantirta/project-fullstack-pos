<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop_user;
use Illuminate\Support\Facades\Auth;
use App\Util\ResponseJson;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $user_detail = User::with('role')->find($user->id);
            $shop_detail = Shop_user::with('shop')->where('user_id', $user->id)->get();
            if($shop_detail[0]['shop']->is_active){
                $data = array(
                    'indonesia' => 'Login Berhasil',
                    'english' => 'You are logged in',
                    'data' => array(
                        'user' => $user_detail,
                        'shop' => $shop_detail,
                        'token' => $user->createToken('nApp')->accessToken
                    )
                );
                return response()->json(ResponseJson::response($data), 200);
            }else{
                $data = array(
                    'status' => false,
                    'indonesia' => 'Akun Anda Belum Aktif',
                    'english' => 'Your Account is not active'
                );
                return response()->json(ResponseJson::response($data), 401);
            }
        }
        else{
            $data = array(
                'status' => false,
                'indonesia' => 'Login Gagal',
                'english' => 'Failed to Login'
            );
            return response()->json(ResponseJson::response($data), 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('nApp')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success], 200);
    }

    public function logout(Request $request)
    {
        $logout = $request->user()->token()->revoke();
        if($logout){
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        }
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], 200);
    }
}
