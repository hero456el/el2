<?php

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\dai_data;
use App\Models\floor_info;

class dai_data extends Authenticatable
{
    protected $table = 'dai_data';
    protected $guarded = [''];
    use HasApiTokens, HasFactory, Notifiable;

    // 台データ取得
    public static function daiDataGet($floor = null)
    {
        set_time_limit(8000);

        $headers = [
            'Connection'=> 'keep-alive',
            'sec-ch-ua'=> '" Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
            'DNT'=> '1',
            'sec-ch-ua-mobile'=> '?0',
            'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
            'Content-Type'=> 'application/json;charset=UTF-8',
            'Accept'=> 'application/json, text/plain, */*',
            'ir-ticket'=> '4e33eb8cb59f1f1a74774484cb59ee51ee326004',
            'sec-ch-ua-platform'=> '"Windows"',
            'Origin'=> 'https://el-drado.com',
            'Sec-Fetch-Site'=> 'same-site',
            'Sec-Fetch-Mode'=> 'cors',
            'Sec-Fetch-Dest'=> 'empty',
            'Referer'=> 'https://el-drado.com',
            'Accept-Language'=> 'ja-JP,ja;q=0.9',
        ];
        $error = 0;
        $hall = ((int)date("G")>9 && (int)date("G")<22)?"100100002":"100100003";

        //当日フロアデータ
        $info = floor_info::floorInfoGet();
        $notYesterday = 0;

        //1営業日前データ
        $date1 = date("Y-m-d", strtotime("-1 day"));
        if((int)date("G")<10) $date1 = date("Y-m-d", strtotime("-2 day"));
        $info1 = floor_info::floorInfoGet($date1, $hall);
        if($info != $info1){
            $notYesterday = 1;
            /*
            for($i;$i<count($info);$i++){
                $temp = isset($info1[$i])?$info1[$i]:null;
                $info[$i]["yesterday1"]=$temp;
            }
            */
        }

        //2営業日前データ
        $date2 = date("Y-m-d", strtotime("-2 day"));
        if((int)date("G")<10) $date2 = date("Y-m-d", strtotime("-3 day"));
        $info2 = floor_info::floorInfoGet($date2, $hall);
        if($info != $info2) $info["yesterday2"]=$info2;
        if($info != $info2){
            $notYesterday = 1;
            /*
             * for($i;$i<count($info);$i++){
                $temp = isset($info2[$i])?$info2[$i]:null;
                $info[$i]["yesterday2"]=$temp;
            }
            */
        }

        //フロア指定があればそのフロアだけ
        if($floor) $info = [$info[$floor-1]];

        //各台ごと回す
        foreach($info as $info){
            $daisu=$info["machine"];
            for($daiban=1; $daiban<=$daisu; $daiban++){
                $insert = dai_data::daiInsert($info, $daiban, $notYesterday);
//                if(!$insert) $insert = dai_data::daiInsert($info, $daiban);
//                if(!$insert) $error++;
            }
        }

        return $error;
    }

