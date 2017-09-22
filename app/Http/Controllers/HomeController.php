<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function add()
    {
        if(!(Auth::user()->checkAdmin())){
            return abort(403, 'No Admin');
        }

        return view('add');
    }

    public function list()
    {
        if(!(Auth::user()->checkAdmin())){
            return abort(403, 'No Admin');
        }

        return view('list');
    }

    // Admin
    public function admin(Request $req){
        $code = env('ADM_CODE');
        $input_code = $req->input('k') != null ? $req->input('k') : null;

        if(!isset($input_code))
            return response()->json(['msg' => 'No input Code.'],404);

        if($code != $input_code)
            return response()->json(['msg' => 'No match Code.'],401);

        $adm_data = DB::table('users')->where('adm', 1)->first();

        if(!empty($adm_data))
            return response()->json(['msg' => 'Already Admin.'],401);

        DB::table('users')
            ->update([
                'adm' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('+9 hour')),
            ]);

        return response()->json(['msg' => 'ok'],200);
    }

}
