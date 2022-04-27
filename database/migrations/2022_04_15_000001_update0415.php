<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update0415 extends Migration
{
    public function up()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->string('sid')->nullable();
            $table->boolean('go')->nullable();
        });
        Schema::create('cinnamonPatrol', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hall')->nullable();
            $table->date('date')->nullable()->comment('日付');
            $table->integer('floor')->nullable();
            $table->string('kisyu')->nullable();
            $table->integer('kisyuId')->nullable();
            $table->boolean('type')->nullable();
            $table->integer('rate')->nullable();
            $table->integer('kankin')->nullable();
            $table->integer('border')->nullable();
            $table->integer('tenjyo')->nullable();
            $table->integer('ren')->nullable();
            $table->integer('borderPla')->nullable();
            $table->integer('tenjyoPla')->nullable();
            $table->integer('renPla')->nullable();
            $table->integer('hatu')->nullable();
            $table->string('target')->nullable();
            $table->string('ng')->nullable();
            $table->boolean('go')->nullable();
            $table->boolean('template')->nullable()->default(0);
        });
        Schema::table('dai', function (Blueprint $table) {
            $table->boolean('tyakuseki')->nullable();
            $table->boolean('vgCheckSumi')->nullable();
        });

    }


    public function down()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('sid');
            $table->dropColumn('go');
        });
        Schema::table('dai', function (Blueprint $table) {
            $table->dropColumn('tyakuseki');
            $table->dropColumn('vgCheckSumi');
        });
        Schema::dropIfExists('cinnamonPatrol');
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