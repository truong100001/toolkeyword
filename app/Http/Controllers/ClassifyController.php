<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassifyController extends Controller
{
    public function __construct()
    {

    }

    public function setStreamKWPlanner(Request $request)
    {
         $pagination = DB::table('keyword_in')->where('check_kw',0)->paginate(10);

         $lastPage = $pagination->lastPage();
         $stream  = $request->stream;

         $perpage = ceil($lastPage/$stream);

         $page_from = 1;
         for($i = 1; $i <= $stream; $i++)
         {
            if($i == $stream)
                $page_to = $lastPage;
            else
                $page_to = $perpage*$i;

            DB::table('index_kw')->where('id',$i)->update([
                'page_from' => $page_from,
                'page_to' => $page_to
            ]);

            $page_from = $page_to;
         }


         for($i = $stream+1; $i <= 10; $i++)
         {
             DB::table('index_kw')->where('id',$i)->update([
                 'page_from' => 0,
                 'page_to' => 0
             ]);
         }
    }


    public function getStreamKWPlanner()
    {
        $stream = DB::table('index_kw')->where('page_to','!=',0)->get();
        return response()->json($stream,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function setStreamSearchResult(Request $request)
    {
        $listKeyword = DB::table('keyword_out')->where('check_gg',0)->get();

        $total = count($listKeyword);
        $stream = $request->stream;

        $perpage = ceil($total/$stream);

        $index_from = 0;
        for($i = 1; $i <= $stream; $i++)
        {
            if($i == $stream)
                $index_to = $total;
            else
                $index_to = $perpage*$i;

            DB::table('index_sr')->where('id',$i)->update([
                'index_from' => $index_from,
                'index_to' => $index_to
            ]);

            $index_from = $index_to;
        }

        for($i = $stream+1; $i <= 10; $i++)
        {
            DB::table('index_sr')->where('id',$i)->update([
                'index_from' => 0,
                'index_to' => 0
            ]);
        }
    }

    public function getStreamSearchResult()
    {
        $stream = DB::table('index_sr')->where('index_to','!=',0)->get();
        return response()->json($stream,200,[],JSON_UNESCAPED_UNICODE);
    }
}
