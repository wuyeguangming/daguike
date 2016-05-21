<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreClassTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('store_class', function($table){
            $table->increments('id')->unsigned();   //索引id
            $table->string('name',100); //分类名称
            $table->integer('parent_id')->unsigned()->default(0);; //父层id
            $table->tinyInteger('sort')->unsigned()->default(0);; //排序
            $table->timestamps();//创建时间created_at 更新时间updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('store_class');
    }

}
