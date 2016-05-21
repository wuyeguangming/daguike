<?php
// require_once app_path().'/common/Wechat/Wechat.php';
// require_once app_path().'/common/Wechat/errCode.php';

class WxDashboardController  extends WxController {
    public function __construct(){
        parent::__construct();
        $this->getUser();
        if (empty($this->user)) {
            return Redirect::to('/')->send();
        }
        $this->__path__ = 'site/wx/dashboard';
    }

    public function getIndex(){
        return $this->display('',$this->data);
    }

    // public function getOrder($last=0){
    //     $this->data['order'] = Order::where('store_id','=',$this->user->store_id)->orderBy('created_at', 'desc')->skip($last)->take(5)->get();//->where('status','>',1)
    //     $this->data['order_goods'] = array();
    //     foreach ($this->data['order'] as $key => $order) {
    //         $this->data['order_goods'][$key] = $order->getOrderGoods();
    //     }
    //     return $this->display('',$this->data);
    // }

    public function getOrderPage($p=1){
        if ($this->user->store_id) {
            $order = new Order;
            $page = $order->iPage(function($query){
                $status = Input::get('status','');
                if (''==$status) {
                    $query->where('store_id','=',$this->user->store_id)->where('status','>',0);
                }else if('delay2'==$status){
                    $query->where('store_id','=',$this->user->store_id)->where('status','=',2)->where('delivery_time','>',date("Y-m-d H:i:s",time()+30*60));
                }else if('delay3'==$status){
                    $query->where('store_id','=',$this->user->store_id)->where('status','=',3)->where('delivery_time','>',date("Y-m-d H:i:s",time()+30*60));
                }else{
                    $query->where('store_id','=',$this->user->store_id)->where('status','=',$status);
                }
            },$p,10,'created_at',true);
            if (0!=count($page)) {
                // todo 用in查询，减少查询次数
                foreach ($page['data'] as $index => $value) {
                    $order = Order::find($value['id']);
                    $page['data'][$index]['order_goods'] = $order->getOrderGoods();
                    $page['data'][$index]['address'] = $order->getAddress();
                    // $page['data'][$index]['location'] = Location::getLocAllByNodeSid($page['data'][$index]['address']['loc_room']);
                    $page['data'][$index]['location'] = Location::getLocAllByNodeSid($page['data'][$index]['address']['loc_building']);
                }
            }
            $this->data['page'] = $page;
            return $this->display('订单管理',$this->data);
        }
    }

    public function getOrderDetail($order_id=''){
        $order = Order::where('store_id','=',$this->user->store_id)->where('id','=',$order_id)->first();
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

    public function postOrderDetail(){
        $post = Input::all();
        $order = Order::where('store_id','=',$this->user->store_id)->where('id','=',$post['id'])->first();
        if (empty($order)) {
            return $this->error('无法找到该订单');
        }
        $order->status = $post['status'];
        $res = $order->save();
        $this->data['order'] = $order;
        if ($res ) {
            return $this->success('',$this->data);
        }else{
            return $this->error('更新失败');
        }
    }
}