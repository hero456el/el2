<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update0226 extends Migration
{
    public function up()
    {
        Schema::create('dai', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hall')->nullable();
            $table->integer('floor')->nullable();
            $table->integer('daiban')->nullable();
            $table->date('date')->nullable()->comment('日付');
            $table->integer('totalSpin')->nullable()->comment('総回転数');
            $table->integer('spin')->nullable()->comment('現在回転数');
            $table->integer('BB')->nullable()->comment('BIG');
            $table->integer('RB')->nullable()->comment('REG');
            $table->integer('AT')->nullable()->comment('AT');
            $table->integer('hatu')->nullable()->comment('初当り回数');
            $table->integer('tujyo')->nullable()->comment('通常G');
            $table->integer('toushi')->nullable()->comment('投資メダル');
            $table->integer('dedama')->nullable()->comment('出玉メダル');
            $table->integer('syushi')->nullable()->comment('収支円');
        });
        Schema::table('dai', function (Blueprint $table) {
            $table->unique(['hall', 'floor','daiban','date']);
        });
        Schema::create('floor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hall')->nullable();
            $table->integer('floor')->nullable();
            $table->date('date')->nullable()->comment('日付');
            $table->integer('kisyu')->nullable()->comment('機種');
            $table->integer('rate')->nullable()->comment('レート');
            $table->integer('kankin')->nullable()->comment('換金率');
            $table->integer('totalSpin')->nullable()->comment('総回転数');
            $table->integer('dedama')->nullable()->comment('出玉メダル');
            $table->integer('syushi')->nullable()->comment('収支円');
            $table->boolean('total')->nullable()->comment('全階数');
            $table->boolean('good')->nullable()->comment('評価');
            $table->boolean('veryGood')->nullable()->comment('評価');
            $table->timestamp('lastUpdate')->nullable()->comment('最終更新');
        });
        Schema::table('floor', function (Blueprint $table) {
            $table->unique(['hall', 'floor','date'],'uni');
        });
        Schema::create('kisyu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('機種名');
            $table->integer('type')->nullable()->comment('1スロ,2パチ');
            $table->integer('tenjyo')->nullable()->default(9999)->comment('天井');
            $table->integer('renZone')->nullable()->default(0)->comment('連荘G');
            $table->integer('oneK')->nullable()->comment('回転数');
            $table->integer('kakurituVeryGood')->nullable()->comment('初当優');
            $table->integer('kakurituGood')->nullable()->comment('初当良');
            $table->integer('kakurituBad')->nullable()->comment('初当悪');
            $table->integer('dedamaVeryGood')->nullable()->default(7999)->comment('出玉優');
            $table->integer('dedamaGood')->nullable()->default(3999)->comment('出玉良');
            $table->integer('dedamaBad')->nullable()->default(0)->comment('出玉悪');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        Schema::table('kisyu', function (Blueprint $table) {
            $table->unique(['name'],'uni');
        });
        Schema::create('hall', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hall')->nullable();
            $table->date('date')->nullable()->comment('日付');
            $table->text('twitter')->nullable()->comment('Twitter');
            $table->integer('totalSpin')->nullable()->comment('万回転');
            $table->integer('syushi')->nullable()->comment('万円');
            $table->integer('wari')->nullable()->comment('割');
            $table->boolean('good')->nullable()->comment('評価');
            $table->boolean('veryGood')->nullable()->comment('評価');
            $table->boolean('complete')->nullable()->comment('完了');
        });
            Schema::table('hall', function (Blueprint $table) {
                $table->unique(['hall','date']);
            });

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
                'oneK' => 32,
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
                'oneK' => 20.3,
                'kakurituVeryGood' => 450,
                'kakurituGood' => 700,
                'kakurituBad' => 850,
            ]);
            DB::table('kisyu')->insert([
                'id' => 3,
                'name' => "PEACHDREAM",
                'type' => 1,
                'tenjyo' => 1100,
                'oneK' => 30,
                'kakurituVeryGood' => 115,
                'kakurituGood' => 145,
                'kakurituBad' => 170,
            ]);
            DB::table('kisyu')->insert([
                'id' => 4,
                'name' => "Mr.Rich",
                'type' => 1,
                'tenjyo' => 1889,
                'renZone' => 1,
                'oneK' => 32,
                'kakurituVeryGood' => 220,
                'kakurituGood' => 280,
                'kakurituBad' => 350,
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
                'oneK' => 18.25,
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
                'oneK' => 30,
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
                'oneK' => 17,
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
                'oneK' => 17,
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
                'oneK' => 17,
            ]);
    }

    public function down()
    {
        Schema::dropIfExists('dai');
        Schema::dropIfExists('floor');
        Schema::dropIfExists('kisyu');
        Schema::dropIfExists('hall');
    }

}







/*テンプレート



// 型変更
$table->bigInteger('unit_price')->change();

// カラム作成
public function up()
{
Schema::table('order_outsource', function (Blueprint $table) {
$table->string('document')->after('period')->nullable();
});
}

public function down()
{
Schema::table('order_outsource', function (Blueprint $table) {
$table->dropColumn('document');
});
}


//　クリエイト
public function up()
{
Schema::create('order_outsource', function(Blueprint $table)
{
$table->increments('id');
$table->string('project_id')->nullable();
$table->string('outsourcing_id')->nullable();
$table->string('pic_id')->nullable();
$table->bigInteger('pic_id')->nullable();
$table->integer('pic_id')->nullable();
$table->date('submit_day')->nullable();
$table->text('memo')->nullable();
$table->softDeletes();
$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
});
}

public function down()
{
Schema::dropIfExists('order_outsource');
}


// リネーム
$table->renameColumn('estimate_id', 'id');


// seed
DB::table('users')->insert([
'id' => 8,
'name' => "name",
'name2' => "name2",
'position' => "",
'email' => "a_yamakami@yamakamikensetsu.net",
'password' => Hash::make('yamaken'),
]);
DB::table('users')->insert([
'id' => 9,
'name' => "name",
'name2' => "name",
'position' => "",
'email' => "shimizume@yamakamikensetsu.net",
'password' => Hash::make('yamaken'),
]);

// コメント
$table->text('contract')->after('billing_base')->nullable()->comment('契約書の有無');

■ロールバック
php artisan migrate:rollback --step=1

*
*/