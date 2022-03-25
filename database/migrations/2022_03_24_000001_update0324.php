<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update0324 extends Migration
{
    public function up()
    {
        Schema::create('account', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usr_id')->nullable();
            $table->string('name')->nullable();
        });
        Schema::create('play', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable()->comment('日付');
            $table->integer('dedama')->nullable()->comment('出玉メダル');
            $table->integer('syushi')->nullable()->comment('収支円');
            $table->integer('floor')->nullable();
            $table->integer('daiban')->nullable();
            $table->timestamp('lastUpdate')->nullable()->comment('最終更新');
        });

        //seed
        DB::table('account')->insert([
            'usr_id' => '4634',
            'name' => "EL50",
        ]);
        DB::table('account')->insert([
            'usr_id' => '4646',
            'name' => "EL51",
        ]);
        DB::table('account')->insert([
            'usr_id' => '4647',
            'name' => "EL52",
        ]);
        DB::table('account')->insert([
            'usr_id' => '4649',
            'name' => "EL53",
        ]);
        DB::table('account')->insert([
            'usr_id' => '4651',
            'name' => "EL54",
        ]);
    }


    public function down()
    {
        Schema::dropIfExists('account');
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