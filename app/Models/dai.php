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

class dai extends Authenticatable
{
    protected $table = 'dai';
    protected $guarded = [''];
    public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    // APIから台データ取得
    public static function daiGetAPI($oneFloor = null, $line=null)
    {
        set_time_limit(8000);
        $start = time(); //時間計測
        $ima = floor::ima(); //現時刻のデータ

        //現時刻のフロア
        $floor = floor::floorData();

        //前日のフロア
        $floor1 = floor::where('hall', $ima["hall"])
            ->where('date', $ima["date1"])
            ->orderBy('floor', 'asc')
            ->get();
        if($floor1->isEmpty()){
            foreach($floor as $f){
                $f1 = new floor();
                $f1->hall = $f["hall"];
                $f1->floor = $f["floor"];
                $f1->date = $ima["date1"];
                $f1->rate = $f["rate"];
                $f1->kisyu = $f["kisyu"];
                $f1->kankin = $f["kankin"];
                $f1->save();
            }
            $floor1 = floor::where('hall', $ima["hall"])
            ->where('date', $ima["date1"])
            ->orderBy('floor', 'asc')
            ->get();
        }

        //前々日のフロア
        $floor2 = floor::where('hall', $ima["hall"])
            ->where('date', $ima["date2"])
            ->orderBy('floor', 'asc')
            ->get();
        if($floor2->isEmpty()){
            foreach($floor as $f){
                $f2 = new floor();
                $f2->hall = $f["hall"];
                $f2->floor = $f["floor"];
                $f2->date = $ima["date2"];
                $f2->rate = $f["rate"];
                $f2->kisyu = $f["kisyu"];
                $f2->kankin = $f["kankin"];
                $f2->save();
            }
            $floor2 = floor::where('hall', $ima["hall"])
            ->where('date', $ima["date2"])
            ->orderBy('floor', 'asc')
            ->get();
        }


        //global変数取得
        $global = config('global');

        //フロア指定があればそのフロアだけ
        if($oneFloor) $floor = [$floor[$oneFloor-1]];

        //フロアごと回す
        foreach($floor as $index => $f){
            //url
            $kisyu = kisyu::find($f["kisyu"],["type","tenjyo","renZone","name","kakurituVeryGood","kakurituGood"])->toArray();
            $url = $global["daiApiUrl"][$kisyu["type"]];

            //floorID
            $floor_id = $f["floor"] + $global["toFloorId"][$f["hall"]];

            //machineIDに必要な数字
            $addMachineId = (($f["floor"]-1)*40) + $global["toMachineId"][$f["hall"]];

            //前日データを取得するか
            $notYesterday = 0;
            if($f["lastUpdate"])  $notYesterday = 1;
            if($f["kisyu"]!=$floor1[$index]["kisyu"]) $notYesterday = 1;
            if($f["rate"]!=$floor1[$index]["rate"]) $notYesterday = 1;
            if($f["kankin"]!=$floor1[$index]["kankin"]) $notYesterday = 1;
            $notYesterday2 = 0;
            if($f["kisyu"]!=$floor2[$index]["kisyu"]) $notYesterday2 = 1;
            if($f["rate"]!=$floor2[$index]["rate"]) $notYesterday2 = 1;
            if($f["kankin"]!=$floor2[$index]["kankin"]) $notYesterday2 = 1;
            if($floor1[$index]["lastUpdate"])  $notYesterday2 = 1;


            //台ごと回す
            $spinSum = 0;
            $dedamaSum = 0;
            $syushiSum = 0;
            $spinSum1 = 0;
            $dedamaSum1 = 0;
            $syushiSum1 = 0;
            $spinSum2 = 0;
            $dedamaSum2 = 0;
            $syushiSum2 = 0;
            $good = 0;
            $Vgood = 0;
            $good1 = 0;
            $Vgood1 = 0;
            $good2 = 0;
            $Vgood2 = 0;
            $good_line = $kisyu["kakurituGood"];
            $Vgood_line = $kisyu["kakurituVeryGood"];
            //一列ごと
            $s=1;$e=40;
            if($line){
                if($line==1){$s=1;$e=8;}
                if($line==2){$s=9;$e=16;}
                if($line==3){$s=17;$e=24;}
                if($line==4){$s=25;$e=32;}
                if($line==5){$s=33;$e=40;}
            }
            for($daiban=$s; $daiban<=$e; $daiban++){
                //API
                $machine_id = $daiban + $addMachineId;
                $response = Http::withHeaders($global["apiHead"])
                    ->post($url, [
                        'mst_floor_id' => $floor_id,
                        'mst_hall_id' => $ima["hall_id"],
                        'mst_machine_id' => $machine_id,
                ]);
                if(!isset($response['body'][$global["detail"][$kisyu["type"]]])) return false; //apiの戻りが空ならエラー
                $res = $response['body'][$global["detail"][$kisyu["type"]]]; //これやらないとデータ空

                //■本日データ
                $date = $ima["date"];
                $resData = $res[0];
                $dai = dai::where('hall', $ima["hall"])
                    ->where('floor', $f["floor"])
                    ->where('daiban', $daiban)
                    ->where('date', $date)
                    ->first();
                //DBに無ければインスタンス作成
                if(!$dai){
                    $dai = new dai();
                    $dai->hall = $ima["hall"];
                    $dai->floor = $f["floor"];
                    $dai->daiban = $daiban;
                    $dai->date = $date;
                }
                //APIのデータ格納
                if(!$f["lastUpdate"] || $resData["total_spin_count"] > $dai->totalSpin){
                    dai::resToModel($dai, $resData, $kisyu, $f);
                    $dai->save();
                }
                $spinSum += $dai->totalSpin;
                $dedamaSum += $dai->dedama;
                $syushiSum += $dai->syushi;

                //veryGoodカウント
                if($dai->hatu){
                    $kakuritu = $dai->tujyo/$dai->hatu;
                    if($kakuritu < $Vgood_line){ $Vgood++;}
                    elseif($kakuritu < $good_line){ $good++;}
                }


                //■ 前日データ
                if(!$notYesterday){
                    $date = $ima["date1"];
                    $resData = $res[1];
                    $dai1 = dai::where('hall', $ima["hall"])
                        ->where('floor', $f["floor"])
                        ->where('daiban', $daiban)
                        ->where('date', $date)
                        ->first();
                    //DBに無ければインスタンス作成
                    $daiNew = 0;
                    if(!$dai1){
                        $dai1 = new dai();
                        $dai1->hall = $ima["hall"];
                        $dai1->floor = $f["floor"];
                        $dai1->daiban = $daiban;
                        $dai1->date = $date;
                        $daiNew = 1;
                    }
                    //APIのデータ格納
                    if($daiNew || $resData["total_spin_count"] > $dai1->totalSpin){
                        dai::resToModel($dai1, $resData, $kisyu, $floor1[$index]);
                        $dai1->save();
                    }
                    $spinSum1 += $dai1->totalSpin;
                    $dedamaSum1 += $dai1->dedama;
                    $syushiSum1 += $dai1->syushi;

                    //veryGoodカウント
                    if($dai1->hatu){
                        $kakuritu = $dai1->tujyo/$dai1->hatu;
                        if($kakuritu < $Vgood_line){ $Vgood1++;}
                        elseif($kakuritu < $good_line){ $good1++;}
                    }


                    //■前々日データ
                    if(!$notYesterday2){
                        $date = $ima["date2"];
                        $resData = $res[2];
                        $dai2 = dai::where('hall', $ima["hall"])
                            ->where('floor', $f["floor"])
                            ->where('daiban', $daiban)
                            ->where('date', $date)
                            ->first();
                        //DBに無ければインスタンス作成
                        $daiNew = 0;
                        if(!$dai2){
                            $dai2 = new dai();
                            $dai2->hall = $ima["hall"];
                            $dai2->floor = $f["floor"];
                            $dai2->daiban = $daiban;
                            $dai2->date = $date;
                            $daiNew = 1;
                        }
                        //APIのデータ格納
                        if($daiNew || $resData["total_spin_count"] > $dai2->totalSpin){
                            dai::resToModel($dai2, $resData, $kisyu, $floor2[$index]);
                            $dai2->save();
                        }
                        $spinSum2 += $dai2->totalSpin;
                        $dedamaSum2 += $dai2->dedama;
                        $syushiSum2 += $dai2->syushi;

                        //veryGoodカウント
                        if($dai2->hatu){
                            $kakuritu = $dai2->tujyo/$dai2->hatu;
                            if($kakuritu < $Vgood_line){ $Vgood2++;}
                            elseif($kakuritu < $good_line){ $good2++;}
                        }

                    }
                }
            } //台回し終わり

            if(!$line){
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
        } //フロア回し終わり

        //ホール情報を最新に
        hall::saishin($ima);

        $end = time(); //時間計測
        $diff = date("i:s", $end - $start);
        return $diff;

    }

    // APIデータをモデルに格納
    public static function resToModel($dai, $res, $kisyu, $floor)
    {
        $type = $kisyu["type"];

        //メダルか玉か
        $chip = "";
        if($type==1){$chip="medal";}
        if($type==2){$chip="ball";}

        $dai->totalSpin = $res["total_spin_count"];
        $dai->spin = $res["spin_count"];
        $dai->BB = $type==1? $res["bb_count"]: $res["sf_count"];
        $dai->RB = $type==1? $res["rb_count"]: $res["normal_count"];
        $dai->AT = $type==1? $res["at_count"]: $res["ct_count"];
        $dai->dedama = $res[$chip]; //出玉

        //投資
        $toushi = 0;
        $graph = $res["slump_graph"];
        if(!empty($graph)) $toushi = min($graph)[$chip];
        $dai->toushi = $toushi;


        //収支(円)
        $kaisyu = $dai->dedama - $toushi;
        $syushi = $toushi+($kaisyu*($floor["kankin"]/1000)); //メダル
        $dai->syushi = round($syushi*$floor["rate"]); //円

        $history = array_reverse($res["bonus_history"]); //当たりGの配列の前後入れ替え

        //初当たり
        $tenjyo = $kisyu["tenjyo"];
        $renZone = $kisyu["renZone"];
        $hatu = 0;
        $tujyo = $res["spin_count"]; //現在回転数
        $history = array_reverse($res["bonus_history"]); //当たりGの配列の前後入れ替え

        //■ウィッチローズ ----------------------
        if($kisyu["name"]=="WhichRoses"){
            $befor = 0; //1:BB 2:REG
            $fleeze = 0;
            foreach($history as $b){ // b["type"] 1:BB 2:REG
                //朝一
                if($befor==0){
                    $tujyo += $b["spin"];

                    //バケ後
                }elseif($befor==2){
                    $tujyo += $b["spin"];
                    if($b["type"]==1 && $b["spin"]<85) $hatu++;
                    //BB後
                }elseif($befor==1){
                    if($b["type"]==2 && $b["spin"]>32) $tujyo += $b["spin"]-32;
                }
                if($b["type"] ==1 && $b["spin"]>100) $fleeze++;
                $befor = $b["type"];
            }
            if($befor==1 && $res["spin_count"]>32) $tujyo -= 32;

            $dai->hatu = $hatu;
            $dai->tujyo = $tujyo;


        //■エンジェルローズ ----------------------
        }elseif($kisyu["name"]=="AngelRoses"){
            foreach($history as $b){
                $tujyo += $b["spin"];
                if($b["spin"]<$tenjyo && $b["type"]==1) $hatu++;
            }

        //■ハニー ----------------------
        }elseif($kisyu["name"]=="Honey♡Collection"){
            $spin = $res["spin_count"];
            $tujyo = 0;
            if($spin>38) $tujyo += 3.4;
            if($spin>82) $tujyo += 3.1;
            if($spin>116) $tujyo += 11;
            if($spin>227) $tujyo += 22.9;
            foreach($history as $b){
                if($b["spin"]>38) $tujyo += 3.4;
                if($b["spin"]>82) $tujyo += 3.1;
                if($b["spin"]>116) $tujyo += 11;
                if($b["spin"]>227) $tujyo += 22.9;
                if($b["type"]==1){
                    if($b["spin"]>=33 && $b["spin"]<=38) $hatu++;
                    if($b["spin"]>=77 && $b["spin"]<=82) $hatu++;
                    if($b["spin"]>=111 && $b["spin"]<=116) $hatu++;
                    if($b["spin"]>=222 && $b["spin"]<=227) $hatu++;
                }
            }
            $tujyo = round($tujyo);

            //■一般的な台 ----------------------
        }else{
            foreach($history as $b){
                if($hatu == 0){
                    $tujyo += $b["spin"];
                    if($b["spin"]<$tenjyo) $hatu++;
                }elseif($b["spin"] > ($renZone+1)){
                    $tujyo += ($b["spin"] - $renZone);
                    if($b["spin"]<$tenjyo) $hatu++;
                }
            }
            if($hatu && $tujyo>$renZone) $tujyo -= $renZone;
        }
        //■---------------------------

        $dai->hatu = $hatu;
        $dai->tujyo = $tujyo;

        return true;
    }

    // 台リスト
    public static function daiList($floor)
    {
        //台一覧
        $daiList = dai::where([
            "date" => $floor->date,
            "hall" => $floor->hall,
            "floor" => $floor->floor,
        ])->get();

        //機種情報
        $kisyu = kisyu::where("id", $floor->kisyu)->first()->toArray();

        //台詳細
        foreach($daiList as $d){
            //初当たり確率
            if($d->hatu) $d->kakuritu = round($d->tujyo / $d->hatu);
            else $d->kakuritu = "-";

            //初当たり確率評価
            $d->kakurituHyouka = "";
            if($d->kakuritu > $kisyu["kakurituBad"]) $d->kakurituHyouka = "kakurituBad";
            if($d->kakuritu < $kisyu["kakurituGood"]) $d->kakurituHyouka = "kakurituGood";
            if($d->kakuritu < $kisyu["kakurituVeryGood"]) $d->kakurituHyouka = "kakurituVeryGood";
            if($d->hatu == 0) $d->kakurituHyouka = "";

            //出玉確率評価
            $d->dedamaHyouka = "";
            if($d->dedama < $kisyu["dedamaBad"]) $d->dedamaHyouka = "dedamaBad";
            if($d->dedama > $kisyu["dedamaGood"]) $d->dedamaHyouka = "dedamaGood";
            if($d->dedama > $kisyu["dedamaVeryGood"]) $d->dedamaHyouka = "dedamaVeryGood";
        }
        return $daiList;
    }

    // 台リスト
    public static function daiListHall($hall)
    {

    }



    // 着席データ（引数はdaiList必須）
    public static function sit($floor)
    {
        //現時刻のデータ
        $ima = floor::ima();

        //global変数取得
        $global = config('global');

        //アカウントリスト
        $accountIdList = account::pluck('usr_id')->toArray();
        $accountList = account::pluck('name','usr_id')->toArray();

        //返り値
        $pinch = "";

        foreach($floor as $f){
            //floorID
            $floor_id = $f["floor"] + $global["toFloorId"][$ima["hall"]];

            //API
            $standby = Http::withHeaders($global["apiHead"])
            ->post($global["url_list"], [
                'mst_floor_id' => $floor_id,
                'mst_hall_id' => $ima["hall_id"],
            ]);
            $sit = $standby['body']['machine']; //これやらないとデータ空

            //返り値
            $akiVG = [];
            $akiG = [];
            $dull = [];
            $myplay = [];
            foreach($sit as $i => $s){
                //daiがなければ空のインスタンス
                if(isset($f->daiList[$i])) $dai = $f->daiList[$i];
                else $dai = new dai();

                //myplayとvgを更新
                /*
                if((in_array($s['usr_id'], $accountIdList)) ||
                    ($dai->kakurituHyouka=='kakurituVeryGood')){
                        dai::daiOne($f, $dai);
                        if($dai->hatu) $dai->kakuritu = round($dai->tujyo / $dai->hatu);
                        else $dai->kakuritu = "-";
                }*/
                if((in_array($s['usr_id'], $accountIdList))){
                        dai::daiOne($f, $dai);
                        if($dai->hatu) $dai->kakuritu = round($dai->tujyo / $dai->hatu);
                        else $dai->kakuritu = "-";
                }
                //今打ってる台
                if(in_array($s['usr_id'], $accountIdList)){
                    $dai->EL = $accountList[$s['usr_id']]; //EL
                    $to = $s['time_out']- time(); //タイムアウト
                    if($to<1600) $pinch = "pinch";
                    if($to<60) $dai->time_out = $to.'秒'; //タイムアウト
                    else $dai->time_out = round($to/60).'分'; //タイムアウト
                    $dai->dollar_box = $s['dollar_box']; //持ちメダル
                    $myplay[]=$dai;

                //VG
                }elseif($dai->kakurituHyouka=='kakurituVeryGood'){
                    $to = $s['time_out']- time(); //タイムアウト
                    if($to<60) $dai->time_out = $to.'秒'; //タイムアウト
                    else $dai->time_out = round($to/60).'分'; //タイムアウト
                    $dai->dollar_box = $s['dollar_box']; //持ちメダル

                    if($s['usr_id']) $dull[]=$dai;
                    else $akiVG[]=$dai;

                /*//G
                }elseif($dai->kakurituHyouka=='kakurituGood'){
                    if($s['usr_id']){}
                    else $akiG[]=$dai;*/

                }

            }
            $f->akiVG = $akiVG;
            $f->akiG = $akiG;
            $f->dull = $dull;
            $f->myplay = $myplay;

        }
        return $pinch;

    }



    // 一台更新
    public static function daiOne($f,$dai)
    {
        $ima = floor::ima();
        $global = config('global');
        $daiban = $dai->daiban;
        $addMachineId = (($f["floor"]-1)*40) + $global["toMachineId"][$ima["hall"]];
        $floor_id = $f["floor"] + $global["toFloorId"][$ima["hall"]];
        $kisyu = kisyu::find($f["kisyu"],["type","tenjyo","renZone","name","kakurituVeryGood","kakurituGood"])->toArray();
        $url = $global["daiApiUrl"][$kisyu["type"]];

        //API
        $machine_id = $daiban + $addMachineId;
        $response = Http::withHeaders($global["apiHead"])
        ->post($url, [
            'mst_floor_id' => $floor_id,
            'mst_hall_id' => $ima["hall_id"],
            'mst_machine_id' => $machine_id,
        ]);
        if(!isset($response['body'][$global["detail"][$kisyu["type"]]])) return false; //apiの戻りが空ならエラー
        $res = $response['body'][$global["detail"][$kisyu["type"]]][0]; //これやらないとデータ空

        //dai書き換え
        if(!$dai->hall) $dai->hall = $ima["hall"];
        if(!$dai->floor) $dai->floor = $f["floor"];
        if(!$dai->daiban) $dai->daiban = $daiban;
        if(!$dai->date) $dai->date = $ima["date"];

        //db書き換え
        $newDai = dai::where('hall', $ima["hall"])
            ->where('floor', $f["floor"])
            ->where('daiban', $daiban)
            ->where('date', $ima["date"])
            ->first();
        //DBに無ければインスタンス作成
        if(!$newDai){
            $newDai = new dai();
            $newDai->hall = $ima["hall"];
            $newDai->floor = $f["floor"];
            $newDai->daiban = $daiban;
            $newDai->date = $ima["date"];
        }

        //APIのデータ格納
        if($res["total_spin_count"] > $dai->totalSpin){
            dai::resToModel($dai, $res, $kisyu, $f);
            dai::resToModel($newDai, $res, $kisyu, $f);
            $newDai->save();
        }
    }


    // sitdown
    public static function sitdown()
    {
        $ima = floor::ima();
        $global = config('global');
        $url = $global["sitdownUrl"];
        $floor_id = 11 + $global["toFloorId"][$ima["hall"]];
        $machine_id = '100530422';
/*
        $url = 'https://api.el-drado.com/hall/enterhall';
        $response = Http::withHeaders($global["apiHead"])
        ->post($url, [
            'mst_floor_id' => $floor_id,
            'mst_hall_id' => $ima["hall_id"],
            'mst_machine_id' => $machine_id,
        ]);
        $res = $response['body']; //これやらないとデータ空
*/
        $url = $global["sitdownUrl"];
        $url = 'https://api.el-drado.com/balance/payoff';
        $response = Http::withHeaders($global["apiHead"])
        ->post($url, [
            'mst_floor_id' => $floor_id,
            'mst_hall_id' => $ima["hall_id"],
            'mst_machine_id' => $machine_id,
        ]);
        $res = $response['body']; //これやらないとデータ空


        /*
        // 台辞める
        $url = 'https://api.el-drado.com/balance/payoff';
        // プロフィール
        $url = 'https://api.el-drado.com/profile/mefloor';
        // ログアウト
        $url = 'https://api.el-drado.com/login/logout';
        // ログイン
        $url = 'https://api.el-drado.com/login';

        $response2 = Http::withHeaders($global["apiHead"])
        ->post($url, [
            'code' => '',
            'is_mode_id_passwd' => 'True',
            'lock_tid' => '3541e5f1-a7f8-4a65-b991-e1c6486f9481',
            'login_id' => 'mituru.hamaoka@gmail.com',
            'password' => 'panda-el2',
            'uuid' => '932ea948-bb0e-11ec-bac2-5254001d8a87',
        ]);

        $url = 'https://api.el-drado.com/';
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'log', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'auth/chekip', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'hall/getHallIdList', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'app/getenv', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'header/badge', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'hall/hallBgmList', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'profile/me', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'auth/loginstatus', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'machine/enableButton', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'town/curtain', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'profile/seat', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'profile/me', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'profile/firstLoginCheck', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'hall', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'hall/closedInfo', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'information/new', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'visitpoint', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'profile/seat', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'log', []);
        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'header/badge', []);

        $response = Http::withHeaders($global["apiHead"])
        ->post($url.'profile/me', []);

        /*
        $url = $global["url_list"];
        $response = Http::withHeaders($global["apiHead"])
        ->post($url, [
            'mst_floor_id' => $floor_id,
            'mst_hall_id' => $ima["hall_id"],
//            'mst_machine_id' => $machine_id,
        ]);
*/

        return $response;
    }








}
