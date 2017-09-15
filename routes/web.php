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

Auth::routes();

Route::get('/', 'GuestController@index')->name('index');
Route::get('/list', 'HomeController@list')->name('list');
Route::get('/admin', 'Adm\ViewController@info')->name('adm');
Route::get('/add', 'HomeController@add')->name('add');
Route::get('/view/{id}', 'GuestController@view');
//Route::get();

// api
Route::group(['prefix' => 'api'], function () {
    Route::resource('/adm/vote', 'Adm\VoteController');
    Route::put('/adm/switch/vote', 'Adm\SwitchController@update');
    Route::put('/adm/switch/adm', 'Adm\SwitchController@adm');

// VoteData
    Route::get('/vote/{id}', 'UserVoteController@index');

});
