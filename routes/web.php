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



// line webhook受取用
Route::any('/line/callback',    'App\Http\Controllers\LineApiController@postWebhook');
// line メッセージ送信用
Route::get('/line/message/send', 'App\Http\Controllers\LineApiController@sendMessage');

Route::middleware(['auth:sanctum', 'verified'])->any('/ir', "App\Http\Controllers\TopController@ir");

//Route::any('/', "App\Http\Controllers\TopController@now");
Route::middleware(['auth:sanctum', 'verified'])->any('/', "App\Http\Controllers\TopController@now");
Route::middleware(['auth:sanctum', 'verified'])->any('/list', "App\Http\Controllers\TopController@list");
Route::middleware(['auth:sanctum', 'verified'])->any('/{date}/{hall}', "App\Http\Controllers\TopController@hall");
Route::middleware(['auth:sanctum', 'verified'])->any('/{date}/{hall}/{floor}', "App\Http\Controllers\TopController@floor");
Route::middleware(['auth:sanctum', 'verified'])->any('/{date}/{hall}/{floor}/{dai}', "App\Http\Controllers\TopController@dai");
//Route::middleware(['auth:sanctum', 'verified'])->any('/project', "ProjectController@projectList");

Route::middleware(['auth:sanctum', 'verified'])->any('/sit', "App\Http\Controllers\TopController@sit");

//シナモンパトロール
Route::middleware(['auth:sanctum', 'verified'])->any('/cinnamonPatrol', "App\Http\Controllers\cinnamonPatrolController@cpTop");
Route::middleware(['auth:sanctum', 'verified'])->any('/cinnamonPatrol2', "App\Http\Controllers\cinnamonPatrolController@cpTop2");
Route::middleware(['auth:sanctum', 'verified'])->any('/uid', "App\Http\Controllers\cinnamonPatrolController@uid");
Route::middleware(['auth:sanctum', 'verified'])->any('/boyInsert', "App\Http\Controllers\cinnamonPatrolController@boyInsert");
Route::middleware(['auth:sanctum', 'verified'])->any('/boyGo', "App\Http\Controllers\cinnamonPatrolController@boyGo");
Route::middleware(['auth:sanctum', 'verified'])->any('/cpShiji', "App\Http\Controllers\cinnamonPatrolController@cpShiji");
Route::middleware(['auth:sanctum', 'verified'])->any('/goStop', "App\Http\Controllers\cinnamonPatrolController@goStop");
Route::middleware(['auth:sanctum', 'verified'])->any('/allSidCheck', "App\Http\Controllers\cinnamonPatrolController@allSidCheck");
Route::middleware(['auth:sanctum', 'verified'])->any('/gogo', "App\Http\Controllers\cinnamonPatrolController@gogo");
Route::middleware(['auth:sanctum', 'verified'])->any('/seika', "App\Http\Controllers\cinnamonPatrolController@seika");
Route::middleware(['auth:sanctum', 'verified'])->any('/uid', "App\Http\Controllers\cinnamonPatrolController@uid");
Route::middleware(['auth:sanctum', 'verified'])->any('/dull', "App\Http\Controllers\cinnamonPatrolController@dull");
Route::middleware(['auth:sanctum', 'verified'])->any('/dAttack', "App\Http\Controllers\cinnamonPatrolController@dAttack");


//収支
Route::middleware(['auth:sanctum', 'verified'])->any('/syushi', "App\Http\Controllers\cinnamonPatrolController@syushi");

//ログ
Route::middleware(['auth:sanctum', 'verified'])->any('/log', "App\Http\Controllers\TopController@log");

//マニュアル
Route::middleware(['auth:sanctum', 'verified'])->any('/manual', "App\Http\Controllers\TopController@manual");
Route::middleware(['auth:sanctum', 'verified'])->any('/playtest', "App\Http\Controllers\TopController@playtest");


//現在の台情報取得
Route::any('/dataget', "App\Http\Controllers\TopController@dataget");
//ホールリフレッシュ
Route::any('/hallRefresh', "App\Http\Controllers\TopController@hallRefresh");

//api
Route::middleware(['auth:sanctum', 'verified'])->any('/apidataget', "App\Http\Controllers\ApiController@apidataget");
Route::middleware(['auth:sanctum', 'verified'])->any('/apiMatome', "App\Http\Controllers\ApiController@apiMatome");
Route::middleware(['auth:sanctum', 'verified'])->any('/apiPlayNow', "App\Http\Controllers\ApiController@apiPlayNow");

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



/*
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::any('/', "App\Http\Controllers\TopController@now");
});

*/



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