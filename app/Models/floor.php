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


class floor extends Authenticatable
{
    protected $table = 'floor';
    protected $guarded = [''];
    public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    //現在の情報
    public static function ima(){
        $G = (int)date("G");
        $ima = [];

        //現在時刻よりホールID取得
        $ima["hall_id"] = ($G>9 && $G<22)? "100100002":"100100003";
        $ima["hall"] = ($G>9 && $G<22)? "2":"3";

        //現在時刻より営業日取得
        $date = date("Y-m-d");
        if($G<10) $date = date("Y-m-d", strtotime("-1 day"));
        $ima["date"] = $date;

        //1営業日前
        $date1 = date("Y-m-d", strtotime("-1 day"));
        if($G<10) $date1 = date("Y-m-d", strtotime("-2 day"));
        $ima["date1"] = $date1;

        //2営業日前
        $date2 = date("Y-m-d", strtotime("-2 day"));
        if($G<10) $date2 = date("Y-m-d", strtotime("-3 day"));
        $ima["date2"] = $date2;

        return $ima;
    }


    //最新のフロア情報取得
    public static function floorData(){

        $ima = floor::ima(); //現時刻データ
        $kisyuID = kisyu::kisyuID(); //機種IDリスト

        //本日のデータがDBにあるか確認
        $floor = floor::where('hall', $ima["hall"])
            ->where('date', $ima["date"])
            ->orderBy('floor', 'asc')
            ->get();

        //なければapiから取得して挿入
        if($floor->isEmpty() || $floor->count() != $floor->first()->total){
            //被らいないようDB削除
            floor::where('hall', $ima["hall"])
            ->where('date', $ima["date"])
            ->delete();

            //API
            $response = Http::withHeaders(config('global.apiHead'))
                ->post(config('global.url_hall').$ima["hall_id"]);
            if(!isset($response['body']['floor'])) return false; //apiの戻りが空ならエラー
            $res = $response['body']['floor'];


            //DBインサート
            $total = count($res);
            foreach($res as $r){
                //新機種なら登録
                if(!array_key_exists($r['machine_name_ja'],$kisyuID)){
                    $kisyuID = kisyu::kisyuID($r);
                }

                //レコード作成
                $floor = new floor();
                $floor->hall = $ima["hall"];
                $floor->floor = $r["floor"];
                $floor->date = $ima["date"];
                $floor->kisyu = $kisyuID[$r['machine_name_ja']];
                $floor->rate = $r['rate'];
                $floor->kankin = $r['exchange_rate'];
                $floor->total = $total;
                $floor->save();
            }

             //再びDBから取得
             $floor = floor::where('hall', $ima["hall"])
                 ->where('date', $ima["date"])
                 ->orderBy('floor', 'asc')
                 ->get();

            //ホール作成
             $hall = hall::where(['hall'=>$ima["hall"],'date'=>$ima["date"]])->first();
             if(!$hall){
                 $hall = new hall(["hall"=>$ima["hall"], "date"=>$ima["date"]]);
                 $hall->save();
             }
        }
        return $floor;
    }

    //フロア情報詳細
    public static function floorDetail($floor){
        foreach($floor as $f){
            //経過時間
            if($f["lastUpdate"]){
                $diff = time() - strtotime($f["lastUpdate"]);
                $hours = floor($diff / 3600); //時間
                $minutes = floor(($diff / 60) % 60); //分
                $min = floor($diff / 60); //分
                //$seconds = floor($diff % 60); //秒
                //$hms = sprintf("%2d:%02d:%02d", $hours, $minutes, $seconds);
                $hms = sprintf("%2d:%02d", $hours, $minutes);
                //$f->updateDiff = $hms;
                $f->updateDiff = $min;
            }else{
                $f->updateDiff = "-";
            }

            //機種
            $kisyu = kisyu::find($f["kisyu"],["type","name", "oneK"])->toArray();
            $f->kisyuName = $kisyu["name"];
            $f->mai = $kisyu["type"]==1? "枚": "玉";
            $f->slo = $kisyu["type"]==1? "スロ": "パチ";
            $f->urlDate = str_replace("-", "", $f['date']);

            //割計算
            $oneKW = $kisyu["oneK"];
            if($oneKW==0) $oneKW=30;
            $totalSpinW = $f->totalSpin;
            $rateW = $f->rate;
            $dedamaW = $f->dedama;
            $wariW = 0;
            $toushi = $totalSpinW/$oneKW*1000/$rateW;
            $wariW = 0;
            if($toushi) $wariW = (round((($toushi + $dedamaW) / $toushi)*1000))/10;
            if($wariW<0) $wariW = 0;
            $f->wari = $wariW;
        }
        return true;
    }

