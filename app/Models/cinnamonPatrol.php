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


class cinnamonPatrol extends Authenticatable
{
    protected $table = 'cinnamonPatrol';
    protected $guarded = [''];
    public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    // ■フロント
    // データ取得
    public static function cpGet(){
        $floor = floor::floorData();
        if(!$floor) return false; //お休み中
        $ima = floor::ima();

        //日付が変ったらリセット
        $todayCheck = cinnamonPatrol::where([
            'hall' => $ima['hall'],
            'date' => $ima['date'],
            'template' => 0,
        ])->first();

        if(!$todayCheck){
            cinnamonPatrol::where('template', '<>', 1,)->delete();
            /*
            cinnamonPatrol::where([
                'template' => 0,
            ])->delete();
            cinnamonPatrol::where([
                'template' => 2,
            ])->delete();
            cinnamonPatrol::where([
                'template' => 3,
            ])->delete();
            */
        }

        foreach($floor as $f){
            $cp = cinnamonPatrol::where([
                'hall' => $ima['hall'],
                'date' => $ima['date'],
                'floor' => $f['floor'],
                'kisyuId' => $f['kisyu'],
                'rate' => $f['rate'],
                'kankin' => $f['kankin'],
                'template' => 0,
            ])->first();

            // cpがなかったら
            if(!$cp){
                //テンプレート取得
                $template = cinnamonPatrol::where([
                    'kisyuId' => $f['kisyu'],
                    'rate' => $f['rate'],
                    'kankin' => $f['kankin'],
                    'template' => 1,
                ])->first();

                //機種情報取得
                $kisyu = kisyu::find($f["kisyu"],["type","tenjyo","renZone","name","kakurituVeryGood","kakurituGood"])->toArray();

                //テンプレート作成
                if(!$template){
                    $template = new cinnamonPatrol();
                    $template->kisyuId = $f["kisyu"];
                    $template->tenjyo = $kisyu["tenjyo"]-100;
                    $template->ren = $kisyu["renZone"];
                    $template->border = $kisyu["kakurituVeryGood"];
                    $template->hatu = 1;
                    $template->rate = $f["rate"];
                    $template->kankin = $f["kankin"];
                    $template->go = 0;
                    $template->template = 1;
                    $template->save();
                }

                // cp作成
                $cp = new cinnamonPatrol();
                $cp->hall = $ima["hall"];
                $cp->date = $ima["date"];
                $cp->floor = $f["floor"];
                $cp->kisyuId = $f["kisyu"];
                $cp->kisyu = $kisyu["name"];
                $cp->type = $kisyu["type"];
                $cp->tenjyo = $template->tenjyo;
                $cp->ren = $template->ren;
                $cp->border = $template->border;
                $cp->tenjyoPla = $kisyu["tenjyo"];
                $cp->renPla = $kisyu["renZone"];
                $cp->borderPla = $kisyu["kakurituVeryGood"];
                $cp->hatu = $template->hatu;
                $cp->rate = $f["rate"];
                $cp->kankin = $f["kankin"];
                $cp->go = $template->go;
                $cp->template = 0;
                $cp->target = "";
                $cp->ng = "";
                $cp->save();
            }
            $f['cp'] = $cp->toArray();
        }
        return $floor;
    }

    //成果表示
    public static function seika(){
        $seika = cinnamonPatrol::where([
            'template' => 2,
        ])->select('target')
        ->get()
        ->toArray();

        return $seika;


    }




