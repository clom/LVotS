<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $req){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }
        // information Data
        $respData = DB::table('vote_info')
            ->select('vote_info.id', 'vote_info.title')
            ->get();

        return response()->json($respData,200);
    }

    public function show(Request $req, $id){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }
        $respData = (object)Array();
        // information Data
        $i_data = DB::table('vote_info')->where('id', $id)->first();
        if(empty($i_data))
            return response()->json(['msg' => 'no Info data.'], 400);

        // Menu Data
        $m_data = DB::table('vote_menu')->where('vote_id', $id)->get();
        if(count($m_data) <= 0)
            return response()->json(['msg' => 'no Menu data.'], 400);

        $respData->info = $i_data;
        $respData->menu = $m_data;

        return response()->json($respData,200);
    }

    public function store(Request $req){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }
        $request_data = $req->json()->all();
        if(empty($request_data)){
            return response()->json(['msg' => 'no Request Data.'], 400);
        }
        // id
        $uuid = str_replace("-", "", Uuid::uuid1()->toString());

        // insert info
        DB::table('vote_info')->insert([
            'id' => $uuid,
            'title' => $request_data['title'],
            'created_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
            'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
        ]);

        // insert menu
        $count = 1;
        foreach($request_data['menu'] as $menu){
            DB::table('vote_menu')->insert([
                'vote_id' => $uuid,
                'no' => $count,
                'text' => $menu['text'],
                'created_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
            ]);
            $count++;
        }

        return response()->json(['id' => $uuid],201);
    }

    public function update(Request $req, $id){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }
        $request_data = $req->json()->all();
        if(empty($request_data)){
            return response()->json(['msg' => 'no Request Data.'], 400);
        }

        $data = DB::table('vote_info')->where('id', $id)->first();
        if(empty($data))
            return response()->json(['msg' => 'No Vote Data.'],400);

        try{
            // Update info
            DB::table('vote_info')
                ->where('id', $id)
                ->update([
                    'title' => isset($request_data['title'])? $request_data['title'] : $data->title,
                    'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
                ]);

            // Del menu
            DB::table('vote_menu')
                ->where('vote_id', $id)
                ->delete();

            // insert menu
            $count = 1;
            foreach($request_data['menu'] as $menu){
                DB::table('vote_menu')->insert([
                    'vote_id' => $id,
                    'no' => $count,
                    'text' => $menu['text'],
                    'created_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
                ]);
                $count++;
            }
        }catch (Exception $ex){
            return response()->json(['message' => 'Server Error'], 500);
        }

        return response()->json([],204);
    }

    public function destroy(Request $req, $id){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }
        $nowId = $this->getVoteID();
        $data = DB::table('vote_info')->where('id', $id)->first();
        if(empty($data))
            return response()->json(['msg' => 'No Vote Data.'],400);

        if($nowId == $id){
            return response()->json(['msg' => 'Now Voting.'],400);
        }

        try{
            // DEL Menu
            DB::table('vote_menu')
                ->where('vote_id', $id)
                ->delete();

            // DEL Ans
            DB::table('vote_ans')
                ->where('vote_id', $id)
                ->delete();

            // DEL Info
            DB::table('vote_info')
                ->where('id', $id)
                ->delete();

        }catch (Exception $ex){
            return response()->json(['message' => 'Server Error'], 500);
        }

        return response()->json([],204);
    }
}
