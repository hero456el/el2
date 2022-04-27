<?php

namespace App\Http\Controllers;

use DB;
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



class cinnamonPatrolController extends Controller
{
    // ■■■■■フロント■■■■■
    // top画面
    public function cpTop(Request $request){
//        $a = cinnamonPatrol::akiDai(1);
//        return view ('test', ['test' => $a, 'test2' => "sss"]);




        // パトロールデータ
        $floor = cinnamonPatrol::cpGet();

        // 着席ボーイ
        $go = account::goGet();

        // SID
        $sid = account::sidGet();

        // goStop
        $goStop = cinnamonPatrol::goStop('now');

        //seika
        $seika =  cinnamonPatrol::seika();

        $ima = floor::ima();
        $global = config('global');
        $addMachineId = ((12-1)*40) + $global["toMachineId"][3];
        $daiban = 100530441 - $addMachineId;
        $test = $daiban;



        return view ('cinnamonPatrol', [
            'floor' => $floor,
            'go' => $go,
            'sid' => $sid,
            'goStop' => $goStop,
            'seika' => $seika,
            'test' => $test,

        ]);
    }

    public function cpTop2(Request $request){
        return view ('cinnamonPatrol2', [
        ]);
    }

    // uid登録
    public function uid(Request $request){
        return view ('uid', ['test' => 'ss', 'test2' => "sss"]);
    }

    // 朝一着席
    public function morningTop(Request $request){
        return view ('test', ['test' => "morning", 'test2' => "sss"]);
    }


    // ■■■■■API■■■■■
    // GoStop切替
    public function goStop(Request $request){
        $go = $request->input('go');
        cinnamonPatrol::goStop($go);
        return back();
    }

    // 指示
    public function cpShiji(Request $request){
        $input = $request->input();
        cinnamonPatrol::cpUpdate($input);
        //return view ('test', ['test1' => $input, 'test2' => "sss"]);
        return back();
    }

    // 着席ボーイを選択
    public function boyGo(Request $request){
        $input = $request->input();
        account::goOn($input);
        return back();
    }

    // 着席ボーイのSID登録
    public function boyInsert(Request $request){
        $input = $request->input();
        // 登録
        account::sidInsert($input);
        return back();
    }

    // 着席ボーイの全SIDチェック
    public function allSidCheck(){
        account::allSidCheck();
        return back();
    }

    // クロール実行
    public function gogo(Request $request){
        $floor = $request->input('f');
        $res = cinnamonPatrol::crawl($floor);
        if(!$res) return false; //お休み中
        return true;
        return json_encode([$res]);
    }

    // 成果表示
    public function seika(){
        $seika = cinnamonPatrol::seika();
        return json_encode($seika);
    }








}




/*
 * 着席ポリシー
 * ①当たり中の台
 * ②空席になった台はすかさずチェック
 * ・連ゾーン
 * ・天井
 * ・VG
 *
 */