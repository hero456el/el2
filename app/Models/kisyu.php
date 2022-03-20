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


class kisyu extends Authenticatable
{
    protected $table = 'kisyu';
    protected $guarded = [''];
    protected $primaryKey = 'id';
    use HasApiTokens, HasFactory, Notifiable;

    //キーが機種名、値がIDの配列を返す
    public static function kisyuID($tuika=null){
        //引数が渡されたらDBに挿入
        if($tuika){
            if(!kisyu::where('name',$tuika['machine_name_ja'])->first()){
                DB::table('kisyu')->insert([
                    'name' => $tuika['machine_name_ja'],
                    'type' => $tuika['machine_type'],
                ]);
            }
        }

        $kisyu = DB::table('kisyu')->get();
        $kisyuID = [];
        foreach($kisyu as $k){ $kisyuID[$k->name] = $k->id;}
        return $kisyuID;

    }




}


