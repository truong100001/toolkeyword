<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getKeyWord()
    {
        $data = DB::table('keyword_in')->where('check_kw',0)->paginate(10);
        return response()->json($data->items(),200,[],JSON_UNESCAPED_UNICODE);
    }
}
