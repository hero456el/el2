<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kisyu')->truncate(); //データベース削除

        //スロット機種
        DB::table('kisyu')->insert([
            'id' => 1,
            'name' => "SecretRoses",
            'type' => 1,
            'tenjyo' => 1600,
            'renZone' => 32,
            'kakurituVeryGood' => 200,
            'kakurituGood' => 250,
            'kakurituBad' => 300,
//             'dedamaVeryGood' => ,
//             'dedamaGood' => ,
//             'dedamaBad' => ,
        ]);
        DB::table('kisyu')->insert([
            'id' => 2,
            'name' => "JackPotDice",
            'type' => 1,
            'tenjyo' => 1500,
            'renZone' => 50,
        ]);
        DB::table('kisyu')->insert([
            'id' => 3,
            'name' => "PEACHDREAM",
            'type' => 1,
            'tenjyo' => 1100,
        ]);
        DB::table('kisyu')->insert([
            'id' => 4,
            'name' => "Mr.Rich",
            'type' => 1,
            'tenjyo' => 1889,
            'renZone' => 1,
        ]);
        DB::table('kisyu')->insert([
            'id' => 5,
            'name' => "WhichRoses",
            'type' => 1,
            'tenjyo' => 1600,
            'renZone' => 32,
            'kakurituVeryGood' => 350,
            'kakurituGood' => 400,
            'kakurituBad' => 500,
        ]);
        DB::table('kisyu')->insert([
            'id' => 6,
            'name' => "AngelRoses",
            'type' => 1,
            'tenjyo' => 555,
            'kakurituVeryGood' => 100,
            'kakurituGood' => 115,
            'kakurituBad' => 130,
            'dedamaVeryGood' => 3000,
            'dedamaGood' => 1500,
            'dedamaBad' => 0,
        ]);

        //パチンコ機種
        DB::table('kisyu')->insert([
            'id' => 7,
            'name' => "COSMOAttack",
            'type' => 2,
            'tenjyo' => 1500,
            'kakurituVeryGood' => 230,
            'kakurituGood' => 300,
            'kakurituBad' => 400,
            'dedamaVeryGood' => 30000,
            'dedamaGood' => 15000,
            'dedamaBad' => 0,
        ]);
        DB::table('kisyu')->insert([
            'id' => 8,
            'name' => "Checkmate",
            'type' => 2,
            'tenjyo' => 1000,
            'kakurituVeryGood' => 170,
            'kakurituGood' => 200,
            'kakurituBad' => 250,
            'dedamaVeryGood' => 20000,
            'dedamaGood' => 10000,
            'dedamaBad' => 0,
        ]);
        DB::table('kisyu')->insert([
            'id' => 9,
            'name' => "Promotion",
            'type' => 2,
            'tenjyo' => 600,
            'kakurituVeryGood' => 70,
            'kakurituGood' => 85,
            'kakurituBad' => 100,
            'dedamaVeryGood' => 20000,
            'dedamaGood' => 10000,
            'dedamaBad' => 0,
        ]);
    }
}