    // ■API
    // メインクロール
    public static function crawl($floor){
        $ima = floor::ima();
        $global = config('global');
        $addMachineId = (($floor-1)*40) + $global["toMachineId"][$ima["hall"]];
        //return [$floor];

        // 空台リスト
        $aki = floor::akiDai($floor);
        if(!$aki) return false; //お休み中

        // 各台回す
        foreach($res as $key => $d){
            // DBからdai取得
            $daiban = $d['mst_machine_id'] - $addMachineId;
            $dai = dai::where('hall', $ima["hall"])
                ->where('floor', $floor)
                ->where('daiban', $daiban)
                ->where('date', $ima["date"])
                ->first()
                ->toArray();
            if(!$dai) return false; //お休み中

            //空き台なら
            if(!$d['usr_id']){}
            //タイムアウト前なら
            if($d['time_out'] <10 && $d['time_out'] >1){}

            // 新しい空台
            if(!$d['usr_id'] && $dai['tyakuseki']){}

            // vgの空台はチェック
            // 着席情報更新
        }

        return true;




    }





    // 受信データをアップデート
    public static function cpUpdate($input){
        $floor = floor::floorData();
        $ima = floor::ima();

        foreach($floor as $f){
            $cp = cinnamonPatrol::where([
                'hall' => $ima['hall'],
                'date' => $ima['date'],
                'floor' => $f['floor'],
                'kisyuId' => $f['kisyu'],
                'rate' => $f['rate'],
                'kankin' => $f['kankin'],
                'template' => 0,
            ])->first();

            $temlate = cinnamonPatrol::where([
                'kisyuId' => $f['kisyu'],
                'rate' => $f['rate'],
                'kankin' => $f['kankin'],
                'template' => 1,
            ])->first();

            //goセット
            $cp->go = isset($input['go'.$f['floor']])? 1: 0;
            $temlate->go = $cp->go;

            //borderセット
            $cp->border = $input['border'.$f['floor']];
            $temlate->border = $cp->border;

            //hatuセット
            $cp->hatu = $input['hatu'.$f['floor']];
            $temlate->hatu = $cp->hatu;

            //tenjyoセット
            $cp->tenjyo = $input['tenjyo'.$f['floor']];
            $temlate->tenjyo = $cp->tenjyo;

            //renセット
            $cp->ren = $input['ren'.$f['floor']];
            $temlate->ren = $cp->ren;

            //targetセット
            $tgArr = explode(",", $input['target'.$f['floor']]);
            $tgNew = "";
            foreach($tgArr as $t){
                if($t && $t>=1 && $t<=40){
                    if(strpos($tgNew, $t) === false ) {
                        $tgNew .= $t.",";
                    }
                }
            }

            $cp->target = rtrim($tgNew, ',');

            //ngセット
            $ng = $cp->ng;
            $ngBackup = explode(",", $input['ngBackup'.$f['floor']]);
            foreach($ngBackup as $b){
                $ng = str_replace($b, "", $ng);
            }
            $ngArr = explode(",", $ng);
            $ngNew = "";
            foreach($ngArr as $n){
                if($n) $ngNew .= $n.",";
            }
            $ngInput = explode(",", $input['ng'.$f['floor']]);
            foreach($ngInput as $i){
                if($i && $i>=1 && $i<=40){
                    if(strpos($ngNew, $i) === false ) {
                        $ngNew .= $i.",";
                    }
                }
            }

            $cp->ng = rtrim($ngNew, ',');

            $cp->save();
            $temlate->save();
        }
        return true;
    }

    // go,stopトグル
    public static function goStop($go){
        $cp = cinnamonPatrol::where([
            'hall' => 999,
            'template' => 1,
        ])->first();

        if(!$cp){
            $cp = new cinnamonPatrol();
            $cp->hall = 999;
            $cp->template = 1;
            $cp->go = 0;
            $cp->save();
        }

        if($go == 'now') return $cp->go;

        $cp->go = $go;
        $cp->save();

        return true;


    }



