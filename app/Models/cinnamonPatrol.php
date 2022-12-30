<?php

namespace App\Models;

use DB;
use Log;
use Illuminate\Support\Facades\Http;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

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

/*    //ドゥル要請表示
    public static function dull(){
        $dull = cinnamonPatrol::where([
            'template' => 3,
            'go' => 1,
        ])
        ->get();

        $now = date("H:i");
        $res = [];

        foreach($dull as $d){
            $to = $d->ren-time();
            if($to<1 || $to>600){
                $d->go = 0;
                $d->save();
                continue;
            }
            //$to = ceil($to/60);
            $res[] = $now.'-'.$to.'秒後解放 '.$d->target.'【'. $d->ng.'】';
        }

        return $res;
    }
*/
    //ドゥル要請表示
    public static function dull(){
        Log::info('-------ドゥル要請確認---------');//★★★log★★★
        $dull = cinnamonPatrol::where([
            'template' => 3,
            'go' => 1,
        ])
        ->orderBy('ren', 'asc')
        ->get();

        $now = date("H:i");
        $res = [];
        Log::info($dull->count());//★★★log★★★
        if(!$dull->count()){
            $cp = cinnamonPatrol::where([
                'hall' => 999,
            ])->first();
            if(!$cp){
                $cp = new cinnamonPatrol();
                $cp->hall = 999;
                $cp->template = 1;
                $cp->go = 0;
                $cp->save();
            }
            $cp->ren = 0;
            $cp->save();

            return [["mess"=>"nothing"]];
        }

        foreach($dull as $d){
             // タイムオーバーで終わり
            $to = $d->ren-time();
            /*
             if($to<0){
                Log::info('タイムオーバー'.$to);//★★★log★★★
                $d->go = 0;
                $d->save();
                continue;
            }*/

            /*
            $aki = floor::akiDai($d->floor);
            // 戻ってきてたら終わり
            if($aki[$d->kisyuId-1]['time_out'] != $d->ren && $to>5){
                Log::info('戻ってきた。残り'.$to);//★★★log★★★
                $d->delete();
                continue;
            }*/

            if(floor($to/60)>0){
                $mess = $now.' - '.floor($to/60).'分'.($to%60).'秒後解放'.'('.date('G:i:s',$d->ren).') '.$d->target.'【'. $d->ng.'】';
            }else{
                $mess = $now.' - '.$to.'秒後解放'.'('.date('G:i:s',$d->ren).') '.$d->target.'【'. $d->ng.'】';
            }
            Log::info($mess);//★★★log★★★
            $floor = $d->floor;
            $daiban = $d->kisyuId;
            $time = $d->ren;

            $res[] = ['mess'=>$mess, 'floor'=>$floor, 'daiban'=>$daiban, 'time'=>$time];
        }

        return $res;
    }


    // アタック
    public static function dAttack() {
        Log::info('-------アタックIN---------');//★★★log★★★

        //現時刻のデータ
        $ima = floor::ima();

        //global変数取得
        $global = config('global');

        $now = time();

        $dullList = cinnamonPatrol::where([
            'template' => 3,
            'go' => 1,
        ])
        ->where('ren', '>=', $now-2)
        ->where('ren', '<=', $now+10) //10でもいいかも。
        ->get();
        if($dullList->count()==0){
            Log::info('return end');//★★★log★★★
            return 'end';
        }

        foreach($dullList as $d){
            $diff = $d->ren - $now;
            if($diff>3) continue;

            Log::info($d->target.'アタック - '.$d->ng.' '.($diff).'秒後');//★★★log★★★

            $aki = floor::akiDai($d->floor);

            // 狙いが着席されてたら終わり
            if($aki[$d->kisyuId-1]['to'] > 600){
                Log::info('アタック - 着席されてて終わり'.$aki[$d->kisyuId-1]['to']);//★★★log★★★
                $d->go = 0; //失敗
                $d->save();
                continue;
            }

            // ボーナス中狙いならakiやったから終わり
            if($d->ng=="bonus") continue;

            //狙いが空台
            if($aki[$d->kisyuId-1]['usr_id']==0){
                Log::info('アタック - 狙いが空いてる！！');//★★★log★★★
                $fid = $d->floor + $global["toFloorId"][$ima["hall"]];
                $addMachineId = (($d->floor-1)*40) + $global["toMachineId"][$ima["hall"]];
                $mid = $d->kisyuId + $addMachineId;
                Log::info('fid'.$fid);//★★★log★★★
                Log::info('mid'.$mid);//★★★log★★★
                Log::info('3'.$d->ng);//★★★log★★★
                $el = cinnamonPatrol::tore($fid, $mid, $d->ng);
                $d->go = 0;
                $d->save();
                continue;
            }
        }
    }




    // ■API
    // メインクロール
    public static function crawl($floor){
        $ima = floor::ima();
        $global = config('global');
        $addMachineId = (($floor-1)*40) + $global["toMachineId"][$ima["hall"]];
        $addFloorId = $global["toFloorId"][$ima["hall"]];
        $floor_id = $floor + $addFloorId;

        // 空台リスト
        $aki = floor::akiDai($floor);
        if(!$aki) return "error1"; //お休み中

        $cp = cinnamonPatrol::where([
            'hall' => $ima['hall'],
            'date' => $ima['date'],
            'floor' => $floor,
            'template' => 0,
        ])->first();
        if(!$cp) return "error2"; //お休み中

        // 各台回す
        foreach($aki as $key => $d){
            // DBからdai取得
            $daiban = $d['mst_machine_id'] - $addMachineId;
            $dai = dai::where('hall', $ima["hall"])
                ->where('floor', $floor)
                ->where('daiban', $daiban)
                ->where('date', $ima["date"])
                ->first();
            if(!$dai) return "error3"; //お休み中

            //タイムアウトフラグ
//            $to = ($d['to'] <10 && $d['to'] >1)? 1: 0;
            $to = ($d['to'] <6 && $d['to'] >1)? 1: 0;

            // 新しい空台フラグ
            $akiNew = (!$d['usr_id'] && $dai->tyakuseki)? 1: 0;
            if($akiNew){
                $dai->tyakuseki = 0;
                $dai->save();
            }

            // フラグが立っていれば台チェック
            if($to || $akiNew){
                //url
                $floorlist = floor::floorData();
                $f = [$floorlist[$floor-1]][0];
                $kisyu = kisyu::find($f["kisyu"],["type","tenjyo","renZone","name","kakurituVeryGood","kakurituGood"])->toArray();
                $url = $global["daiApiUrl"][$kisyu["type"]];

                //daiAPI
                $response = Http::withHeaders($global["apiHead"])
                ->post($url, [
                    'mst_floor_id' => $floor_id,
                    'mst_hall_id' => $ima["hall_id"],
                    'mst_machine_id' => $d['mst_machine_id'],
                ]);
                if(!isset($response['body'][$global["detail"][$kisyu["type"]]])) return "error4"; //apiの戻りが空ならエラー
                $res = $response['body'][$global["detail"][$kisyu["type"]]]; //これやらないとデータ空
//                Log::info($res[0]['medal']);

                dai::resToModel($dai, $res[0], $kisyu, $f);

                //取る台かチェック
                $mes = cinnamonPatrol::torukai($dai, $cp);
                if($mes){
//                    Log::info('torukai mes-'.$mes);
                    if($akiNew) cinnamonPatrol::tore($floor_id, $d['mst_machine_id'], $mes);
                    if($to) cinnamonPatrol::dullShite($floor, $daiban, $d['time_out'], $mes);
                }
            }

            // vgの空台はチェック

            // 着席情報更新
            if(!$d['usr_id'] && $dai->tyakuseki==1){
                $dai->tyakuseki = 0;
                $dai->save();
            }
            if($d['usr_id'] && $dai->tyakuseki==0){
                $dai->tyakuseki = 1;
                $dai->save();
            }


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
    // return EL, false
    public static function tore($fid, $mid, $mes=null){
        $ima = floor::ima();
        $global = config('global');
        $addFloorId = $global["toFloorId"][$ima["hall"]];
        $floor = $fid - $addFloorId;
        $addMachineId = (($floor-1)*40) + $global["toMachineId"][$ima["hall"]];
        $daiban = $mid - $addMachineId;

        //boy取得
        $boyList = account::whereNotNull('sid')
            ->where('go', '1')
            //->where('name', 'EL13')
            ->inRandomOrder()
            ->get();

        // 着席boyいない
        if(!$boyList){
            Log::info('着席ボーイいない。。。');//★★★log★★★
            cinnamonPatrol::goStop('0');
        }

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
                    $dai = dai::where('hall', $ima["hall"])
                        ->where('floor', $floor)
                        ->where('daiban', $daiban)
                        ->where('date', $ima['date'])
                        ->first();
                    $dai->tyakuseki = 0;
                    $dai->save();
                    Log::info('他の人座ってるよ。'.$floor.'-'.$daiban);//★★★log★★★

                    return false;
                }
                continue;
            }

            $b->go = 0;
            $b->save();

            // ログ
            $now = date("H:i");
            $cp = cinnamonPatrol::where([
                'hall' => $ima['hall'],
                'date' => $ima['date'],
                'floor' => $floor,
                'template' => 0,
            ])->first();
            $type = $cp->type==1? 'S': 'P';
            $kisyuRyaku = str_split($cp->kisyu,5)[0];
            $kisyu = $cp->rate.$type.($cp->kankin/10).'% '.$kisyuRyaku;
            $mes2 = $now.'-'.$b->name.'-'.$floor.'F-'.$daiban.'番 '.$kisyu.'【'.$mes.'】';
            $cpMes = new cinnamonPatrol();
            $cpMes->target = $mes2;
            $cpMes->template = 2;
            $cpMes->save();

            // VG,targetならNGリストに登録
            if(strpos($mes2, "VG")!==false || strpos($mes, "target")!==false){
                $cp = cinnamonPatrol::where([
                    'hall' => $ima['hall'],
                    'date' => $ima['date'],
                    'floor' => $floor,
                    'template' => 0,
                ])->first();
                if($cp){
                    $cp->ng .= $cp->ng? ','.$daiban: $daiban;
                    $cp->save();
                }
            }

            // Lineでお知らせ
            $linemes  = "台取れたよ！\n\n";
            $linemes .= $b->name."\n";
            $linemes .= $floor."F ".$daiban."番台 ";
            $linemes .= $cp->kisyu."\n";
            $linemes .= $cp->rate.$type." ".($cp->kankin/10)."%\n";
            $linemes .= "【".$mes."】\n";
            $linemes .= "\nぷっぷくのご加護がありますように\n";
            cinnamonPatrol::sendLine($linemes);

            Log::info($linemes);//★★★log★★★

            return $b->name;
        }

        return false;
    }




    // 取るかどうか
    // return false, '理由'
    public static function torukai($dai, $cp){
        $ima = floor::ima();
        $now = date("H");
        $heiten = 0;
        if($ima['hall']==2 && $now>=21) $heiten = 1;
        if($ima['hall']==3 && ($now==9 || $now==10)) $heiten = 1;

        //初当たり確率
        $daiKakuritu = '-';
        if($dai->hatu){$daiKakuritu = round($dai->tujyo / $dai->hatu);}

        //連中なら取る
        if($cp->ren && $dai->hatu){
            if($dai->spin < $cp->ren) return '連中('.$dai->spin.'G)';
        }

        //goじゃないなら取らない
        if(!$cp->go) return false;

        //閉店1時間前ならここまで
        if($heiten) return false;

        //天井近いなら取る
        if($cp->tenjyo){
            if($dai->spin > $cp->tenjyo) return '天井('.$dai->spin.'G)';
        }

        //ng台なら取らない
        if(in_array($dai->daiban, explode(",", $cp->ng))) return false;

        //ターゲット台なら取る
        if(in_array($dai->daiban, explode(",", $cp->target))) return 'target '.$dai->hatu.'/'.$dai->tujyo.'(1/'.$daiKakuritu.')';

        //ボーダーなら取る
        if($dai->hatu && $cp->border && $cp->hatu){
            if($dai->hatu >= $cp->hatu
                && $daiKakuritu <= $cp->border
                ) return 'VG '.$dai->hatu.'/'.$dai->tujyo.' (1/'.$daiKakuritu.')';
        }

        //最後まで引っかからなければ取らない
        return false;
    }



    // ドゥル要請登録
    public static function dullShite($floor, $daiban, $time, $mes){
        if((int)$daiban > (15*40)){
            $ima = floor::ima();
            $global = config('global');
            $addMachineId = (($floor-1)*40) + $global["toMachineId"][$ima["hall"]];
            $daiban = (int)$daiban - $addMachineId;
        }
        $shikibetu = $floor.'F-'. $daiban.'番';

        //DBチェック
        $cp = cinnamonPatrol::where([
            'target' => $shikibetu,
            'template' => 3,
            'go' => 1,
        ])->first();

        //timeout対象外
        $to = $time-time();
        if($to<-10 || $to>600){
            if($cp){
                $cp->go = 0;
                $cp->save();
            }
            return false;
        }

        //Lineで連絡
        if(!$cp){
            $cp = new cinnamonPatrol();
             // Lineでお知らせ
            $linemes  = "ドゥルお願い！\n\n";
            $linemes .= $shikibetu."\n";
            $linemes .= $mes."\n";
            $linemes .= "残り".$to."秒\n";
            $linemes .= "\n";
            $linemes .= "何件かあっても一回しかライン送らないうになったよ。\n";
            $linemes .= "詳しくはシナモンを見て。\n";
            $linemes .= "https://aroma-luna.floppy.jp/hero2/public/cinnamonPatrol \n";
            $linemes .= "\n";
            $linemes .= "ぷっぷくのご加護がありますように\n";


            cinnamonPatrol::sendLine($linemes);
        }


        //残りtimeout時間をセット
        $cp->target = $shikibetu;
        $cp->ng = $mes;
        $cp->ren = $time;
        $cp->go = 1;
        $cp->template = 3;
        $cp->floor = $floor;
        $cp->kisyuId = $daiban;
        $cp->save();

        return true;
    }


    // メールを送信するメソッド.
    public static function sendMail()
    {

        // Mail::sendで送信できる.
        // 第1引数に、テンプレートファイルのパスを指定し、
        // 第2引数に、テンプレートファイルで使うデータを指定する
        Mail::send('email.email', [
            "me" => "helloooo"

        ], function($mess) {

            // 第3引数にはコールバック関数を指定し、
            // その中で、送信先やタイトルの指定を行う.
            $mess
            ->to('mituru.hamaoka@gmail.com')
            //->bcc('')
            ->subject("-- Heros Eye --");
        });
        return true;
    }


    // ライン送信
    public static function sendLine($message = null) {
        $cp = cinnamonPatrol::where([
            'hall' => 999,
        ])->first();
        if(!$cp){
            $cp = new cinnamonPatrol();
            $cp->hall = 999;
            $cp->template = 1;
            $cp->go = 0;
            $cp->save();
        }
        if((time()-$cp->ren)<600) return "さっき送った";
        $cp->ren = time();
        $cp->save();

//        return true;
        // :point_down: アクセストークン
        $access_token = env('LINE_ACCESS_TOKEN');
        // :point_down: チャンネルシークレット
        $channel_secret = env('LINE_CHANNEL_SECRET');

        //別アカ
//        $access_token = 'kYYwSpPQ460jOiMsl/1gWo5z+IypbjNw45EMge/ZBQdvzPGSczMgu0a4I1cx/KIEYSbbeigk12kSSBjazrItL/IgvNbpMqAECTNFd4kRU/fF2bk5Gm5enRYbdTYFI0s8Ilo1AEr9r/u1eZhsKIZRxAdB04t89/1O/w1cDnyilFU=';
//        $channel_secret = '20ea4491a7bf8ffe58fa86e9d62c514d';


        // Lineに送信する準備
        $http_client = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
        $bot         = new \LINE\LINEBot($http_client, ['channelSecret' => $channel_secret]);

        $line_user_id_y = "Uf89fbb3ef62700d920dd358aeda106f4"; //yan
        $line_user_id_p = "U8fa7c0643a7b087ec0f5baef09901465"; //p

        $message = $message ?? "ぷっぷくのご加護がありますように。";
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
        $response    = $bot->pushMessage($line_user_id_y, $textMessageBuilder);
        $response    = $bot->pushMessage($line_user_id_p, $textMessageBuilder);

        // 配信成功・失敗
        if ($response->isSucceeded()) {
            Log::info('Line 送信完了');
        } else {
            Log::error('投稿失敗: ' . $response->getRawBody());
        }

        return true;
    }


        // ルロロ
    public static function lloro($floor, $daiban) {
        $ima = floor::ima();
        $global = config('global');
        $addMachineId = (($floor-1)*40) + $global["toMachineId"][$ima["hall"]];
        $addFloorId = $global["toFloorId"][$ima["hall"]];
        $floor_id = $floor + $addFloorId;
        $machine_id = $addMachineId + $daiban;

        // floorAPIを叩いてボーナス中ではない事を確認
        $daiList = floor::akiDai($floor);
        if(!$daiList) return false;
        $bonus = $daiList[$daiban-1]['bonus'];
        if($bonus) return "bonus中";

        // 台APIを叩いて連荘、天井間近では無いことを確認
        $cp = cinnamonPatrol::where([
            'hall' => $ima['hall'],
            'date' => $ima['date'],
            'floor' => $floor,
            'template' => 0,
        ])->first();
        if(!$cp) return false;
        $kisyu = kisyu::find($cp->kisyuId,["type","tenjyo","renZone","name","kakurituVeryGood","kakurituGood"])->toArray();
        $url = $global["daiApiUrl"][$kisyu["type"]];
        $response = Http::withHeaders($global["apiHead"])
        ->post($url, [
            'mst_floor_id' => $floor_id,
            'mst_hall_id' => $ima["hall_id"],
            'mst_machine_id' => $machine_id,
        ]);
        if(!isset($response['body'][$global["detail"][$kisyu["type"]]])) return false; //apiの戻りが空ならエラー
        $res = $response['body'][$global["detail"][$kisyu["type"]]]; //これやらないとデータ空


        // 辞めAPI
        // ジャックポットAPI
        // goフラグ立てる

        return true;
    }





}




/*
template 0 指示出しレコード
template 1 テンプレート
template 2 台取り結果
template 3 ドゥル要請
template 4 ドゥル実行データ
template 5

*/












