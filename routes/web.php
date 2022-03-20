<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\UserController;
use App\Http\Controllers\frontend\EmailController;
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


/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::any('/', "App\Http\Controllers\TopController@now");
Route::any('/list', "App\Http\Controllers\TopController@list");
Route::any('/{date}/{hall}', "App\Http\Controllers\TopController@hall");
Route::any('/{date}/{hall}/{floor}', "App\Http\Controllers\TopController@floor");
Route::any('/{date}/{hall}/{floor}/{dai}', "App\Http\Controllers\TopController@dai");

//現在の台情報取得
Route::any('/dataget', "App\Http\Controllers\TopController@dataget");
//ホールリフレッシュ
Route::any('/hallRefresh', "App\Http\Controllers\TopController@hallRefresh");