    //台データインサート
    public static function daiInsert($info, $daiban, $notYesterday){
        $hall = ((int)date("G")>9 && (int)date("G")<22)?"100100002":"100100003";

        $floor = ((int)date("G")>9 && (int)date("G")<22)?"100300000":"100330000";
        $floor += $info["floor"];

        $machine = ((int)date("G")>9 && (int)date("G")<22)?"100500000":"100530000";
        $machine += (($info["floor"]-1)*40) + $daiban;

        $url = "";
        $surl = "https://api.el-drado.com/machine/detailSlot";
        $purl = "https://api.el-drado.com/machine/detailPachinko";
        if($info["machine_type"]==1) $url = $surl;
        elseif($info["machine_type"]==2)$url = $purl;

        $headers = [
            'Connection'=> 'keep-alive',
            'sec-ch-ua'=> '" Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
            'DNT'=> '1',
            'sec-ch-ua-mobile'=> '?0',
            'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
            'Content-Type'=> 'application/json;charset=UTF-8',
            'Accept'=> 'application/json, text/plain, */*',
            'ir-ticket'=> '4e33eb8cb59f1f1a74774484cb59ee51ee326004',
            'sec-ch-ua-platform'=> '"Windows"',
            'Origin'=> 'https://el-drado.com',
            'Sec-Fetch-Site'=> 'same-site',
            'Sec-Fetch-Mode'=> 'cors',
            'Sec-Fetch-Dest'=> 'empty',
            'Referer'=> 'https://el-drado.com',
            'Accept-Language'=> 'ja-JP,ja;q=0.9',
        ];

        //api
        $response = Http::withHeaders($headers)
        ->post($url, [
            'mst_floor_id' => $floor,
            'mst_hall_id' => $hall,
            'mst_machine_id' => $machine,
        ]);
        $response['body']; //これやらないとデータ空
        if(!$response['body']) return false;
        return false;

        $sp = "";
        if(strpos(serialize($response['body']), 'slot_detail')) $sp = 'slot_detail';
        if(strpos(serialize($response['body']), 'pachinko_detail')) $sp = 'pachinko_detail';

        $date0 = date("Y-m-d");
        if((int)date("G")<10) $date0 = date("Y-m-d", strtotime("-1 day"));

        //本日データ
        $daiData = dai_data::where('floor_id', $floor)
            ->where('hall_id', $hall)
            ->where('machine_id', $machine)
            //            ->where('complete','<>', 1)
            ->where('date', $date0)
            ->first();
        if(!$daiData){$daiData = new dai_data();}
        $daiData->floor_id = $floor;
        $daiData->hall_id = $hall;
        $daiData->machine_id = $machine;
        $daiData->date = $date0;
        $daiData->data = serialize($response['body'][$sp][0]);
        $daiData->kisyu = $info["machine_name_ja"];
        $daiData->rate = $info["rate"];
        $daiData->kankin = $info["exchange_rate"];
        $daiData->save();
        if($daiban==40) $daiData->update();



        if(!$daiData->yesterdayOK && !$notYesterday){
            //前日データ
            $date1 = date("Y-m-d", strtotime("-1 day"));
            if((int)date("G")<10) $date1 = date("Y-m-d", strtotime("-2 day"));
            $daiData1 = dai_data::where('floor_id', $floor)
                ->where('hall_id', $hall)
                ->where('machine_id', $machine)
                ->where('date', $date1)
                ->first();
            if(!$daiData1){
                $daiData1 = new dai_data();
                $daiData1->floor_id = $floor;
                $daiData1->hall_id = $hall;
                $daiData1->machine_id = $machine;
                $daiData1->date = $date1;
                $daiData1->data = serialize($response['body'][$sp][1]);
                $daiData1->kisyu = $info["machine_name_ja"];
                $daiData1->rate = $info["rate"];
                $daiData1->kankin = $info["exchange_rate"];
                $daiData1->complete = "1";
                $daiData1->save();
            }elseif(!$daiData1->complete){
                $daiData1->data = serialize($response['body'][$sp][1]);
                $daiData1->complete = "1";
                $daiData1->save();
            }

            //前々日データ
            $date2 = date("Y-m-d", strtotime("-2 day"));
            if((int)date("G")<10) $date2 = date("Y-m-d", strtotime("-3 day"));
            $daiData2 = dai_data::where('floor_id', $floor)
                ->where('hall_id', $hall)
                ->where('machine_id', $machine)
                ->where('date', $date2)
                ->first();
            if(!$daiData2){
                $daiData2 = new dai_data();
                $daiData2->floor_id = $floor;
                $daiData2->hall_id = $hall;
                $daiData2->machine_id = $machine;
                $daiData2->date = $date2;
                $daiData2->data = serialize($response['body'][$sp][2]);
                $daiData2->kisyu = $info["machine_name_ja"];
                $daiData2->rate = $info["rate"];
                $daiData2->kankin = $info["exchange_rate"];
                $daiData2->complete = "1";
                $daiData2->save();
            }elseif(!$daiData2->complete){
                $daiData2->data = serialize($response['body'][$sp][2]);
                $daiData2->complete = "1";
                $daiData2->save();
            }

            $daiData->update(["yesterdayOK" => "1"]);
        }
        return $daiData;

    }

