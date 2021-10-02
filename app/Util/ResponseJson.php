<?php
namespace App\Util;
class ResponseJson {
    public static function response($x)
    {
        if(isset($x['status'])){
            $status = $x['status'];
        }else{
            $status = true;
        }
        if(isset($x['data'])){
            $y = $x['data'];
        }else{
            $y = null;
        }
        $data['response']['status'] = $status;
        $data['response']['message']['indonesia'] = $x['indonesia'];
        $data['response']['message']['english'] = $x['english'];
        $data['data'] = $y;
        return $data;
    }
}