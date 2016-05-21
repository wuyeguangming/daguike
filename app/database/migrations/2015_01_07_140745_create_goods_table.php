<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up(){
        Schema::create('goods', function($table){
            $table->increments('id')->unsigned(); //商品索引id

            $table->integer('store_id')->unsigned();//店铺id
            $table->integer('category_id')->unsigned();//商品分类id
            $table->integer('sku_id')->unsigned()->nullable(); //商品默认对应的规格id?
            $table->integer('type_id')->unsigned()->default(0); //类型id
            // $table->integer('brand_id')->unsigned()->default(0)->nullable(); //商品品牌id，由于留作外键，0为无品牌
            // $table->integer('transport_id')->unsigned()->nullable(); //运费模板ID，不使用运费模板值为0
            // 地点信息根据store读取
            // $table->integer('city_id')->unsigned()->default(0)->nullable(); //商品所在地(市)
            // $table->integer('province_id')->unsigned()->default(0)->nullable(); //商品所在地(省)
            // 由created_at代替
            // $table->integer('add_time')->unsigned()->default(0); //商品添加时间
            $table->integer('start_time')->unsigned()->default(0); //发布开始时间
            $table->integer('end_time')->unsigned()->default(0); //发布结束时间
            $table->integer('click')->default(0); //商品浏览数
            $table->integer('comment_num')->unsigned()->default(0); //评论次数
            $table->integer('sale_num')->unsigned()->default(0); //售出数量
            $table->integer('collect')->unsigned()->default(0); //商品收藏数量
            // $table->integer('gold_num')->unsigned()->default(0); //直通车剩余金币额
            $table->integer('ztcgold_num')->unsigned()->default(0); //直通车剩余金币额
            $table->integer('ztcstartdate')->unsigned()->default(0)->nullable(); //开始时间
            $table->integer('ztclastdate')->unsigned()->default(NULL)->nullable(); //最后消费金币的时间 如果没有设置的话则按照没有减金币情况

            $table->tinyInteger('sku_open')->default(0); //商品规格开启状态，1开启，0关闭
            $table->tinyInteger('is_show')->default(0); //商品上架
            $table->tinyInteger('state')->default(0); //商品状态，0开启，1违规下架
            // $table->tinyInteger('commend')->default(0); //商品推荐
            $table->tinyInteger('recommend')->default(0); //商品推荐
            // $table->tinyInteger('form')->default(1); //商品类型,1为全新、2为二手
            $table->tinyInteger('oldlevel')->default(0); //商品类型,0为全新、其他为二手
            // $table->tinyInteger('store_state')->default(0); //商品所在店铺状态 0开启 1关闭
            $table->tinyInteger('isztc')->default(0); //是否是直通车商品 0不是 1是直通车商品
            $table->tinyInteger('ztcstate')->default(1); //直通车状态  1表示开启 2表示中止
            // $table->tinyInteger('group_flag')->unsigned()->default(0)->nullable(); //团购标识(团购1/非团购0)
            // $table->tinyInteger('xianshi_flag')->unsigned()->default(0); //限时折扣商品标志(1-是/0-不是)
            $table->tinyInteger('is_group')->default(0)->nullable(); //是否为团购(团购1/非团购0)
            $table->tinyInteger('is_flash')->default(0)->nullable(); //是否为闪购(1-是/0-不是)
            // $table->tinyInteger('transfee_charge')->unsigned()->default(0); //商品运费承担方式 默认 0为买家承担 1为卖家承担
            $table->tinyInteger('express_fee')->unsigned()->default(3); //商品运费承担方式 默认 0为买家承担 1为卖家承担' 3为免邮

            // $table->decimal('py_price', 10,2)->default(0)->nullable(); //平邮
            // $table->decimal('kd_price', 10,2)->default(0)->nullable(); //快递
            // $table->decimal('es_price', 10,2)->default(0)->nullable(); //EMS
            $table->decimal('price',10,2); //商品店铺价格，主要用于索引和显示
            $table->decimal('group_price', 10,2)->default(0); //团购价格
            // $table->decimal('xianshi_discount', 10,2)->default(0); //限时折扣率
            $table->decimal('flash_discount', 10,2)->default(0); //闪购折扣率

            $table->string('name',100); //商品名称
            $table->string('subtitle', 200); //商品副标题
            // $table->string('category_name',200); //商品分类名称
            $table->string('sku_name',255)->default(''); //规格名称
            $table->string('image',100); //商品默认封面图片
            // $table->string('store_price_interval', 30)->default(''); //商品价格区间
            $table->string('serial', 50)->default('')->nullable(); //商品货号
            $table->string('keywords', 255)->default('')->nullable(); //商品关键字
            $table->string('description', 255)->default('')->nullable(); //商品描述
            $table->string('close_reason', 255)->default('')->nullable(); //商品违规下架原因

            $table->text('image_more')->nullable(); //商品多图
            $table->text('body'); //商品详细内容
            $table->text('attr')->nullable(); //商品属性
            $table->text('sku')->nullable(); //商品规格
            $table->text('col_img')->default(NULL)->nullable(); //规格图片
            $table->text('sale_map')->default('')->nullable(); //销售区域 1:默认全网(为空'') 2:组合：[level:sid];[level:sid];...

            // new
            $table->integer('num')->unsigned()->default('1')->nullable(); // 库存数量（宝贝数量）
            $table->integer('album_id')->unsigned()->default(NULL)->nullable(); // 所属专辑id
            $table->decimal('vip_discount',10,2)->default('1')->nullable(); //会员折扣
            $table->dateTime('modified')->nullable(); //修改时间
            $table->string('upload_fail_msg',20)->default(NULL)->nullable(); //上传错误信息
            $table->integer('express_size')->default('0')->nullable(); //物流体积
            $table->integer('express_weight')->default('0')->nullable(); //物流重量
            $table->tinyInteger('sell_promise')->default('0')->nullable(); //退换货承诺
            $table->string('barcode',255)->default('')->nullable(); //商品条形码
            // $table->string('barcode',255)->default('')->default('0'); //sku 条形码

            $table->timestamps();//创建时间created_at 更新时间updated_at
            $table->softDeletes();
            
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('album_id')->references('id')->on('albums')->onUpdate('cascade')->onDelete('cascade');
            // 不能相互外键？!
            // $table->foreign('sku_id')->references('id')->on('skus')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('type_id')->references('id')->on('types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('goods');
    }

}
