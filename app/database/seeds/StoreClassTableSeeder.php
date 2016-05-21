<?php

class StoreClassTableSeeder extends Seeder {

    public function run()
    {
        DB::table('store_class')->delete();

        $store_class = array(
            array('id' => '1','name' => '珠宝/首饰','parent_id' => '0','sort' => '8'),
            array('id' => '5','name' => '3C数码','parent_id' => '0','sort' => '2'),
            array('id' => '6','name' => '美容护理','parent_id' => '0','sort' => '3'),
            array('id' => '7','name' => '家居用品','parent_id' => '0','sort' => '4'),
            array('id' => '8','name' => '食品/保健','parent_id' => '0','sort' => '5'),
            array('id' => '9','name' => '母婴用品','parent_id' => '0','sort' => '6'),
            array('id' => '10','name' => '文体/汽车','parent_id' => '0','sort' => '7'),
            array('id' => '11','name' => '收藏/爱好','parent_id' => '0','sort' => '9'),
            array('id' => '12','name' => '生活/服务','parent_id' => '0','sort' => '10'),
            array('id' => '19','name' => '电脑硬件/显示器/电脑周边','parent_id' => '5','sort' => '1'),
            array('id' => '20','name' => '手机','parent_id' => '5','sort' => '2'),
            array('id' => '21','name' => '笔记本电脑','parent_id' => '5','sort' => '3'),
            array('id' => '22','name' => '网络设备/路由器/网络相关','parent_id' => '5','sort' => '4'),
            array('id' => '23','name' => '数码相机/摄像机/摄影器材','parent_id' => '5','sort' => '5'),
            array('id' => '24','name' => 'mp3/mp4/iPod/录音笔','parent_id' => '5','sort' => '6'),
            array('id' => '25','name' => '电玩/配件/游戏/攻略','parent_id' => '5','sort' => '7'),
            array('id' => '26','name' => '影音电器','parent_id' => '5','sort' => '8'),
            array('id' => '27','name' => '厨房电器','parent_id' => '5','sort' => '9'),
            array('id' => '28','name' => '生活电器','parent_id' => '5','sort' => '10'),
            array('id' => '29','name' => '男士内衣/女士内衣/家居服','parent_id' => '4','sort' => '7'),
            array('id' => '30','name' => '箱包皮具/热销女包/男包','parent_id' => '4','sort' => '8'),
            array('id' => '31','name' => '服饰配件/皮带/帽子/围巾','parent_id' => '4','sort' => '9'),
            array('id' => '32','name' => '美容护肤/美体/精油','parent_id' => '6','sort' => '255'),
            array('id' => '33','name' => '彩妆/香水/美发工具','parent_id' => '6','sort' => '255'),
            array('id' => '34','name' => '国货精品/开架化妆品','parent_id' => '6','sort' => '255'),
            array('id' => '35','name' => '家居日用/收纳/礼品','parent_id' => '7','sort' => '255'),
            array('id' => '36','name' => '厨房/餐饮用具','parent_id' => '7','sort' => '255'),
            array('id' => '37','name' => '日化/清洁/护理','parent_id' => '7','sort' => '255'),
            array('id' => '38','name' => '床上用品/靠垫/毛巾/布艺','parent_id' => '7','sort' => '255'),
            array('id' => '39','name' => '零食/坚果/茶叶/地毯','parent_id' => '8','sort' => '1'),
            array('id' => '40','name' => '滋补/生鲜/速食/订餐','parent_id' => '8','sort' => '2'),
            array('id' => '41','name' => '保健食品','parent_id' => '8','sort' => '3'),
            array('id' => '42','name' => '奶粉/辅食/营养品','parent_id' => '9','sort' => '255'),
            array('id' => '43','name' => '尿片/洗护/喂哺用品','parent_id' => '9','sort' => '255'),
            array('id' => '44','name' => '益智玩具/早教/童车床/出行','parent_id' => '9','sort' => '255'),
            array('id' => '45','name' => '童装/童鞋/孕妇装','parent_id' => '9','sort' => '255'),
            array('id' => '46','name' => '运动/瑜伽/健身/球迷用品','parent_id' => '10','sort' => '1'),
            array('id' => '47','name' => '户外/登山/野营/旅行','parent_id' => '10','sort' => '2'),
            array('id' => '48','name' => '汽车/配件/改装/摩托/自行车','parent_id' => '10','sort' => '3'),
            array('id' => '49','name' => '书籍/杂志/报纸','parent_id' => '10','sort' => '4'),
            array('id' => '50','name' => '宠物/宠物食品及用品','parent_id' => '10','sort' => '5'),
            array('id' => '51','name' => '音乐/影视/音像','parent_id' => '10','sort' => '6'),
            array('id' => '52','name' => '乐器/吉他/钢琴/配件','parent_id' => '10','sort' => '7'),
            array('id' => '53','name' => '办公设备/文具/耗材','parent_id' => '10','sort' => '8'),
            array('id' => '54','name' => '珠宝/钻石/翡翠/黄金','parent_id' => '1','sort' => '1'),
            array('id' => '55','name' => '饰品流行/首饰/时尚饰品','parent_id' => '1','sort' => '2'),
            array('id' => '56','name' => '品牌手表/流行手表','parent_id' => '1','sort' => '3'),
            array('id' => '57','name' => '玩具/模型/娃娃/人偶','parent_id' => '11','sort' => '1'),
            array('id' => '58','name' => '古董/邮币/字画/收藏','parent_id' => '11','sort' => '2'),
            array('id' => '59','name' => 'ZIPPO/瑞士军刀/眼镜','parent_id' => '11','sort' => '3'),
            array('id' => '60','name' => '鲜花速递/蛋糕配送/园艺花艺','parent_id' => '12','sort' => '1'),
            array('id' => '61','name' => '演出/吃喝玩乐折扣券','parent_id' => '12','sort' => '2'),
            array('id' => '62','name' => '酒店客栈/景点门票/度假旅游','parent_id' => '12','sort' => '3'),
            array('id' => '63','name' => '网店/网络服务/个性定制/软件','parent_id' => '12','sort' => '4'),
            array('id' => '64','name' => '成人用品/避孕/计生用品','parent_id' => '12','sort' => '5')
        );

        DB::table('store_class')->insert( $store_class );
    }

}
