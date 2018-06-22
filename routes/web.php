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

Route::resource('xls', 'XlsController');
Route::post('loadfile', 'XlsController@loadFile');
Route::post('store-row', 'XlsController@storeRow');
Route::delete('clearData/{name}', 'XlsController@deleteWholeFile');