    // 現在のフロアデータを整形して取得
    public static function floorGet($floor)
    {
        $hall = ((int)date("G")>9 && (int)date("G")<22)?"100100002":"100100003";

        $floor_id = ((int)date("G")>9 && (int)date("G")<22)?"100300000":"100330000";
        $floor_id += $floor;

        $date = date("Y-m-d");
        if((int)date("G")<10) $date = date("Y-m-d", strtotime("-1 day"));

        $floorData = dai_data::where('floor_id', $floor_id)
            ->where('hall_id', $hall)
            ->where('date', $date)
            ->orderBy('machine_id', 'asc')
            ->get();
        if(!$floorData) return false;
        $kisyu = $floorData[0]->kisyu;

        //パチかスロか
        $type = 0;
        if(strpos($kisyu,'Rose') !== false) $type=1;
        if(strpos($kisyu,'Rich') !== false) $type=1;
        if(strpos($kisyu,'DREAM') !== false) $type=1;
        if(strpos($kisyu,'Dice') !== false) $type=1;

        if(strpos($kisyu,'Attack') !== false) $type=2;
        if(strpos($kisyu,'mate') !== false) $type=2;
        if(strpos($kisyu,'motion') !== false) $type=2;

        $dedama = 0;
        $totalG = 0;
        $syushi = 0;
        foreach($floorData as $d){
            $d->data = unserialize($d->data);
            $d->floor = (int)substr($d->floor_id, -2);
            $d->machine = (int)substr($d->machine_id, -4) - (($d->floor-1)*40);
            dai_data::arrangeDataIn($d, $type);
            $dedama += $d->dedama;
            $totalG += $d->data["total_spin_count"];
            $syushi += $d->syushi;
        }
        $floorData->dedama = $dedama; //フロア合計出玉
        $floorData->tani = $type==1? "枚": "玉"; //枚か玉か
        $floorData->totalG = $totalG; //フロア合計回転数
        $floorData->wari = 0; //フロア割
        if($type==1) $floorData->wari = (round((($totalG*3 +$dedama)/($totalG*3))*1000))/10;
        if($type==2) $floorData->wari = (round((($totalG*15 + $dedama)/($totalG*15))*1000))/10;
        $floorData->syushi = $syushi; //フロア収支

        return $floorData;
    }

