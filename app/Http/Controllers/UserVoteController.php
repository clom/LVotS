<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserVoteController extends Controller
{
    public function index(Request $req)
    {
        // information Data
        $respData = DB::table('vote_info')
            ->join('vote_ans', 'vote_info.id', '=', 'vote_ans.vote_id')
            ->groupBy('vote_ans.vote_id')
            ->select('vote_info.id', 'vote_info.title', DB::raw("COUNT(*) as vote_count"))
            ->get();

        return response()->json($respData,200);
    }

    public function show(Request $req, $id)
    {
        $respData = (object)Array();
        // information Data
        $i_data = DB::table('vote_info')->where('id', $id)->select('id', 'title')->first();
        if (empty($i_data))
            return response()->json(['msg' => 'no Info data.'], 404);

        // Menu Data
        $m_data = DB::table('vote_menu')->where('vote_id', $id)->select('no', 'text')->get();
        if (empty($m_data))
            return response()->json(['msg' => 'no Menu data.'], 404);
        foreach ($m_data as $menu){
            $ans_data = DB::table('vote_ans')->where('vote_id', $id)->where('no', $menu->no)->count();
            if(empty($ans_data))
                $menu->vote_count = 0;
            $menu->vote_count = $ans_data;
        }
        $respData->ansCount = DB::table('vote_ans')->where('vote_id', $id)->count();
        $respData->info = $i_data;
        $respData->menu = $m_data;

        return response()->json($respData, 200);
    }

    public function info(Request $req)
    {
        $respData = $this->voteInformation();
        if (empty($respData))
            return response()->json(['msg' => 'now voting NO DATA.'], 404);;

        return response()->json($respData, 200);
    }


}
