<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('stores', function($table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();  //店铺索引id
            $table->string('name',50); //店铺名称
            $table->tinyInteger('store_auth')->default(0); //店铺认证
            $table->tinyInteger('name_auth')->default(0); //店主认证
            $table->integer('grade_id')->default(0);//店铺等级
            $table->integer('user_id')->unsigned()->index();//会员id member_id
            $table->string('username',50);//会员名称 member_name
            $table->string('owner_card',50)->default('');//身份证
            $table->integer('sc_id')->default(0);//店铺分类 shop class id
            $table->integer('area_id')->default(0);//地区id
            $table->string('area_info',100)->nullable();//地区内容，冗余数据
            $table->string('address',100)->nullable();//详细地区
            $table->string('zip',10)->nullable();//邮政编码
            $table->string('tel',50)->nullable();//电话号码
            $table->string('sms',200)->default('');//短信接口字段
            $table->string('image',100)->nullable();//证件上传
            $table->string('image1',100)->nullable();//执照上传
            $table->tinyInteger('state')->default(2);//店铺状态，0关闭，1开启，2审核中
            $table->string('close_info',255)->nullable();//店铺关闭原因
            $table->integer('sort')->default(0);//店铺排序
            $table->string('time',10);//店铺时间
            $table->string('end_time',10)->nullable();//店铺关闭时间
            $table->string('label',255)->nullable();//店铺logo
            $table->string('banner',255)->nullable();//店铺横幅
            $table->string('logo',255)->nullable();//店标
            $table->string('keywords',255)->default('');//店铺seo关键字
            $table->string('description',255)->default('');//店铺seo描述
            $table->string('qq',50)->nullable();//QQ
            $table->string('ww',50)->nullable();//阿里旺旺
            $table->text('zy')->nullable();//主营商品
            $table->string('domain',50)->nullable();//店铺二级域名
            $table->tinyInteger('domain_times')->unsigned()->default(0);//二级域名修改次数
            $table->tinyInteger('recommend')->default(0);//推荐，0为否，1为是，默认为0
            $table->string('theme',50)->default('default');//店铺当前主题'
            $table->integer('credit')->default(0);//店铺信用
            $table->float('praise_rate')->default(0);//店铺好评率
            $table->float('desccredit')->default(0);//描述相符度分数
            $table->float('servicecredit')->default(0);//服务态度分数
            $table->float('deliverycredit')->default(0);//发货速度分数
            $table->string('code',255)->default('default_qrcode.png');//店铺二维码
            $table->integer('collect')->unsigned()->default(0);//店铺收藏数量
            $table->text('slide')->nullable();//店铺幻灯片
            $table->text('slide_url')->nullable();//店铺幻灯片链接
            $table->text('center_quicklink')->nullable();//卖家中心的常用操作快捷链接
            $table->string('stamp',200)->nullable();//店铺印章
            $table->string('printdesc',500)->nullable();//打印订单页面下方说明文字
            $table->integer('sales')->unsigned()->default(0);//店铺销量
            $table->text('presales')->nullable();//售前客服
            $table->text('aftersales')->nullable();//售后客服
            $table->string('workingtime',100)->nullable();//工作时间
            $table->integer('loc_province')->unsigned()->nullable();
            $table->integer('loc_city')->unsigned()->nullable();
            $table->integer('loc_district')->unsigned()->nullable();
            $table->integer('loc_community')->unsigned()->nullable();
            $table->timestamps();//创建时间created_at 更新时间updated_at

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->index('id');
            $table->index('name');
            $table->index('sc_id');
            $table->index('area_id');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('stores');
    }

}
