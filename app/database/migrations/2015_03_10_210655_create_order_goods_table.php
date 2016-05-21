<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderGoodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('order_goods', function($table){
            $table->timestamps();
            $table->increments('id')->unsigned();                                       // 订单商品表索引
            $table->integer('num')->unsigned();                                         // 商品数量
            $table->integer('returnnum')->unsigned()->default(0)->nullable();           // 退货数量
            $table->bigInteger('order_id')->unsigned();                                 // 订单id
            // $table->integer('store_id')->default(0);                                  // 店铺ID
            $table->integer('sku_id')->unsigned();                                      // 规格id
            $table->string('sku_value',50)->default('');                                // 规格描述
            $table->decimal('sku_price',10,2);                                          // sku价格
            $table->string('sku_serial',50)->default('');                                // 规格编码
            $table->integer('goods_id')->unsigned();                                    // 商品id
            $table->string('goods_name',100);                                           // 商品名称 缓存，减少二次查询
            // $table->decimal('goods_price',10,2);                                        // 商品价格
            $table->string('goods_image',100)->default(NULL)->nullable();               // 商品图片、缩略图
            
            // todo
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('sku_id')->references('id')->on('skus');
            $table->foreign('goods_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('order_goods');
    }

}
