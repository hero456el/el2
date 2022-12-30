<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use App\Models\dai;
use App\Models\floor;
use App\Models\kisyu;
use App\Models\hall;
use App\Models\account;
use App\Models\cinnamonPatrol;
use App\Models\ir;



class TopController extends Controller
{
    //■API
    public function apidataget(Request $request){
        $floor = $request->get('f');
        $line = $request->get('l');
        dai::daiGetAPI($floor, $line);
//        if($line) floor::vgCount();
        return true;
    }

    public function apiPlayNow(){
        return true;
    }

    public function apiMatome(Request $request){
        return true;
    }

    //APIたたく
    public function dataget(Request $request){
        $floor = $request->get('f');
        $time = dai::daiGetAPI($floor);
        //        return view ('test', ['test1' => $time, 'test2' => "sss"]);
        return back();
    }

    //ホールリフレッシュ
    public function hallRefresh(){
        $t = hall::hallRefresh();
        return back();
    }

    //sit
    public function sit(){
        //$test = dai::sitdown(5,28);
        $test = 'ss';
        return view ('sit', ['test' => $test, 'test2' => "sss"]);
    }

    public function ir(Request $request){
        //削除
        $delId = $request->input('del');
        if($delId) ir::del($delId);

        //チェック
        if($request->input('check')) ir::APItest();
        
        //登録
        $ir = $request->input('ir');
        if($ir) ir::insert($ir);

        //クッキー
        // $cookie = $request->cookie();

        $irList = ir::irList();
        return view ('ir', [
            'irList' => $irList,
        ]);
    }

    //■Top画面
    public function now(){
        Log::info("topへアクセス！");

        // return view ('test', ['test1' => $a, 'test2' => "sss"]);


        // データベースの情報更新
        kisyu::dbInsert();

        // フロアデータ取得
        $floor = floor::floorData();
        // return view ('test', ['test1' => "res", 'test2' => "sss"]);
        floor::floorDetail($floor); //詳細も付ける

        // ホールデータ取得
        $hall = hall::hallGet();
        $lastUpdate = hall::koshin();

        //台データ取得
        foreach($floor as $f){
            $f->daiList = dai::daiList($f);
        }

        //着席情報
        $pinch = dai::sitData($floor);
        if($pinch=='oyasumi') return view('oyasumi', []);
//        return view ('test', ['test1' => $sit, 'test2' => "sss"]);

        //台データ取得
        foreach($floor as $f){
            $f->daiList = dai::daiList($f);
        }

        //dull
        $dull =  cinnamonPatrol::dull();

        return view('top', [
            'page' => 'top',
            'floor' => $floor,
            'hall' => $hall,
            'lastUpdate' => $lastUpdate,
            'pinch' => $pinch,
            'dull' => $dull,
        ]);
    }

    //■ホールリスト
    public function list(){
        $hallList = hall::hallList();

        return view ('list', [
            'page' => 'hall',
            'hallList' => $hallList,
            'test2' => "ss",

        ]);
    }

    //■ホール詳細
    public function hall($date, $hall){
//        return view ('test', ['test1' => $date, 'test2' => "sss"]);
        // フロアデータ取得
        $floor = floor::floorList($hall, $date);
        floor::floorDetail($floor); //詳細も付ける

        // ホールデータ取得
        $hall = hall::hallGet($hall, $date);
        $hallLink = hall::hallLink($hall->hall, $date);

        //台データ取得
        foreach($floor as $f){
            $f->daiList = dai::daiList($f);
        }

        return view('hall', [
            'floor' => $floor,
            'hall' => $hall,
            'hallLink' => $hallLink,
        ]);
    }

    //■フロア詳細
    public function floor($date, $hall, $floor){

        //フロアデータ取得
        $floorList = floor::where(["date"=>$date, "hall"=>$hall, "floor"=>$floor])->get();

        floor::floorDetail($floorList); //詳細も付ける
        $floor = $floorList->first();


        //台データ取得
        $daiList = dai::daiList($floor);

        // ホールデータ取得
        $hall = hall::hallGet($hall, $date);
        $hallLink = hall::hallLink($hall->hall, $date);

        return view ('floor', [
            'floor' => $floor,
            'daiList' => $daiList,
            'hall' => $hall,
            'hallLink' => $hallLink,
        ]);
    }

    //■台詳細
    public function dai($date, $hall, $floor, $dai){
        return view ('commingsoon', ['test1' => "", 'test2' => "sss"]);
    }

    //■ログ
    public function log(){
        //$res = cinnamonPatrol::sendLine('バーベキュー楽しみだね。台が取れたらここから連絡するよ。');
        $res = cinnamonPatrol::dAttack();

        return view ('test', ['test1' => $res, 'test2' => "sss"]);
    }


}
