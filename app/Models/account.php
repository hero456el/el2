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





