<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// api
Route::group(['prefix' => 'api'], function () {
    Route::resource('/adm/vote', 'Adm\VoteController');
    Route::put('/adm/switch/vote', 'Adm\SwitchController@update');
    Route::put('/adm/switch/adm', 'Adm\SwitchController@adm');

// VoteData
    Route::get('/vote/{id}', 'UserVoteController@index');

});
