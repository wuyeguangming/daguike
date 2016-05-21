<?php
require_once app_path().'/common/Wechat/Wechat.php';
require_once app_path().'/common/Wechat/errCode.php';

class WxOrderController  extends WxController {
    public function __construct(){
        parent::__construct();
        $this->getUser();
        if (empty($this->user)) {
            return $this->error('请先登录');
        }
    }

    public function getIndex($last=0){
        $this->data['order'] = Order::where('buyer_id','=',$this->user->id)->orderBy('created_at', 'desc')->skip($last)->take(5)->get();
        $this->data['order_goods'] = array();
        foreach ($this->data['order'] as $key => $order) {
            $this->data['order_goods'][$key] = $order->getOrderGoods();
        }
        return $this->display('',$this->data);
    }

    public function getDetail($order_id=''){
        $order = Order::where('buyer_id','=',$this->user->id)->where('id','=',$order_id)->first();
        if (!empty($order) && 1==$order->status && isWeixin()) {
            $this->js_auth();
            // 支付环节
            require_once app_path().'/common/Wechat/WxPayPubHelper/WxPayPubHelper.php';
            //=========步骤2：使用统一支付接口，获取prepay_id============
            $unifiedOrder = new UnifiedOrder_pub();//使用统一支付接口
            $unifiedOrder->setParameter("openid",$this->data['wx']['openid']);//商品描述
            $unifiedOrder->setParameter("body","大贵客");//商品描述
            $timeStamp = time();
            // $out_trade_no = Config::get('app.wx')['appid']."$timeStamp";//自定义订单号，此处仅作举例
            $order->sn_out = Order::newSnOut($order->buyer_id);
            $unifiedOrder->setParameter("out_trade_no","$order->sn_out");//"$out_trade_no"商户订单号 
            $total_fee = floatval($order->amount)*100;
            $unifiedOrder->setParameter("total_fee","$total_fee");//总金额
            $unifiedOrder->setParameter("notify_url",Config::get('app.wx')['notify_url']);//通知地址 
            $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
            //非必填参数，商户可根据实际情况选填
            //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
            //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
            //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
            //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
            //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
            //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
            //$unifiedOrder->setParameter("openid","XXXX");//用户标识
            //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
            $prepay_id = $unifiedOrder->getPrepayId();
            if (empty($prepay_id)) {
                return $this->error('',$unifiedOrder->result["return_msg"]);
            }
            //=========步骤3：使用jsapi调起支付============
            $jsApi = new JsApi_pub();
            $jsApi->setPrepayId($prepay_id);
            $this->data['wx']['pay'] = $jsApi->getParameters();
            $order->update();//更新sn_out
        }
        $this->data['order'] = $order;
        $this->data['order']['hongbao'] = $order->hongbao;
        $this->data['order_goods'] = $order->getOrderGoods();
        $this->data['address'] = $order->getAddress();
        if (!empty($this->data['address'])) {
            // $this->data['location'] = Location::getLocAllByNodeSid($this->data['address']['loc_room']);
            $this->data['location'] = Location::getLocAllByNodeSid($this->data['address']['loc_building']);
        }
        return $this->display('',$this->data);
    }

    public function postCancel($order_id){
        require_once app_path().'/common/yunpian/sms.php';
        $order = Order::where('buyer_id','=',$this->user->id)->where('id','=',$order_id)->first();
        $title = '取消订单';
        if (0==$order->pay_type) {
            $title = '【申请退款】'.$title;
        }
        $this->sendEmailToAdmin($title,'emails/admin/order/notify',array('notify' => $title.$order->sn));
        return $this->result($order->cancel());
    }

    public function postRefund($id){
        $order = Order::find($id);
        if (($order->buyer_id == $this->user->id)&&(5==$order->status)) {
            $order->status = 6;//退款中
            return $this->result($order->save());
        }
    }
}