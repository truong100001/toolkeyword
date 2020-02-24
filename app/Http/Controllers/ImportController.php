<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function readFileExecl(Request $request)
    {
        $file = $request->file('file');
        $name_file = rand(10000,999999).$file->getClientOriginalName();

        $path = public_path('keyword');

        $file->move($path,$name_file);

        $path = public_path('keyword\\'.$name_file);

        $rows = Excel::load($path,function ($reader){})->toArray();

        foreach ($rows as $columns)
        {
            $str = $columns['domain'];

            if(strpos($str,'?id='))
            {
                $str = str_replace('https://','',$str);
                $str = str_replace('?id=','/',$str);
                $arr_str = explode('/',$str);
                $domain = $arr_str[0];
            }
            else
            {
                $str = str_replace('https://','',$str);
                $arr_str = explode('/',$str);
                $domain = $arr_str[0];
            }

            DB::table('keyword_in')->insert([
                'keyword' =>$columns['keyword'],
                'domain' => $domain,
                'type' => $request->type,
                'syntax' => $request->syntax,
                'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                'updated_at' => Carbon::now('Asia/Ho_Chi_Minh'),
            ]);
        }
        var_dump('success');
    }
}
