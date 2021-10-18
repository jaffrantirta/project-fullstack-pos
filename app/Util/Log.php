<?php
namespace App\Util;
use Illuminate\Http\Request;
use App\Models\Log as mLog;
use App\Models\Shop_user;

class Log {
    public static function create($shop, $data)
    {
        $vlog = new mLog();
        $vlog->name = $data['name'];
        $vlog->author = $shop[0]->user_id;
        $vlog->description = $data['description'];
        $vlog->shop_id = $shop[0]->shop_id;
        $vlog->save();

        return true;
    }
}