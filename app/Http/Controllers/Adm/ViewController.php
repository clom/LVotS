<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ViewController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function info()
    {
        if(!(Auth::user()->checkAdmin())){
            return abort(403, 'No Admin');
        }

        return view('admin');
    }

    public function edit($id)
    {
        if(!(Auth::user()->checkAdmin())){
            return abort(403, 'No Admin');
        }

        return view('edit', ['id' => $id]);
    }
}
