<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('skus', function($table){
            $table->increments('id')->unsigned(); //索引id
            $table->integer('goods_id')->unsigned(); //商品id
            $table->string('name',255)->default(''); //名称
            $table->decimal('price',10,2); //价格
            $table->integer('num')->default(0); //库存
            $table->tinyInteger('volume')->default(0); //体积
            $table->integer('salenum')->default(0);//售出数量
            $table->string('serial',50)->default(''); //编号
            $table->text('value');  //序列化            
            $table->timestamps();//创建时间created_at 更新时间updated_at

            $table->foreign('goods_id')->references('id')->on('goods')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('skus');
    }
}
