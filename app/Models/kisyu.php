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
use App\Models\account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



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

    //データベースにインサート
    public static function dbInsert($tuika=null){
        $jyu = kisyu::all()->find(10);
        if(!$jyu){
            $jyu = new kisyu();
            $jyu->id = 10;
            $jyu->name = 'Honey♡Collection';
            $jyu->type = 1;
        }
        $jyu->tenjyo = 999;
        $jyu->oneK = 35;
        $jyu->kakurituVeryGood = 50;
        $jyu->kakurituGood = 70;
        $jyu->kakurituBad = 100;
        $jyu->save();

        $an = kisyu::all()->find(6);
        $an->kakurituVeryGood = 250;
        $an->kakurituGood = 265;
        $an->kakurituBad = 280;
        $an->save();

        if(!account::where(['usr_id'=>'3694'])->exists()) account::create(['usr_id'=>'3694','name'=>'EL11']);
        if(!account::where(['usr_id'=>'3678'])->exists()) account::create(['usr_id'=>'3678','name'=>'EL14']);
        if(!account::where(['usr_id'=>'4469'])->exists()) account::create(['usr_id'=>'4469','name'=>'EL19']);
        if(!account::where(['usr_id'=>'4476'])->exists()) account::create(['usr_id'=>'4476','name'=>'EL21']);
        if(!account::where(['usr_id'=>'4599'])->exists()) account::create(['usr_id'=>'4599','name'=>'EL22']);
        if(!account::where(['usr_id'=>'4601'])->exists()) account::create(['usr_id'=>'4601','name'=>'EL24']);
        if(!account::where(['usr_id'=>'4605'])->exists()) account::create(['usr_id'=>'4605','name'=>'EL28']);
        if(!account::where(['usr_id'=>'4609'])->exists()) account::create(['usr_id'=>'4609','name'=>'EL31']);
        if(!account::where(['usr_id'=>'4612'])->exists()) account::create(['usr_id'=>'4612','name'=>'EL33']);
        if(!account::where(['usr_id'=>'4614'])->exists()) account::create(['usr_id'=>'4614','name'=>'EL35']);
        if(!account::where(['usr_id'=>'4618'])->exists()) account::create(['usr_id'=>'4618','name'=>'EL39']);
        if(!account::where(['usr_id'=>'4619'])->exists()) account::create(['usr_id'=>'4619','name'=>'EL40']);
        if(!account::where(['usr_id'=>'4620'])->exists()) account::create(['usr_id'=>'4620','name'=>'EL41']);
        if(!account::where(['usr_id'=>'4670'])->exists()) account::create(['usr_id'=>'4670','name'=>'EL46']);
        $id="3696"; $name="EL12"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4469"; $name="EL19"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4470"; $name="EL20"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4613"; $name="EL34"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4621"; $name="EL42"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4669"; $name="EL45"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4671"; $name="EL47"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4672"; $name="EL48"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="3683"; $name="EL17"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="3679"; $name="EL15"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="3702"; $name="EL18"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4617"; $name="EL38"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="3697"; $name="EL13"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4603"; $name="EL26"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4604"; $name="EL27"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4608"; $name="EL30"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4610"; $name="EL32"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4616"; $name="EL37"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4615"; $name="EL36"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4667"; $name="EL43"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4668"; $name="EL44"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4673"; $name="EL49"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="3676"; $name="EL16"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4600"; $name="EL23"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4602"; $name="EL25"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="4607"; $name="EL29"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="19121"; $name="EL103"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="19120"; $name="EL102"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="19118"; $name="EL101"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="19116"; $name="EL100"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="19122"; $name="EL104"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="21098"; $name="EL105"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="1024"; $name="EL1"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="1153"; $name="EL2"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="1034"; $name="EL5"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="1023"; $name="EL6"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);
        $id="1208"; $name="EL7"; if(!account::where(['usr_id'=>$id])->exists()) account::create(['usr_id'=>$id,'name'=>$name]);

        if(!User::where(['id'=>'1'])->exists()){
            DB::table('users')->insert([
                'id' => 1,
                'name' => "pon",
                'email' => "hero@hero.hero",
                'password' => Hash::make('hero'),
            ]);
        }

        return true;

    }





}


