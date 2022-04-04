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

    //■Top画面
    public function now(){
        // データベースの情報更新
        kisyu::dbInsert();

        // フロアデータ取得
        $floor = floor::floorData();
        floor::floorDetail($floor); //詳細も付ける

        // ホールデータ取得
        $hall = hall::hallGet();

        //台データ取得
        foreach($floor as $f){
            $f->daiList = dai::daiList($f);
        }

        //着席情報
        $sit = dai::sit($floor);
//        return view ('test', ['test1' => $sit, 'test2' => "sss"]);

        return view('top', [
            'floor' => $floor,
            'hall' => $hall,
        ]);
    }

    //■ホールリスト
    public function list(){
        $hallList = hall::hallList();

        return view ('list', ['hallList' => $hallList, 'test2' => "ss"]);
    }

    //■ホール詳細
    public function hall($date, $hall){
//        return view ('test', ['test1' => $date, 'test2' => "sss"]);
        // フロアデータ取得
        $floor = floor::floorList($hall, $date);
        floor::floorDetail($floor); //詳細も付ける

        // ホールデータ取得
        $hall = hall::hallGet($hall, $date);

        return view('hall', [
            'floor' => $floor,
            'hall' => $hall,
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

//        return view ('test', ['test1' => $daiList, 'test2' => "sss"]);
        return view ('floor', ['floor' => $floor, 'daiList' => $daiList]);
    }

    //■台詳細
    public function dai($date, $hall, $floor, $dai){
        return view ('commingsoon', ['test1' => "", 'test2' => "sss"]);
    }


}
