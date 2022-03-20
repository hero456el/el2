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


class floor_info extends Authenticatable
{
    protected $table = 'floor_info';
    protected $guarded = [''];
    use HasApiTokens, HasFactory, Notifiable;

    //フロアデータ取得
    public static function floorInfoGet($date=null, $hall_id=null){

        // 引数が空なら現在のフロア情報を返す
        if(!$date && !$hall_id) return floor_info::floorInfoLatest();

        //DBから最新の日付データ取得
        $info = floor_info::where('hall_id', $hall_id)
            ->where('date', ">=", $date)
            ->orderBy('date', 'asc')
            ->first();
        if(!$info) return false;

        return unserialize($info->data);

    }

    //最新のフロア情報取得
    public static function floorInfoLatest($now=null){
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

        $info = ""; //返り値

        //現在時刻よりホールID取得
        $hall_id = ((int)date("G")>9 && (int)date("G")<22)?"100100002":"100100003";

        //現在時刻より営業日取得
        $date = date("Y-m-d");
        if((int)date("G")<10) $date = date("Y-m-d", strtotime("-1 day"));

        //DBから最新の日付データ取得
        $floorInfo = floor_info::where('hall_id', $hall_id)
            ->orderBy('date', 'desc')
            ->first();
        if(!$floorInfo){ $floorInfo = new floor_info();}

        //本日のデータがあればそれを返り値に
        if($floorInfo->date == $date){
            $info = $floorInfo;

        //本日データがなければapiから取得
        }else{
            $url = "https://api.el-drado.com/floor/info/";
            $url .= $hall_id;
            $response = Http::withHeaders($headers)->post($url);
            $res = $response['body']['floor'];
            // プレイ中人数を空にする
            for($i=0; $i<count($res); $i++) $res[$i]["player"] = 0;

            //データが変ってなければ日付だけ更新
            if($floorInfo->data == serialize($res)){
                $floorInfo->date =$date;
                $floorInfo->save();
                $info = $floorInfo;

            //データが変っていれば新しくインサート
            }else{
                $newInfo = new floor_info();
                $newInfo->hall_id = $hall_id;
                $newInfo->date = $date;
                $newInfo->data = serialize($res);
                $newInfo->save();
                $info = $newInfo;
            }
        }

        ///引数にnowを指定するとプレーヤーの人数が出る。
        if($now=="now"){
            $url = "https://api.el-drado.com/floor/info/";
            $url .= $hall_id;
            $response = Http::withHeaders($headers)->post($url);
            $res = $response['body']['floor'];
            return $res;
        }
        return unserialize($info->data);
    }



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