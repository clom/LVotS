<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Predis\Client;
use Illuminate\Support\Facades\DB;

class SwitchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $req){
        $redis = new Client('tcp://'.env('REDIS_HOST').':'.env('REDIS_PORT'));
        $request_data = $req->json()->all();
        $prefix = Controller::LB_NOWVOTE;
        $id = $request_data['id'];

        if($redis->exists($prefix)){
            if($redis->get($prefix) != $id)
                return response()->json(['msg' => 'Now Voting Service.'],400);
            else {
                // Off Vote
                $redis->del([$prefix]);
                return response()->json(['msg' => 'Off Voting..'],200);
            }
        }
        $data = DB::table('vote_info')->where('id', $id)->first();
        if(empty($data))
            return response()->json(['msg' => 'No Vote Data.'],400);

        // On Vote
        $redis->set($prefix, $request_data['id']);
        return response()->json(['msg' => 'Ready Vote.'],200);

    }

    public function adm(Request $req){
        return response()->json([],200);
    }
}