    // 台データ整形
    public static function arrangeDataIn($dai, $type)
    {
        //機種名
        $kisyu = $dai->kisyu;

        //メダルか玉か
        $chip = "";
        if($type==1){$chip="medal";}
        if($type==2){$chip="ball";}
        $chipPrice = $dai->rate;

        //出玉(メダル)
        $dai->dedama = $dai->data[$chip];

        //投資(メダル)
        $dai->toushi = 0;
        $graph = $dai->data["slump_graph"];
        if(!empty($graph)) $dai->toushi = min($graph)[$chip];
//        $dai->toushi = round($dai->toushi*$chipPrice/1000); //円

        //収支(円)
        $dai->kaisyu = $dai->dedama-$dai->toushi;
        $dai->syushi = $dai->toushi+($dai->kaisyu*($dai->kankin/1000)); //メダル
        $dai->syushi = round($dai->syushi*$chipPrice); //円

        // ■■■シークレットローズ■■■
        if($kisyu=="SecretRoses"){
            $tenjyo = 1600;
            $hatu = 0;
            $tujyoG = $dai->data["spin_count"]; //現在回転数
            $history = array_reverse($dai->data["bonus_history"]); //当たりGの配列の前後入れ替え
            foreach($history as $b){
                if($hatu == 0){
                    $tujyoG += $b["spin"];
                    if($b["spin"]<$tenjyo) $hatu++;
                }elseif($b["spin"]>33){
                    $tujyoG += ($b["spin"]-32);
                    if($b["spin"]<$tenjyo) $hatu++;
                }
            }
            if($hatu && $tujyoG>32) $tujyoG-=32;
            $dai->hatu = $hatu;
            $dai->tujyoG = $tujyoG;
            $dai->kakuritu = "-";
            if($hatu && $tujyoG) $dai->kakuritu = round($tujyoG/$hatu);

            //出玉評価
            if($dai->dedama>3999) $dai->dedamaHyouka = "dedamaGood";
            if($dai->dedama>7999) $dai->dedamaHyouka = "dedamaVeryGood";
            if($dai->dedama<0) $dai->dedamaHyouka = "dedamaBad";
            if($dai->dedama==0) $dai->dedamaHyouka = "";
            //初当たり評価
            if($dai->kakuritu<250) $dai->kakurituHyouka = "kakurituGood";
            if($dai->kakuritu<200) $dai->kakurituHyouka = "kakurituVeryGood";
            if($dai->kakuritu>300) $dai->kakurituHyouka = "kakurituBad";
            if($dai->kakuritu=="-") $dai->kakurituHyouka = "";

        // ■■■ウィッチローズ■■■
        }elseif($kisyu=="WhichRoses"){
            $tenjyo = 1600;
            $hatu = 0;
            $tujyoG = $dai->data["spin_count"];//現在回転数
            $history = array_reverse($dai->data["bonus_history"]); //当たりGの配列の前後入れ替え
            $befor = 0; //1:BB 2:REG
            $fleeze = 0;
            foreach($history as $loop=>$b){ // b["type"] 1:BB 2:REG
                //朝一
                if($befor==0){
                    $tujyoG += $b["spin"];

                //バケ後
                }elseif($befor==2){
                    $tujyoG += $b["spin"];
                    if($b["type"]==1 && $b["spin"]<85) $hatu++;
                //BB後
                }elseif($befor==1){
                    if($b["type"]==2 && $b["spin"]>32) $tujyoG += $b["spin"]-32;
                }
                if($b["type"] ==1 && $b["spin"]>100) $fleeze++;
                $befor = $b["type"];
            }
            if($befor==1 && $dai->data["spin_count"]>32) $tujyoG -= 32;

            $dai->hatu = $hatu;
            $dai->tujyoG = $tujyoG;
            $dai->kakuritu = "-";
            if($hatu && $tujyoG) $dai->kakuritu = round($tujyoG/$hatu);

            //出玉評価
            if($dai->dedama>3999) $dai->dedamaHyouka = "dedamaGood";
            if($dai->dedama>7999) $dai->dedamaHyouka = "dedamaVeryGood";
            if($dai->dedama<0) $dai->dedamaHyouka = "dedamaBad";
            if($dai->dedama==0) $dai->dedamaHyouka = "";
            //初当たり評価
            if($dai->kakuritu<400) $dai->kakurituHyouka = "kakurituGood";
            if($dai->kakuritu<350) $dai->kakurituHyouka = "kakurituVeryGood";
            if($dai->kakuritu>500) $dai->kakurituHyouka = "kakurituBad";
            if($dai->kakuritu=="-") $dai->kakurituHyouka = "";

        // ■■■パチンコ■■■
        }elseif($type==2){
            $dai->hatu = $dai->data["normal_count"] + $dai->data["sf_count"];

        // ■■■その他機種■■■
        }else{

        }
        return true;
    }

    // 着席データ追加  未完成。apiの返信来ず。
    public static function sitDataInsert($floorData)
    {
        $floor_id = ((int)date("G")>9 && (int)date("G")<22)?"100300000":"100330000";
        $floor_id +=$floorData[0]->floor;
        $hall = ((int)date("G")>9 && (int)date("G")<22)?"100100002":"100100003";
        $url = "https://api.el-drado.com/machine/list";
        $headers = [
            'Connection'=> 'keep-alive',
            'sec-ch-ua'=> '" Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
            'DNT'=> '1',
            'sec-ch-ua-mobile'=> '?0',
            'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
            'Content-Type'=> 'application/json;charset=UTF-8',
            'Accept'=> 'application/json, text/plain, */*',
            'ir-ticket'=> '4e33eb8cb59f1f1a74774484cb59ee51ee326004',
            'sec-ch-ua-platform'=> '"Windows"',
            'Origin'=> 'https://el-drado.com',
            'Sec-Fetch-Site'=> 'same-site',
            'Sec-Fetch-Mode'=> 'cors',
            'Sec-Fetch-Dest'=> 'empty',
            'Referer'=> 'https://el-drado.com',
            'Accept-Language'=> 'ja-JP,ja;q=0.9',
        ];

        //api
        $response = Http::withHeaders($headers)
        ->post($url, [
            'mst_floor_id' => $floor_id,
            'mst_hall_id' => $hall,
        ]);

        return $response;

        return true;
    }




}
