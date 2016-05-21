<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrivateInfoToUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('users', function($table){
            $table->string('tel',11)->nullable();
            $table->string('real_name',80)->nullable();
            //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
            $table->integer('sex')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('users', function($table){
            $table->dropColumn(array('tel','real_name','sex'));
        });
    }

}
