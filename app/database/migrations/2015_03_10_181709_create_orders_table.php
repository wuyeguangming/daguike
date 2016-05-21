<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('orders', function($table){
            $table->timestamps();
            $table->softDeletes();

            // 注意order_id是bigInteger！！！
            $table->bigIncrements('id')->unsigned();                                      //订单索引id
            $table->string('sn',50)->unique();                                            //订单编号，商城内部使用
            $table->decimal('amount',10,2);                                               //订单总价格（含折扣等）
            $table->decimal('goods_amount',10,2);                                         //商品总价格（不含折扣）
            $table->integer('address_id')->unsigned();                                                //收货地址 id
            $table->integer('buyer_id')->unsigned();                                                  //买家id
            $table->integer('store_id')->unsigned();                                                  //店铺id
            //订单状态：['已取消','等待支付','下单成功','正在配货','已发货','交易成功','申请退款中','退款成功','退款失败','无人收货']
            $table->tinyInteger('status')->default(1)->nullable();                        
            $table->tinyInteger('type')->default(0)->nullable();                          //订单类型 0.普通 1.团购
            $table->string('message',300)->default('')->nullable();                        //订单留言
            $table->tinyInteger('ifrom')->unsigned()->default(0)->nullable();              //0微信 1手机端 2PC 区别保留字from
            $table->string('invoice',100)->default('')->nullable();                         //发票信息
            $table->decimal('discount',10,2)->default(0)->nullable();                       //折扣价格
            $table->tinyInteger('mark_status')->default(0)->nullable();                     //评价状态 0为未评价，1卖家已评价，2买家已评价，3双方已评价
            $table->string('sn_out',50)->default('')->nullable()->unique();               //订单编号，外部支付时使用，有些外部支付系统要求特定的订单编号
            $table->integer('points')->unsigned()->default(0)->nullable();                //订单赠送积分
            $table->dateTime('finish_time')->nullable();                                  //订单完成时间

            $table->dateTime('delivery_now');                                               //立即配送
            $table->dateTime('delivery_time');                                               //配送时间
            $table->tinyInteger('delivery_type')->default(0)->nullable();                 //配送公司 0为官方配送
            $table->integer('delivery_address_id')->unsigned()->nullable();                            //发货地址ID
            $table->string('delivery_code',50)->default('')->nullable();                     //物流单号
            $table->decimal('delivery_fee',10,2)->default(0)->nullable();                    //运费价格
            $table->tinyInteger('delivery_name')->default(0)->nullable();                  //配送方式
            $table->text('delivery_message')->nullable();                                  //发货备注
                  
            $table->tinyInteger('refund_status')->unsigned()->default(0)->nullable();       //退款状态:0是无退款,1是部分退款,2是全部退款
            $table->decimal('refund_amount',10,2)->default(0)->nullable();                  //退款金额

            $table->tinyInteger('return_status')->unsigned()->default(0)->nullable();       //退货状态:0是无退货,1是部分退货,2是全部退货
            $table->integer('return_num')->unsigned()->default(0)->nullable();              //退货数量
                
            $table->integer('coupon_id')->unsigned()->default(NULL)->nullable();            //代金券id
            $table->decimal('coupon_amount',10,2)->default(0)->nullable();                   //代金券面额
            $table->string('coupon_code',32)->default('')->nullable();                      //代金券编码
            
            $table->string('wx_transaction_id',32)->default('')->nullable();                 //微信支付订单号，加上sn_out可查询该订单信息

            $table->tinyInteger('pay_type')->unsigned()->default(0)->nullable();           //支付方式 0 微信支付 1 货到付款 2 余额支付
            $table->dateTime('pay_time')->nullable();                                     //支付(付款)时间
            // $table->integer('payment_id')->default(0)->nullable();                            //支付方式id
            // $table->string('payment_name',50);                                                //支付方式名称
            // $table->string('payment_code',50);                                                //支付方式名称代码
            // $table->tinyInteger('payment_type')->default(0)->nullable();                      //支付类型:0微信支付，1货到付款
            // $table->string('payment_out_code',255)->default('')->nullable();                  //外部交易平台单独使用的标识字符串
            // $table->string('payment_message',300)->default('')->nullable();                   //支付留言
  
            // $table->integer('group_id')->unsigned()->default('0')->nullable();            //团购编号(非团购订单为0)
            // $table->integer('group_count')->unsigned()->default('0');                     //团购数量

            // $table->integer('xianshi_id')->unsigned()->default('0');                      //限时折扣编号
            // $table->string('xianshi_explain',100)->default('');                           //限时折扣说明

            // $table->integer('mansong_id')->unsigned()->default('0');                      //满就送编号
            // $table->string('mansong_explain',200)->default('');                           //满就送说明

            // $table->integer('bundling_id')->default(NULL)->nullable();                    //搭配套餐id
            // $table->string('bundling_explain',100)->default(NULL)->nullable();            //搭配套餐说明
            // $table->bigInteger('hongbao_id')->unsigned()->nullable();                      //红包id


            // $table->foreign('buyer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('address_id')->references('id')->on('addresses')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('delivery_address_id')->references('id')->on('addresses')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('coupon_id')->references('id')->on('coupons')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('orders');
    }

}
