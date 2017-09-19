<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

}
