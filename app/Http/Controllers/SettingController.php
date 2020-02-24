<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function setAccount(Request $request)
    {
        DB::table('account')->where('id',1)->update([
            'email' =>  $request->email,
            'password' => $request->pass,
            'user_link' => $request->user_link
        ]);
    }

    public function getAccount()
    {
        $account = DB::table('account')->first();
        return response()->json($account,200);
    }

    //=====================================

    public function setCaptcha(Request $request)
    {
        DB::table('captcha')->where('id',1)->update([
            'api_key' =>  $request->api_key,
            'site_key' => $request->site_key
        ]);
    }
    public function getCaptcha()
    {
        $account = DB::table('captcha')->first();
        return response()->json($account,200);
    }

    //==========================================
    public function setProxy(Request $request)
    {
        $arr_proxy = explode(',',$request->proxy);

        for($i = 0; $i < count($arr_proxy); $i++)
        {
            DB::table('proxy')->where('id',$i+1)->update([
                'proxy' => $arr_proxy[$i]
            ]);
        }

        for($i = 0; $i < count($arr_proxy); $i++)
        {

            DB::table('proxy')->where('id','>',count($arr_proxy))->update([
                'proxy' => 0
            ]);
        }
    }

    public function getProxy()
    {
        $proxy = DB::table('proxy')->where('proxy','!=',0)->where('proxy','!=','')->get();

        $arr = [];
        foreach ($proxy as $value)
        {
            array_push($arr,$value->proxy);
        }


        return response()->json(join(',',$arr),200);
    }

}
