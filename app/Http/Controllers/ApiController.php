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



class ApiController extends Controller
{
    //■API
    public function apidataget(Request $request){
        $floor = $request->get('f');
        $line = $request->get('l');
        dai::daiGetAPI($floor, $line);
//        if($line) floor::vgCount();
        return true;
    }

    /*
     * playDBクリア
     * 各フロアを叩いてtempDB作成
     * テーブルを返す
     * playDBクリア
     */
    public function apiPlayNow(){
        $message = ['message'=>'apiPlayNowOk!!'];
        $message = ['message'=>'sound'];
        $json = json_encode($message);
        return $json;
    }

    public function apiMatome(Request $request){
        return true;
    }





}
