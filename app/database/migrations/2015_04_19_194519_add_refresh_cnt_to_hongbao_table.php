<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRefreshCntToHongbaoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('hongbaos', function($table){
        	// 刷新次数
            $table->integer('refresh_cnt')->unsigned()->default(0)->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('hongbaos', function($table){
            $table->dropColumn(array('refresh_cnt'));
        });
	}

}
