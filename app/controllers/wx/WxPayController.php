<?php

class WxPayController  extends PublicController {
    // 用于Fiddler处理
    private function debug($value=''){
        if (('/wx/pay/notify?' == $_SERVER['REQUEST_URI']) ) {
            echo $value;
        }
    }
    /**
     * 通用通知接口demo
     * ====================================================
     * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
     * 商户接收回调信息后，根据需要设定相应的处理流程。
     * 
     * 这里举例使用log文件形式记录回调信息。
    */
    public function postNotify(){
        include_once app_path().'/common/Wechat/WxPayPubHelper/WxPayPubHelper.php';
        // include_once("./log_.php");

        //使用通用通知接口
        $notify = new Notify_pub();

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];  
        $notify->saveData($xml);
        
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
        
        // //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
        if($notify->checkSign() == TRUE){
            if ($notify->data["return_code"] == "FAIL") { //通信出错
                //此处应该更新一下订单状态，商户自行增删操作
            }
            elseif($notify->data["result_code"] == "FAIL"){ //业务出错
                //此处应该更新一下订单状态，商户自行增删操作
            }
            else{ // 支付成功
                //此处应该更新一下订单状态，商户自行增删操作
                $orders = Order::where('sn_out','=',$notify->data['out_trade_no'])->get();
                if (1 != count($orders)) {
                    echo "无法找到该订单，或该订单已被删除";
                    return;
                }
                if ( 1 == $orders[0]->status){
                    $orders[0]->status = 2;
                    $orders[0]->wx_transaction_id = $notify->data['transaction_id'];
                    $orders[0]->pay_time = date('Y-m-d H:i:s',time());
                    if($orders[0]->update()){// 成功处理
                        // todo hongbao 更新
                        // $hongbao = $order[0]->hongbao;
                        // if (!empty($hongbao)) {
                        //     $hongbao->iused($order[0]);
                        // }
                        // todo sku 更新
                        $skus = [];
                        $order_nums = [];
                        foreach ($orders[0]->order_goods()->get() as $key => $order_goods) {
                            $sku = $order_goods->sku;
                            $sku->decrease($order_goods->num); //todo 判断
                            $skus[] = $sku;
                            $order_nums[] = $order_goods->num;
                        }
                        // require_once app_path().'/common/yunpian/sms.php';
                        // $sms = new sms;
                        // $sms->order_notify($orders[0],$skus,$order_nums,13656633843,true);
                        $notify = Order::email_notify($orders[0],$skus,$order_nums,true);
                        $this->sendEmailToAdmin('支付成功','emails/admin/order/notify',array('notify' => $notify));
                        //商户自行增加处理流程,
                        //例如：更新订单状态
                        //例如：数据库操作
                        //例如：推送支付完成信息
                        $this->debug("订单更新成功");
                    }else{
                        $this->debug("订单更新失败");
                    }
                }else{
                    $this->debug("改订单已处理");
                }
                // todo..错误处理
            }
        }

        // todo log
        // //以log文件形式记录回调信息
        // $log_ = new Log_();
        // $log_name="./notify_url.log";//log文件路径
        // $log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
    }
}