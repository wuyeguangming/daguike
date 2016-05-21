<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeixinInfoToUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('users', function($table){
            //用户的标识，对当前公众号唯一
            $table->string('wx_openid',28)->nullable()->unique();
            //用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
            $table->integer('wx_subscribe')->unsigned()->nullable();
            //用户的昵称
            $table->string('wx_nickname',50)->nullable();
            //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
            $table->integer('wx_sex')->unsigned()->nullable();
            //用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
            $table->string('wx_headimgurl',200)->nullable();
            //用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
            $table->integer('wx_subscribe_time')->unsigned()->nullable();
            //只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
            $table->string('wx_unionid',28)->nullable();
            // $table->string('wx_city')->nullable();          //用户所在城市
            // $table->string('wx_country')->nullable();       //用户所在国家
            // $table->string('wx_province')->nullable();      //用户所在省份
            // $table->string('wx_language')->nullable();      //用户的语言，简体中文为zh_CN
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('users', function($table){
            $table->dropColumn(array('wx_subscribe','wx_openid','wx_nickname','wx_sex','wx_headimgurl','wx_subscribe_time','wx_unionid'));
        });
    }
}
