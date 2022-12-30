<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
use Illuminate\Database\Eloquent\Model;
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
use App\Models\ir;

// class ir extends Model
class ir extends Authenticatable
{
    protected $table = 'ir';
    protected $guarded = [''];
    public $timestamps = false;
    // use HasApiTokens, HasFactory, Notifiable;

    // ランダムに一つ取得
    public static function random(){
        // $ir = DB::table('ir')->first();
        $ir = ir::where("active", 0)->inRandomOrder()->first()->ir;
        return $ir;
    }

    // ランダムirつきheader
    public static function header(){
        $ir = ir::random();
        $rancomHead = config('global.apiHead');
        // return var_dump($rancomHead);
        foreach($rancomHead as $key => $r){
            $rancomHead[$key] = str_replace('RANDOMIR', $ir, $r);
        }
        return $rancomHead;
    }

    // APIテスト
    public static function APItest(){
        $ima = floor::ima(); //現時刻データ
        $irList = ir::get();
        foreach($irList as $i){
            $rancomHead = config('global.apiHead');
            $rancomHead['ir-ticket'] = $i->ir;
            $response = Http::withHeaders($rancomHead)
                ->post(config('global.url_hall').$ima["hall_id"]);
            if(!isset($response['body']['floor'])){
                $i->active++;
                $i->save();
            }else{
                $i->active=0;
                $i->save();
            }
        }
        return 'ok';
    }

    // 
    public static function irList(){
        $irList = ir::get();
        return $irList;
    }

    public static function del($id){
        $ir = ir::find($id);
        if($ir) $ir->delete();
        return null;
    }

    public static function insert($ir){
        $new = new ir;
        $new->ir = $ir;
        $new->save();
        return null;
    }


}





