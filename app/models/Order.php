<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Order extends BaseModel {
    use SoftDeletingTrait;
    protected $dates   = ['deleted_at'];
    protected $softDelete = true; 
    protected $guarded = array('');

    public function hongbao(){
        return $this->hasOne('Hongbao');
    }

    public function order_goods(){
        return $this->hasMany('OrderGoods');
    }

    static public function newSnOut($buyer_id){
        return md5(time().sprintf("%010d", $buyer_id));
    }

    public function getAddress(){
        return Address::find($this->address_id);
    }

    public function getOrderGoods(){
        return OrderGoods::where('order_id','=',$this->id)->get();
    }

    static public function createOrFalse($post,$goods_list){
        $order = array();
        $order['sn'] = time().sprintf("%010d", $post['buyer_id']);//临时sn
        $order['sn_out'] = Order::newSnOut($post['buyer_id']);//临时外部sn todo 唯一性？
        $order['amount'] = ($post['amount']<0) ? 0 : $post['amount'];
        $order['goods_amount'] = $post['goods_amount'];
        $order['discount'] = $post['discount'];
        $order['address_id'] = $post['address_id'];
        $order['buyer_id'] = $post['buyer_id'];
        

        // TODO important!!!! 暂时自营！！
        $order['store_id'] = 1;
        // TODO important!!!! 暂时自营！！


        $order['pay_type'] = intval($post['pay_type']);
        // 货到付款-下单成功
        if (1 == $order['pay_type']) {
            $order['status'] = 2;
        }else if (0 == $order['pay_type']) {
            $order['status'] = 1;
        }
        $order['delivery_time'] = date('Y-m-d H:i:s',(intval($post['delivery_time']) ? (intval($post['delivery_time']/1000)) : time()));
        $order['ifrom'] = isWeixin()?0:1;
        $order = Order::create($order);
        if (!empty($order)) {
            $order->sn = sprintf("%010d", time()).sprintf("%04d", $order->id);
            if ($order->update()) {
                foreach ($goods_list as $index => $goods) {
                    OrderGoods::create(array(
                        'order_id' => $order->id,
                        'num' => $post['goods'][$index]['num'], 
                        'sku_id' => $post['goods'][$index]['sku_id'],
                        'sku_value' => $post['goods'][$index]['sku_value'],
                        'sku_price' => $post['goods'][$index]['sku_price'],
                        // 'store_id' => $goods->store_id,
                        'sku_serial' => $post['goods'][$index]['sku_serial'],
                        'goods_id' => $goods->id,
                        'goods_name' => $goods->name,
                        'goods_image' => $goods->image,
                    ));
                }
                return $order;
            }else{
                // return $this->error($order); TODO 从新创建
            }
        }
        return false;
    }

    static public function pageByStoreId($store_id,$page){
        return self::iPage(self::where('store_id','=',$store_id),$page);
    }

    public function cancel(){
        $this->status = (0==$this->pay_type) ? 6 : 0;
        $hongbao = $this->hongbao;
        if (!empty($hongbao)) {
            $hongbao->refund(); //todo:: return code
        }
        $order_goodses = OrderGoods::where('order_id','=', $this->id)->get();
        foreach ($order_goodses as $key => $order_goods) {
            $sku = Sku::find($order_goods->sku_id);
            if (!empty($sku)) {
                $sku->num = $sku->num + $order_goods->num;
                $sku->update();//todo:: return code
            }
        }
        return $this->save();
    }

    static public function email_notify($order,$skus,$sku_num,$completed=false){
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
        // todo 发送短信给客户
        return '配送时间'.$delivery_time.'，金额'.$amount.'，地址'.$address->name.' '.$address->phone.' '.$loc_community->name.$loc_building->name.$address->loc_detail.'，商品规格'.$sms_sku_str.'，单号'.$order->sn;
    }
}