    //フロア情報取得
    public static function floorList($hall=null, $date=null){
        //ホールと日付が空なら現在
        if(!$hall && !$date){
            $ima = floor::ima();
            $hall = $ima["hall"];
            $date = $ima["date"];
        }

        //フロア取得
        $floorList = floor::where(['hall'=>$hall,'date'=>$date])->get();

        if($floorList->isEmpty()) return false;
        else return $floorList;
    }
/*
    //veryGoodをカウントし直し
    public static function vgCount(){
        $ima = floor::ima(); //現時刻データ

        //floor一覧
        $floor = floor::where('hall', $ima["hall"])
        ->where('date', $ima["date"])
        ->orderBy('floor', 'asc')
        ->get();

        foreach($floor as $f){
            //台一覧
            $daiList = dai::daiList($f)
            foreach($daiList as $d){

            }


        }

        //フロア更新時間タッチ
        $now = date("Y-m-d H:i:s", time());
        $f->lastUpdate = $now;
        $f->totalSpin = $spinSum;
        $f->dedama = $dedamaSum;
        $f->syushi = $syushiSum;
        $f->veryGood = $Vgood;
        $f->good = $good;
        $f->save();
        //            if($index==3) return $f;
        if(!$notYesterday){
            $floor1[$index]->totalSpin = $spinSum1;
            $floor1[$index]->dedama = $dedamaSum1;
            $floor1[$index]->syushi = $syushiSum1;
            $floor1[$index]->veryGood = $Vgood1;
            $floor1[$index]->good = $good1;
            $floor1[$index]->save();
            if(!$notYesterday2){
                $floor2[$index]->totalSpin = $spinSum2;
                $floor2[$index]->dedama = $dedamaSum2;
                $floor2[$index]->syushi = $syushiSum2;
                $floor2[$index]->veryGood = $Vgood2;
                $floor2[$index]->good = $good2;
                $floor2[$index]->save();
            }
        }
    }
*/
}

/*

 array(25) {
    ["id"]=>    int(100300001)
    ["mst_hall_id"]=>    int(100100002)
    ["name_ja"]=>    string(0) ""
    ["name_en"]=>    string(0) ""
    ["name_ko"]=>    string(0) ""
    ["name_zh"]=>    string(0) ""
    ["floor"]=>    int(1)
    ["rate"]=>    int(1)
    ["exchange_rate"]=>    int(750)
    ["machine_url"]=>    string(57) "https://cosmoattack.el-drado.com/machine/cosmoattack/boot"
    ["player"]=>    int(0)
    ["machine_name_ja"]=>    string(11) "COSMOAttack"
    ["machine_name_en"]=>    string(11) "COSMOAttack"
    ["machine_name_ko"]=>    string(11) "COSMOAttack"
    ["machine_name_zh"]=>    string(11) "COSMOAttack"
    ["machine_type"]=>    int(2)
    ["mst_app_id"]=>    int(400100001)
    ["detail_url"]=>    string(50) "https://cosmoattack.el-drado.com/info/cosmoattack/"
    ["machine"]=>    int(40)

    ["is_closed"]=>    bool(false)
    ["information_ja"]=>    string(0) ""
    ["information_en"]=>    string(0) ""
    ["information_ko"]=>    string(0) ""
    ["information_zh"]=>    string(0) ""
    ["need_item_id"]=>    int(0)
}





*/