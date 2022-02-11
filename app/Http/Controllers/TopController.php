<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;


class TopController extends Controller
{
    public function index(){
        //        return view('login/login');
        //        $login_data = DB::select('select * from login');
        //        return view('login/login',['data' => $login_data]);

        //$items = login::all();

        return view ('top');
    }


}
