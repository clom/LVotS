<?php

namespace App\Http\Controllers\Adm;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Predis\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SwitchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $req){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }
        $redis = new Client('tcp://'.env('REDIS_HOST').':'.env('REDIS_PORT'));
        $redis->auth(env('REDIS_PASSWORD'));
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
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }

        $request_data = $req->json()->all();

        $user = DB::table('users')->where('id', $request_data['id'])->first();
        if(empty($user)){
            return response()->json(['msg' => 'no User data.'], 400);
        }

        if($user->adm == 1)
            $adm_flag = 0;
        else
            $adm_flag = 1;

        try{
            DB::table('users')
                ->where('id', $request_data['id'])
                ->update([
                    'adm' => $adm_flag,
                    'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
                ]);
        } catch (Exception $ex){
            return response()->json(['message' => 'Server Error'], 500);
        }

        return response()->json([],204);
    }
}
