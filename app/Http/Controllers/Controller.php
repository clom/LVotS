<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// Redis
use Predis\Client;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const LB_NOWVOTE = 'LB_nowVote';

    public function getVoteID(){
        $redis = new Client('tcp://'.env('REDIS_HOST').':'.env('REDIS_PORT'));
        $redis->auth(env('REDIS_PASSWORD'));
        $prefix = Controller::LB_NOWVOTE;
        if($redis->exists($prefix))
            return $redis->get($prefix);
        else
            return null;
    }


    public function voteInformation(){
        $response = (object)Array();

        $id = $this->getVoteID();

        if($id != null){
            $response->info = DB::table('vote_info')->where('id', $id)->first();
            $response->menu = DB::table('vote_menu')->where('vote_id', $id)->get();
        } else {
            return null;
        }

        return $response;
    }

    public function voteAction($num, $userId){
        $id = $this->getVoteID();

        if($id != null){
            $menuData = DB::table('vote_menu')->where('vote_id', $id)->where('no', $num)->first();
            if(empty($menuData))
                return null;

            DB::table('vote_ans')->insert([
                'vote_id' => $id,
                'user_id' => $userId,
                'no' => $num,
                'created_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
            ]);
            return $menuData->text;
        } else {
            return null;
        }

    }

    public function isVoted($userId){
        $id = $this->getVoteID();

        if($id != null){
            if(DB::table('vote_ans')->where('vote_id', $id)->where('user_id', $userId)->exists()){
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
