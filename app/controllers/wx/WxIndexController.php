<?php
require_once app_path().'/common/Wechat/Wechat.php';
require_once app_path().'/common/Wechat/errCode.php';

class WxIndexController  extends WxController {
    public function __construct(){
        parent::__construct();
        $this->getUser();
    }

    // 清除session
    public function getLogout(){
        $_SESSION = array();
        session_destroy();
        echo "成功退出！";
    }

    public function getApi(){
        $this->wx->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $this->wx->getRev()->getRevType();
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $this->wx->text("你好，大贵客！")->reply();
                exit;
                break;
            case Wechat::MSGTYPE_EVENT:
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $this->wx->text("你好，大贵客！")->reply();
        }
    }

    public function postApi(){
        $this->getApi();
    }

    public function getIndex(){
        if (empty($this->user)) {
            $this->data['goods_list'] = array();
        }else{
            $this->js_auth();
            $this->data['goods_list'] = Goods::where('sale_map','like','')
                // ->orWhere('sale_map','like','%4:'.$this->wxuser->loc_community.'%') //暂时业务还未涉及3/2/1 ->orWhere('sale_map','like',3/2/1..)
                ->orWhere('sale_map','like','%4:'.$this->user->loc_community.'%') //暂时业务还未涉及3/2/1 ->orWhere('sale_map','like',3/2/1..)
                ->orderBy('updated_at', 'desc')->take(30)->get();
            foreach ($this->data['goods_list'] as $i => $goods) {
                $this->data['goods_list'][$i]->num = 0;
                foreach ($goods->skus as $ii => $sku) {
                    $this->data['goods_list'][$i]->num += $sku->num;
                }
            }
            $this->data['hongbao_num'] = $this->user->hongbao->count();
        }
        $this->data['albums'] = Store::find(1)->albums();
        return $this->display('大贵客',$this->data);
    }

    public function getGoods($goods_id){
        $goods = Goods::find($goods_id);
        if (empty($goods)) {
            $this->data['goods'] = $goods;
        }else{
            $goods->click = $goods->click + 1;
            $goods->save();
            $this->data['goods'] = $goods->output();
        }
        return $this->display('',$this->data);
    }

    // public function getAddress(){
    //     $this->data['location'] = Location::all();
    //     // $this->data['addresses'] = Address::getByUser($this->user);
    //     return $this->display('',$this->data);
    // }

    // todo: 去重合
    public function postAddress(){
        $post = Input::all();
        $address = Address::find($post['address']['id']);
        if(empty($address)){
            $address = new Address;
        }
        $address->user_id = $this->user->id;
        $address->name  = $post['address']['name'];
        $address->phone  = $post['address']['phone'];
        $address->note  = $post['address']['note'];
        $address->loc_detail  = $post['address']['loc_detail'];
        // $address->loc_room  = $post['address']['selects'][Location::LEVEL_ROOM]['sid'];
        $address->loc_building  = $post['address']['selects'][Location::LEVEL_BUILDING]['sid'];
        return $this->result($address->save());
    }

    public function getSetting($fn){
        if ('hongbao' == $fn) {
            $this->data['hongbao'] = Hongbao::where('user_id','=',$this->user->id)->orderBy('created_at','desc')->limit(10)->get();
        }else if ('nolocation' == $fn) {
            $this->data['location'] = Location::where('level','=',4)->get();
        }else{
            // $this->data['location'] = Location::all();
            $this->data['location'] = Location::where('level','<',6)->get();
        }
        return $this->display('',$this->data);
    }

    public function postSetting(){
        $is_new_user = false;
        $post = Input::all();
        if (!empty($post['location'])) {
            $user = array();
            if ('settingNolocation' == $this->_controller) {
                if (empty($this->user)) {
                    $is_new_user = true;
                    $this->user = User::createByWx($this->data['wx']['openid']);
                }
                $loc = Location::getLocAllByNode($post['location']);
            }
            $user['loc_community'] = $loc['loc_community']['sid'];
            $user['loc_district']  = $loc['loc_district']['sid'];
            $user['loc_city']      = $loc['loc_city']['sid'];
            $user['loc_province']  = $loc['loc_province']['sid'];
            if ($this->user->saveOrUpdate($user)) {
                if ($is_new_user) {
                    Hongbao::createForRegister($this->user);
                }
                return $this->success('',$this->user);
            }else{
                // return $this->error($this->user);
                return $this->error('保存失败',$this->user);
            }
        }
        return $this->error('参数不正确');
    }

    public function postLocation(){
        // $loc = Location::getLocAllByNode(Location::where('parent','=',I('parent'))->where('name','=',I('name'))->first());
        $loc = Location::getLocAllByNode(Location::where('parent','=',I('parent'))->where('sid','=',I('sid'))->first());
        // $loc_new['loc_room']      = $loc['loc_room']['sid'];
        $loc_new['loc_building']  = $loc['loc_building']['sid'];
        $loc_new['loc_community'] = $loc['loc_community']['sid'];
        $loc_new['loc_district']  = $loc['loc_district']['sid'];
        $loc_new['loc_city']      = $loc['loc_city']['sid'];
        $loc_new['loc_province']  = $loc['loc_province']['sid'];
        if ('false'==I('is_address')) {
            if ($this->user->saveOrUpdate($loc_new)) {
                $this->data['user'] = $this->user;
                $this->data['loc'] = $loc;
                return $this->success();
            }else{
                return $this->error('保存失败',$this->user);
            }
        }else{
            $this->data['loc'] = $loc;
            return $this->success();
        }
    }

    // public function getPay(){
    public function getCart(){
        $this->data['wx_addresses'] = Address::getByUser($this->user);
        $this->data['hongbao_num'] = $this->user->hongbao->count();
        return $this->display('',$this->data);
    }

    /**
     * 步骤1：网页授权获取用户openid
     * 步骤2：使用统一支付接口，获取prepay_id
     * 步骤3：使用jsapi调起支付
    */
    // public function postPay(){
    public function postCart(){
        $post = Input::all();
        $post['buyer_id'] = $this->user->id;
        $goods_list = array();
        $error_list = array();
        $error_info = '';
        $now = time();
        $post_goods_amount = $post['goods_amount'];
        $post['goods_amount'] = 0; // 重新计算总额
        $hongbao_amount = 0;
        $cache_sku = [];
        $cache_sku_num = [];
        $goods_num = [];
        foreach ($post['goods'] as $key => $item) {
            $goods = Goods::find($item['id']);
            array_push($goods_list, $goods);
            if (empty($goods)) {
                $error_info = '找不到该商品';//todo
                $error_list[$key] = array('goods' => $item);
            }else{
                if ($goods->start_time > $now) {
                    $error_info = '未到购买时间';
                    $error_list[$key] = array('start_time' => $item);
                }else if ($goods->end_time < $now) {
                    $error_info = '已过购买时间';
                    $error_list[$key] = array('end_time' => $item);
                }else{
                    $sku = Sku::find($item['sku_id']);
                    $cache_sku[] = $sku;
                    $cache_sku_num[] = $item['num'];
                    if (empty($sku) || $item['sku_value']!=unserialize($sku->value) || $item['sku_price']!=$sku->price) {
                        // $error_info = '商品信息已过时';
                        // $error_list[$key] = array('SKU' => $item);
                        $error_info = '商品信息已过时';
                        $error_list[$key] = array('sku' => $sku);
                    }else if ($sku->num < $item['num']) {
                        $error_info = '库存不足';
                        $error_list[$key] = array('num' => $sku->num);
                    }else{
                        // 合法
                        $post['goods_amount'] = $post['goods_amount'] + $sku->price * $item['num'];
                        // 限购判断
                        if (isset($goods_num[$item['id']])) {
                            $goods_num[$item['id']] = $goods_num[$item['id']] + $item['num'];
                        }else{
                            $goods_num[$item['id']] = $item['num'];
                        }
                    }

                    // 限购判断
                    // todo 暂时只支持一次购买
                    // todo order_goods add buyer_id
                    $orders =  Order::where('buyer_id','=',$this->user->id)->get();
                    if (!empty($orders)) {
                        foreach ($orders as $key => $order) {
                            $order_goodses = OrderGoods::where('order_id','=', $order->id)->get();// $order->order_goods();
                            foreach ($order_goodses as $key => $order_goods) {
                                if (($goods->buy_limit > 0)  && ($order_goods->goods_id == $goods->id) && ($order->status > 0)) {
                                    $error_info = '限购商品只能买一次';
                                    $error_list[] = array('buy_limit' => $goods);
                                }
                            }
                        }
                     } 

                }
            }
        }
        // 限购判断
        foreach ($goods_num as $id => $num) {
            if ($goods->buy_limit > 0 && $num > $goods->buy_limit) {
                $error_info = '限购商品只能买一次';
                $error_list[] = array('buy_limit' => $goods);
            }

        }
        if (!count($error_list) && !empty($post['hongbao']['id'])) {
            $hongbao = Hongbao::find($post['hongbao']['id']);
            $res = $hongbao->valid($post['goods_amount']);
            if ((true !== $res) || ($hongbao->user_id != $this->user->id)) {
                $error_list[] = array('hongbao'=>$hongbao);
            }else{
                $hongbao_amount = $hongbao->amount;
            }
        }
        // 更新金额，由后端计算
        $post_discount = $post['discount'];
        $post['discount'] = ($post['goods_amount']>$hongbao_amount) ? $hongbao_amount : $post['goods_amount']; // todo 暂时discount只有红包
        $post['amount'] = $post['goods_amount'] - $post['discount'];
        if ( (0==count($error_list)) && ((abs(floatval($post_goods_amount) - $post['amount']- $post['discount'])>=0.01) || (abs($post_discount -$post['discount'])>=0.01))) {
            $error_info = '订单金额校验错误';
            $error_list[] = array(
                'post_goods_amount' => $post_goods_amount, 
                'post_discount' => $post_discount, 
                'amount' => $post['amount'],
                'goods_amount' => $post['goods_amount']
            );
        }
        if (count($error_list) || !empty($error_info)) {
            return $this->error($error_info, $error_list);
        }
        $order = Order::createOrFalse($post,$goods_list);
        if (empty($order)) {
            return $this->error('创建订单失败');//todo 失败原因
        }
        if (!empty($hongbao)) {
            if (!$hongbao->iused($order)) {
                return $this->error('红包更新错误');
            };
        }
        if (1==$order->pay_type) { // 货到付款立即减库存
            foreach ($cache_sku as $index => $sku) {
                $sku->decrease($cache_sku_num[$index]); //todo 判断
            }
        }
        if (Config::get('app.sms.enable', true)) {
            // todo 发送短信给客户
            // try {
            //     require_once app_path().'/common/yunpian/sms.php';
            //     $sms = new sms;
            //     // $sms->order_notify($order,$cache_sku,$cache_sku_num,13656633843);
            //     $notify = Order::notify($order,$cache_sku,$cache_sku_num,13656633843);
            //     $this->sendEmail($notify,'订单提醒','emails/admin/order/notify');
            // } catch (Exception $e) {
            //     // todo
            // }
            $notify = Order::email_notify($order,$cache_sku,$cache_sku_num);
            $this->sendEmailToAdmin('下单成功','emails/admin/order/notify',array('notify' => $notify));
        }

        $this->data['order'] = $order;
        return $this->success('',$this->data);
    }

    public function getOrder(){
        return $this->success('',Order::where('buyer_id','=',$this->user->id)->get());
    }

    public function getHongbao(){
        // $this->data['hongbao'] = $this->user->hongbao;
        $this->data['hongbao'] = Hongbao::where('user_id','=',$this->user->id)->orderBy('created_at','desc')->limit(10)->get();
        return $this->display('',$this->data);
    }
}