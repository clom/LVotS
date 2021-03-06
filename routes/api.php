<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// must Auth.
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// LineBot
Route::get('/{id}/resize/{size}', 'ResizeController@index');
Route::post('/callback', 'CallbackController@index');
Route::get('/nowvote', 'UserVoteController@info');
