<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function view($id)
    {
        return view('view', ['id' => $id]);
    }

    public function index()
    {
        return view('index');
    }
}
