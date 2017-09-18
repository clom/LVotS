<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ViewController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function info()
    {
        return view('admin');
    }

    public function edit($id)
    {
        return view('edit', ['id' => $id]);
    }
}
