<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyLimitToGoodsTable extends Migration {
   
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('goods', function($table){
            $table->integer('buy_limit')->unsigned()->default(0)->nullable(); //限购数量
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('goods', function($table){
            $table->dropColumn(array('buy_limit'));
        });
    }


}
