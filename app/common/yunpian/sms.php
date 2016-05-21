<?php
/**
* sms
*/
class sms{
    private $apikey = 'a6cf4bad2e7359414a2acc96014fdcbb';
    /**
    * url 为服务的url地址
    * query 为请求串
    */
    public function sock_post($url,$query){
        $data = "";
        $info=parse_url($url);
        $fp=fsockopen($info["host"],80,$errno,$errstr,30);
        if(!$fp){
            return $data;
        }
        $head="POST ".$info['path']." HTTP/1.0\r\n";
        $head.="Host: ".$info['host']."\r\n";
        $head.="Referer: http://".$info['host'].$info['path']."\r\n";
        $head.="Content-type: application/x-www-form-urlencoded\r\n";
        $head.="Content-Length: ".strlen(trim($query))."\r\n";
        $head.="\r\n";
        $head.=trim($query);
        $write=fputs($fp,$head);
        $header = "";
        while ($str = trim(fgets($fp,4096))) {
            $header.=$str;
        }
        while (!feof($fp)) {
            $data .= fgets($fp,4096);
        }
        return $data;
    }

    /**
    * 模板接口发短信
    * apikey 为云片分配的apikey
    * tpl_id 为模板id
    * tpl_value 为模板值
    * mobile 为接受短信的手机号
    */
    public function tpl_send($tpl_id, $tpl_value, $mobile){
        $apikey = $this->apikey;
        $url="http://yunpian.com/v1/sms/tpl_send.json";
        $encoded_tpl_value = urlencode("$tpl_value");
        $post_string="apikey=$apikey&tpl_id=$tpl_id&tpl_value=$encoded_tpl_value&mobile=$mobile";
        return $this->sock_post($url, $post_string);
    }

    /**
    * 普通接口发短信
    * apikey 为云片分配的apikey
    * text 为短信内容
    * mobile 为接受短信的手机号
    */
    public function send($text, $mobile){
        $apikey = $this->apikey;
        $url="http://yunpian.com/v1/sms/send.json";
        $encoded_text = urlencode("$text");
        $post_string="apikey=$apikey&text=$encoded_text&mobile=$mobile";
        return $this->sock_post($url, $post_string);
    }

    public function confirm($code,$mobile){
        return $this->send('【大贵客】您的验证码是'.$code,$mobile);
    }

    public function order_notify($order,$skus,$sku_num,$mobile,$completed=false){
        $sms_sku_str = '[';
        foreach ($skus as $index => $sku) {
            $sms_sku_str = $sms_sku_str.$sku->serial.'x'.$sku_num[$index].'/';
        }
        $sms_sku_str = $sms_sku_str.']';

        $address = Address::find($order->address_id);
        // $loc_room = Location::where('level','=',6)->where('sid','=',$address->loc_room)->first();
        $loc_building = Location::where('level','=',Location::LEVEL_BUILDING)->where('sid','=',$address->loc_building)->first();
        $loc_community = Location::where('level','=',Location::LEVEL_COMMUNITY)->where('sid','=',$loc_building->parent)->first();
        $amount = $order->amount;
        if (1==$order->pay_type) {
            $amount = '[货到付款]-'.$amount;
        }else if (0==$order->pay_type) {
            $amount = ($completed ? ('[支付成功]'.$amount) : ('[等待微信支付]'.$amount));
        }
        $delivery_time = $order->delivery_time;
        $now = time();
        $dt = strtotime($delivery_time);
        if ($dt - $now> 30*60) {//半小时后配送
            $delivery_time = '[预约配送]'.$delivery_time;
        }
        $this->tpl_send(
            Config::get('app.sms.tpl.store.order_notify', true),
            "#time#=".$delivery_time."&#amount#=".$amount."&#location#=".$address->name.' '.$address->phone.' '.$loc_community->name.$loc_building->name.$address->loc_detail."&#sku#=".$sms_sku_str."&#sn#=".$order->sn
            ,$mobile
        );
    }

    public function order_cancel($order,$mobile){
        $this->tpl_send(
            Config::get('app.sms.tpl.store.order_cancel', true),
            "#order#=".$order->sn
            ,$mobile
        );
    }
}