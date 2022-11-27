<?php

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\dai;
use App\Models\floor;
use App\Models\kisyu;
use App\Models\hall;


class hall extends Authenticatable
{
    protected $table = 'hall';
    protected $guarded = [''];
    public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    //hallリスト
    public static function hallList(){
        //日付リスト
        $dateList = hall::select('date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();

        $hallList = [];
        foreach($dateList as $index => $d){
            $hallList[$index]["2"] = hall::where(['date'=> $d, 'hall'=>2])->first();
            if($hallList[$index]["2"]){
                $hallList[$index]["2"] = $hallList[$index]["2"]->toArray();
                $hallList[$index]["2"]["urlDate"] = str_replace("-", "", $d['date']);
            }

            $hallList[$index]["3"] = hall::where(['date'=> $d, 'hall'=>3])->first();
            if($hallList[$index]["3"]){
                $hallList[$index]["3"] = $hallList[$index]["3"]->toArray();
                $hallList[$index]["3"]["urlDate"] = str_replace("-", "", $d['date']);
            }
/*
            $hallList[$index]["2"] = hall::where(['date'=> $d, 'hall'=>2])->first();
            $hallList[$index]["3"] = hall::where(['date'=> $d, 'hall'=>3])->first();
            $hallList[$index]["2"]->urlDate = str_replace("-", "", $d['date']);
            $hallList[$index]["3"]->urlDate = str_replace("-", "", $d['date']);
            */
        }

        return $hallList;

    }

    //ホール情報取得
    public static function hallGet($hall=null, $date=null){
        //ホールと日付が空なら現在
        if(!$hall && !$date){
            $ima = floor::ima();
            $hall = $ima["hall"];
            $date = $ima["date"];
        }

        $hall = hall::where(['hall'=>$hall,'date'=>$date])->first();
        return $hall;
    }

    //前日と翌日のリンク
    public static function hallLink($hall, $date){
        $temp = str_split($date, 4);
        $temp1 = str_split($temp[1], 2);
        $day = $temp[0].'-'.$temp1[0].'-'.$temp1[1];

        $nextDay = '';
        $next = hall::where(['hall'=>$hall])
            ->where('date', '>', $day)
            ->orderBy('date', 'asc')
            ->first();
        if($next) $nextDay = str_replace('-','',$next->date);

        $beforDay = '';
        $befor = hall::where(['hall'=>$hall])
            ->where('date', '<', $day)
            ->orderBy('date', 'desc')
            ->first();
        if($befor) $beforDay = str_replace('-','',$befor->date);

        return ['nextDay'=>$nextDay, 'beforDay'=>$beforDay];
    }

    //最終更新時刻取得
    public static function koshin(){
        $ima = floor::ima();
        $lastUpdate = floor::where('date', $ima['date'])
            ->where('hall', $ima['hall'])
            ->select('lastUpdate')
            ->orderBy('lastUpdate', 'asc')
            ->first()
            ->toArray();
        $res = $lastUpdate? $lastUpdate['lastUpdate']: '';

        return $res;
    }

    //最新情報反映
    public static function saishin($ima){
        //■本日のホール情報取得
        $hall = hall::where(['hall'=>$ima["hall"],'date'=>$ima["date"]])->first();
        //なかったら作成
        if(!$hall) $hall = new hall(["hall"=>$ima["hall"], "date"=>$ima["date"]]);
        //最新情報取得
        hall::hallCal($hall);
        $hall->save();

        //■前日のホール情報取得
        $hall1 = hall::where(['hall'=>$ima["hall"],'date'=>$ima["date1"]])->first();
        //なかったら作成
        if(!$hall1) $hall1 = new hall(["hall"=>$ima["hall"], "date"=>$ima["date1"]]);
        //最新情報取得
        hall::hallCal($hall1);
        $hall1->complete = 1;
        $hall1->save();

        //■前々日のホール情報取得
        $hall2 = hall::where(['hall'=>$ima["hall"],'date'=>$ima["date2"]])->first();
        //なかったら作成
        if(!$hall2) $hall2 = new hall(["hall"=>$ima["hall"], "date"=>$ima["date2"]]);
        //最新情報取得
        hall::hallCal($hall2);
        $hall2->complete = 1;
        $hall2->save();

        return true;
    }

    //hallリフレッシュ
    public static function hallRefresh(){
        //hallリスト
        $floorList = floor::select('hall','date')
            ->groupBy('hall','date')
            ->orderBy('date', 'asc')
            ->orderBy('hall', 'asc')
            ->get()
            ->toArray();

        //存在確認
        foreach($floorList as $list){
            $hall = hall::where(['hall'=>$list["hall"],'date'=>$list["date"]])->first();
            if($hall){
                if(!$hall->complete){
                    hall::hallCal($hall);
                    $hall->save();
                }
            }else{
                $hall = new hall(["hall"=>$list["hall"], "date"=>$list["date"]]);

                hall::hallCal($hall);
                $hall->save();
            }
        }
        return true;
    }

    // 総回転と総収支を挿入
    public static function hallCal($hall){
        $floorList = floor::where(['hall'=>$hall["hall"],'date'=>$hall["date"]])
            ->select('totalSpin','syushi', 'kisyu', 'dedama', 'rate')
            ->get()
            ->toArray();
        if($floorList){
            $hall->totalSpin = (array_sum(array_column($floorList, 'totalSpin')))/10000;
            $hall->syushi = (array_sum(array_column($floorList, 'syushi')))/10000;

            //割計算
            $toushi = 0;
            $dedama = 0;
            foreach($floorList as $f){
                $oneKW = kisyu::where("id", $f["kisyu"])->value("oneK");
                $totalSpinW = $f["totalSpin"];
                $rateW = $f["rate"];
                $toushi += $totalSpinW/$oneKW*1000/$rateW;
                $dedama += $f["dedama"];
            }
            $wari = 0;
            if($toushi) $wari = (round((($toushi + $dedama) / $toushi)*1000))/10;
            if($wari<0) $wari = 0;
            $hall->wari = $wari;


            //2営業日前以前ならコンプリート
            $G = (int)date("G");
            $date2 = date("Y-m-d", strtotime("-2 day"));
            if($G<10) $date2 = date("Y-m-d", strtotime("-3 day"));

            if(strtotime($hall["date"])<strtotime($date2)){
                $hall->complete = 1;
            }


        }
        return true;
    }



}

















