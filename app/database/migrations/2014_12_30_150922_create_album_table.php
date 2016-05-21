<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
        Schema::create('albums', function($table){
            $table->increments('id')->unsigned();                 	//索引id
            $table->string('name',50);                            	//分类名称
            $table->integer('parent_id')->unsigned()->default(0); 	//父ID
            $table->tinyInteger('state')->default(0);             	//分类状态
            $table->tinyInteger('sort')->unsigned()->default(0); 	//排序
            $table->tinyInteger('is_show')->default(1);             	//前台显示，0为否，1为是，默认为1
            $table->timestamps();//创建时间created_at 更新时间updated_at

            $table->integer('store_id')->unsigned()->default(1); 	//店铺ID，1为系统后台发布（原来为0）
            $table->foreign('store_id')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
        Schema::drop('albums');
	}

}
