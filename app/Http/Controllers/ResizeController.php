<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ResizeController extends Controller
{
    function index(Request $req, $size){
        $img = Image::make(file_get_contents(env('LINE_BOT_VOTE_IMG')));

        $img->resize($size,$size);

        if($img->mime() == 'image/png')
            return $img->response('png');
        else if($img->mime() == 'image/jpeg')
            return $img->response('jpg');
        else
            return response()->json(['message' => 'No image format.'],400);
    }
}