    // 着席
    // return EL, false, "notaki"
    public static function tore($fid, $mid, $mes=null){
        $ima = floor::ima();
        $global = config('global');

        //boy取得
        $boyList = account::whereNotNull('sid')
            ->where('go', '1')
            //->where('name', 'EL13')
            ->inRandomOrder()
            ->get();

        // 着席boyいない
        if(!$boyList) cinnamonPatrol::goStop('0');

        foreach($boyList as $b){
            //boyセット
            $header = $global["apiHead"];
            $header['ir-ticket'] = $b->sid;

            //入店API
            $url = 'https://api.el-drado.com/hall/enterhall';
            $response = Http::withHeaders($header)
            ->post($url, [
                'mst_hall_id' => $ima["hall_id"],
            ]);
            $res = $response['result']; //これやって通信完了
            if(!$res){ //入店エラー
                if($response['error_msg'] == "Data update check"){ //ir無効
                    $b->sid = null;
                    $b->save();
                    continue;
                }
                if(strpos($response['error_msg'], "MstHallID not found :")!==false){
                    continue;
                    //return 'ホールID違うよ、バグかな？';
                }
                continue;
            }

            //ジャックポット終わり
            $url = 'https://api.el-drado.com/jackpot/lot';
            $response = Http::withHeaders($header)
            ->post($url, []);
            $res = $response['result']; //これやって通信完了

            //台取りAPI
            $url = $global["sitdownUrl"];
            $response = Http::withHeaders($header)
            ->post($url, [
                'mst_floor_id' => $fid,
                'mst_hall_id' => $ima["hall_id"],
                'mst_machine_id' => $mid,
            ]);
            $res = $response['result']; //これやって通信完了
            if(!$res){ //台取りエラー
                if($response['error_msg'] == "sitting other machine"){ //他の台打ってるやん
                    $b->go = 0;
                    $b->save();
                    continue;
                }
                if(strpos($response['error_msg'], "Fail sit down mhid:")!==false){ //他の人が座ってるよ
                    return 'notaki';
                }
                continue;
            }

            $b->go = 0;
            $b->save();

            // ログ
            $now = date("H:i");
            $addFloorId = $global["toFloorId"][$ima["hall"]];
            $floor = $fid - $addFloorId;
            $addMachineId = (($floor-1)*40) + $global["toMachineId"][$ima["hall"]];
            $daiban = $mid - $addMachineId;
            $cp = cinnamonPatrol::where([
                'hall' => $ima['hall'],
                'date' => $ima['date'],
                'floor' => $floor,
                'template' => 0,
            ])->first();
            $type = $cp->type==1? 'S': 'P';
            $kisyu = $cp->rate.$type.($cp->kankin/10).'% '.$cp->kisyu;
            $mes = $now.'-'.$b->name.'-'.$floor.'F'.$daiban.'番台 '.$kisyu.'【'.$mes.'】';
            $cpMes = new cinnamonPatrol();
            $cpMes->target = $mes;
            $cpMes->template = 2;
            $cpMes->save();

            // ★★★メールでお知らせ★★★
            // ★★★NGリストに登録★★★
            return $b->name;
        }

        return false;
    }




    // 取るかどうか
    // return false, '理由'
    public static function torukai($dai, $cp){
        //goじゃないなら取らない
        if(!$cp->go) return false;

        //連中なら取る
        if($cp->ren && $dai->hatu){
            if($dai->spin < $cp->ren) return '連中('.$dai->spin.'G)';
        }

        //天井近いなら取る
        if($cp->tenjyo){
            if($dai->spin > $cp->tenjyo) return '天井('.$dai->spin.'G)';
        }

        //ng台なら取らない
        if($cp->ng){
            if(strpos($cp->ng, $dai->daiban)!==false) return false;
        }

        //ターゲット台なら取る
        if($cp->target && $dai->hatu){
            $daiKakuritu = round($dai->tujyo / $dai->hatu);
            if(strpos($cp->target, $dai->daiban)!==false)  return '狙台(1/'.$daiKakuritu.')';
        }

        //ボーダーなら取る
        if($dai->hatu && $cp->border && $cp->hatu){
            $daiKakuritu = round($dai->tujyo / $dai->hatu);
            if($dai->hatu >= $cp->hatu
                && $daiKakuritu <= $cp->border
                ) return 'VG(1/'.$daiKakuritu.')';
        }

        //最後まで引っかからなければ取らない
        return false;
    }













}

















