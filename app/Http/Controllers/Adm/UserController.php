<?php

namespace App\Http\Controllers\adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $req){
        if(!(Auth::user()->checkAdmin())){
            return response()->json(['msg' => 'No Auth'],403);
        }

        $users = DB::table('users')->select('id', 'name', 'adm')->get();
        return response()->json($users, 200);
    }
}
