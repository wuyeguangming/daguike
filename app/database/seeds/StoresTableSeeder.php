<?php

class StoresTableSeeder extends Seeder {

    public function run(){
        DB::table('stores')->delete();
        $stores = array(
            array(
                'id' => '1',
                'name' => '大贵客',
                'store_auth' => '1',
                'name_auth' => '1',
                'grade_id' => '1',
                'user_id' => '1',
                'username' => 'admin',
                'owner_card' => '123456712345674567',
                'sc_id' => '0',
                'area_id' => '58',
                'area_info' => '浙江 杭州市 江干区',
                'address' => '天城东路230号 中沙金座4幢 2027',
                'zip' => '300000',
                'tel' => '2345678',
                'sms' => '',
                'image' => '',
                'image1' => '',
                'state' => '1',
                'close_info' => '',
                'sort' => '0',
                // 'time' => '1291617125', //created_at代替
                'end_time' => '',
                'label' => NULL,
                'banner' => '',
                'logo' => '',
                'keywords' => '',
                'description' => '',
                'qq' => '',
                'ww' => '',
                'description' => '',
                'zy' => NULL,
                'domain' => '',
                'domain_times' => '0',
                'recommend' => '1',
                'theme' => 'default',
                'credit' => '0',
                'praise_rate' => '0',
                'desccredit' => '0',
                'servicecredit' => '0',
                'deliverycredit' => '0',
                'code' => 'default_qrcode.png',
                'collect' => '0',
                'slide' => NULL,
                'slide_url' => NULL,
                'center_quicklink' => NULL,
                'stamp' => NULL,
                'printdesc' => NULL,
                'sales' => '0',
                'presales' => NULL,
                'aftersales' => NULL,
                'workingtime' => NULL,
                'workingtime' => NULL,
                'loc_province' => 1,
                'loc_city' => 2,
                'loc_district' => 3,
                'loc_community' => 4,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            )
        );
        DB::table('stores')->insert( $stores );
        // 更新user中的store_id
        DB::table('users')->where('id',1)->update(array('store_id'=>1));
    }

}
