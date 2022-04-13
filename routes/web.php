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

//Route::any('/', "App\Http\Controllers\TopController@now");
Route::middleware(['auth:sanctum', 'verified'])->any('/', "App\Http\Controllers\TopController@now");
Route::middleware(['auth:sanctum', 'verified'])->any('/list', "App\Http\Controllers\TopController@list");
Route::middleware(['auth:sanctum', 'verified'])->any('/{date}/{hall}', "App\Http\Controllers\TopController@hall");
Route::middleware(['auth:sanctum', 'verified'])->any('/{date}/{hall}/{floor}', "App\Http\Controllers\TopController@floor");
Route::middleware(['auth:sanctum', 'verified'])->any('/{date}/{hall}/{floor}/{dai}', "App\Http\Controllers\TopController@dai");
//Route::middleware(['auth:sanctum', 'verified'])->any('/project', "ProjectController@projectList");

Route::middleware(['auth:sanctum', 'verified'])->any('/sit', "App\Http\Controllers\TopController@sit");

//現在の台情報取得
Route::any('/dataget', "App\Http\Controllers\TopController@dataget");
//ホールリフレッシュ
Route::any('/hallRefresh', "App\Http\Controllers\TopController@hallRefresh");

//api
Route::any('/apidataget', "App\Http\Controllers\TopController@apidataget");
Route::any('/apiMatome', "App\Http\Controllers\TopController@apiMatome");

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::any('/', "App\Http\Controllers\TopController@now");
});







/*
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
*/