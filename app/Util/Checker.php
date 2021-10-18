<?php
namespace App\Util;
use Validator;
use Illuminate\Http\Request;
class Checker {
    public static function valid($request, $valid)
    {
        $validator = Validator::make($request->all(), $valid);
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
            return $data ;      
        }else{
            return $data=null;
        }
    }
}