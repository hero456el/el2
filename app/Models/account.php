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


class account extends Authenticatable
{
    protected $table = 'account';
    protected $guarded = [''];
    public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    //フロア情報詳細
    public static function floorDetail($floor){
        return true;
    }


}





