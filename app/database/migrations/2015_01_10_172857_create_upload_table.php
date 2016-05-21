<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('uploads', function($table){
            $table->increments('id')->unsigned(); //索引ID
            $table->string('name', 100)->default(NULL); //文件名
            $table->integer('size')->unsigned()->default(0);//文件大小
            $table->integer('store_id')->unsigned()->default(1);//店铺ID，1为管理员
            $table->integer('item_id')->unsigned()->default(0);//信息ID
            //文件类别, 0为无(默认), 1为文章图片, 2为商品切换图片, 3为商品内容图片, 4为系统文章图片, 5为积分礼品切换图片, 6为积分礼品内容图片
            // $table->tinyInteger('type')->default(0);
            // $table->integer('upload_time')->unsigned()->default(0);//添加时间
            $table->timestamps();//创建时间created_at 更新时间updated_at            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('uploads');
    }

}
