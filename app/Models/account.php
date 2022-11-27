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
use App\Models\account;


class account extends Authenticatable
{
    protected $table = 'account';
    protected $guarded = [''];
    public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    // ■フロント用
    // GOを配列で返す
    public static function goGet(){
        $go = account::select('name', 'go')
            ->whereNotNull('sid')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();

        //boyがいなければパトロール中止
        if(!$go) cinnamonPatrol::goStop('0');

        return $go;
    }

    // SIDを配列で返す
    public static function sidGet(){
        $sid = account::select('name', 'sid')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
        return $sid;
    }

    // 収支を取得
    public static function syushi(){
        $ima = floor::ima(); //現時刻のデータ
        $global = config('global');
        $apiHead = $global["apiHead"];
        $syushiList = [];
        $addMachineIdFloor = $global["toMachineId"][$ima["hall"]];
        $floorList = floor::floorData()->toArray();
        $totalEP = 0;
        $totalKakutoku = 0;
        $totalPEP = 0;
        $totalPKakutoku = 0;
        $diffEP = 0;
        $diffPEP = 0;
        $Pcount = 0;

        //sidリスト取得
        $sidList = account::orderBy('name', 'asc')
            //->whereNotNull('sid')
            ->get();
        $count = $sidList->count();

        //APIで収支取得
        $url = "https://api.el-drado.com/balance";
        foreach($sidList as $s){
            //sidがなければコンティニュー
            if(!$s->sid){
                $syushiList[] = [
                    "el"=> $s->name,
                    "sid"=> null,
                    "balance"=>null,
                    "floor"=> null,
                    "daiban"=> null,
                    "kakutoku"=> null,
                ];
                continue;
            }

            //API
            $apiHead["ir-ticket"] = $s->sid;
            $response = Http::withHeaders($apiHead)
                ->post($url, [ ]);
            $res = $response['body'];
            if(!$res){
                $syushiList[] = [
                    "el"=> $s->name,
                    "sid"=> null,
                    "balance"=> null,
                    "floor"=> null,
                    "daiban"=> null,
                    "kakutoku"=> null,
                ];
                $s->sid = null;
                $s->save();
                continue;
            }

            //floor計算
            $floor = 0;
            $daiban = 0;
            if($res["mst_machine_id"]){
                $floorMoto = $res["mst_machine_id"] - $addMachineIdFloor;
                $floor = ceil($floorMoto/40);
                $daiban = $floorMoto % 40;
            }

            //出玉計算
            $kakutoku = 0;
            $dedama = $res["medal"]?:$res["ball"];
            if($dedama){
                $rate = $floorList[$floor-1]["rate"];
                $kankin = $floorList[$floor-1]["kankin"] / 1000;
                $kakutoku = (round($dedama * $rate * $kankin))/100;
            }

            //返り値
            $syushiList[] = [
                "el"=> $s->name,
                "sid"=> $s->sid,
                "balance"=> round($res["balance"]/100),
                "floor"=>$floor,
                "daiban"=>$daiban,
                "kakutoku"=>round($kakutoku),
            ];

            if($s->name=="EL50"
                || $s->name=="EL51"
                || $s->name=="EL52"
                || $s->name=="EL53"
                || $s->name=="EL54"
                ){
                    $Pcount++;
                    $totalPEP += $res["balance"]/100;
                    $totalPKakutoku += $kakutoku;
            }else{
                $count--;
                $totalEP += $res["balance"]/100;
                $totalKakutoku += $kakutoku;
            }
        }

        //全データがあればホールにセーブ
        if($count==5){
            $hall = hall::where("hall", $ima["hall"])
                ->where("date", $ima["date"])
                ->first();
            if($hall){
                $hall->ep = $totalEP+$totalKakutoku;
                $hall->save();
            }
            $beforHall = $ima["hall"]==2?3:2;
            $beforDate = $ima["hall"]==3?$ima["date"]:$ima["date1"];
            $befor = hall::where("hall", $beforHall)
                ->where("date", $beforDate  )
                ->first();
            if($hall->ep && $befor->ep){
                $diffEP = round($hall->ep - $befor->ep);
            }
        }
        if($Pcount==5){
            $hall = hall::where("hall", $ima["hall"])
                ->where("date", $ima["date"])
                ->first();
            if($hall){
                $hall->pep = $totalPEP+$totalPKakutoku;
                $hall->save();
            }
            $beforHall = $ima["hall"]==2?3:2;
            $beforDate = $ima["hall"]==3?$ima["date"]:$ima["date1"];
            $befor = hall::where("hall", $beforHall)
                ->where("date", $beforDate  )
                ->first();
            if($hall && $hall->pep && $befor->pep){
                $diffPEP = round($hall->pep - $befor->pep);
            }
        }

        return [
            "syushiList"=>$syushiList,
            "totalEP"=>round($totalEP),
            "totalKakutoku"=>round($totalKakutoku),
            "totalPEP"=>round($totalPEP),
            "totalPKakutoku"=>round($totalPKakutoku),
            "diffEP"=>$diffEP,
            "diffPEP"=>$diffPEP,
        ];
    }

    // ■API用
    // SID登録
    public static function sidInsert($input){
        //SID登録
        foreach($input as $key => $sid){
            // sid空ならスルー
            if(!$sid) continue;
            // 有効じゃなければスルー
            if(!account::sidCheck($sid)) continue;
            // DBアップデート
            account::where('name', $key)->update(['sid' => $sid]);
        }
        return true;
    }

    // SID有効確認
    public static function sidCheck($sid){
        $global = config('global');
        $url = "https://api.el-drado.com/balance";

        $global["apiHead"]['ir-ticket'] = $sid;

        //API
        $response = Http::withHeaders($global["apiHead"])
            ->post($url, [ ]);
        return $response['result'];
    }

    // 全SID有効確認
    public static function allSidCheck(){
        $sid = account::whereNotNull('sid')
            ->get();
        foreach($sid as $s){
            $check = account::sidCheck($s->sid);
            if(!$check){
//                return 'チェック落ちたよー';
                $s->sid = null;
                $s->save();
            }
        }

        return $sid;
    }

    // GOオン
    public static function goOn($input){
        // goリセット
        DB::table('account')->update(['go' => 0]);
        foreach($input as $key => $i){
            // on以外ならスルー
            if($i != 'on') continue;
            // DBアップデート
            account::where('name', $key)->update(['go' => 1]);
        }
        return true;
    }

    // UID登録
    public static function uidInsert(){
        return true;
    }


}





