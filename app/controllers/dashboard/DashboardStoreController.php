<?php
use Rhumsaa\Uuid\Uuid;
require_once app_path().'/common/Wechat/WxPayPubHelper/WxPayPubHelper.php';
class DashboardStoreController  extends AuthorizedController {
    /**
     * User Model
     * @var User
     */
    protected $user;

    /**
     * Inject the models.
     * @param User $user
     */
    public function __construct(){
        parent::__construct();
        $this->user = Auth::user(); //区别于$user：后者无登录用户信息，是一个初始化model
        $this->data = array();
        if (!empty($this->user)) {
            $this->data['user'] = $this->user->info();
        }
        $this->__path__ = 'site/dashboard/store';
    }

    public function isOwner($store_id){
        return ($store_id == $this->user->store_id);
    }

    public function getIndex(){
        return $this->display('主页', $this->data);
    }

    public function getCreate(){
        return $this->display('免费开店',$this->data);
    }

    public function postCreate(){
        $post = Input::all();
        if (Store::where('user_id','=',$this->user->id)->first()) {
            return $this->error('抱歉，您只能创建一个店铺！');
        }
        if (Store::where('name','=',$post['name'])->first()) {
            return $this->error('该店铺名已被注册！');
        }
        return $this->result(Store::create(array(
            'name'          => $post['name'],
            'user_id'       => $this->user->id,
            'username'      => $this->user->username,
            'loc_province'  => $this->user->loc_province,
            'loc_city'      => $this->user->loc_city,
            'loc_district'  => $this->user->loc_district,
            'loc_community' => $this->user->loc_community,
        )));
    }

    public function postOrderSearch(){
        if ($this->user->store) {
            $order = new Order;
            $p = I('p',0);
            $page = $order->iPage(function($query){
                $post = I();
                $where = array();
                $query->where('store_id','=',$this->user->store->id);
                $status = Input::get('status','');
                if (''==$status) {
                    $query->where('status','>',0);
                }else if('bill'==$status) {
                    $query->where('status','>',1);
                }else if('delay2'==$status){
                    $query->where('status','=',2)->where('delivery_time','>',date("Y-m-d H:i:s",time()+30*60));
                }else if('delay3'==$status){
                    $query->where('status','=',3)->where('delivery_time','>',date("Y-m-d H:i:s",time()+30*60));
                }else{
                    $query->where('status','=',$status);
                }
                if (!empty($post['sn'])) {
                    $where['sn'] = $post['sn'];
                    $query->where('sn',$post['sn']);
                }
                if(!empty($post['username']) || !empty($post['phone'])){
                    $_where = array();
                    if(!empty($post['phone'])){
                        $_where['phone'] = $post['phone'];
                    }
                    $addresses = Address::where($_where)->where('name','like','%'.$post['username'].'%')->get();
                    if (empty($addresses)) {
                        return $query->whereIn('address_id',[-1]);//todo..
                    }
                    $address_ids = [];
                    foreach ($addresses as $key => $address) {
                        $address_ids[] = $address->id;
                        $where['address_id'] = $address_ids;
                    }
                    if (!empty($where['address_id'])) {
                        $query->whereIn('address_id',$where['address_id']);
                    }else{
                        $query->whereIn('address_id',[-1]);//todo..
                    }
                }
                // todo: not in !xxx
                if(!empty($post['goodsname'])){
                    $goods_ids = array();
                    $goods = Goods::where('name','=',$post['goodsname'])->get();
                    foreach ($goods as $index => $good) {
                        $goods_ids[] = $good->id;
                    }
                    if (empty($goods_ids)) {
                        return $query->whereIn('id',[-1]);//todo..
                    }
                    $order_goods = OrderGoods::whereIn('goods_id',$goods_ids)->get();
                    $where['id'] = array();
                    foreach ($order_goods as $key => $order_good) {
                        $where['id'][] = $order_good->order_id;
                    }
                    if (!empty($where['id'])) {
                        $query->whereIn('id',$where['id']);
                    }else{
                        $query->whereIn('id',[-1]);//todo..
                    }
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

    public function getOrderPage($p=0){
        if ($this->user->store) {
            $order = new Order;
            $status = Input::get('status','');
            $page = $order->iPage(function($query) use ($status){
                if (''==$status) {
                    $query->where('store_id','=',$this->user->store->id)->where('status','>',0);
                }else if('delay2'==$status){
                    $query->where('store_id','=',$this->user->store->id)->where('status','=',2)->where('delivery_time','>',date("Y-m-d H:i:s",time()+30*60));
                }else if('delay3'==$status){
                    $query->where('store_id','=',$this->user->store->id)->where('status','=',3)->where('delivery_time','>',date("Y-m-d H:i:s",time()+30*60));
                }else{
                    $query->where('store_id','=',$this->user->store->id)->where('status','=',$status);
                }
            } ,$p,10,'created_at',true);
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


    public function getBillPage($p=1){
        if ($this->user->store) {
            $order = new Order;
            $page = $order->iPage(function($query){
                $query->where('store_id','=',$this->user->store->id)->where('status','>',1);
            } ,$p,10,'created_at',true);
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
            return $this->display('账单管理',$this->data);
        }
    }

    public function postOrder(){
        $post = I();
        $order = Order::where('id','=',$post['id'])->first();
        if (empty($order)) {
            return $this->error('无法找到该订单');
        }
        if ($this->isOwner($order->store_id)) {
            $order->seller_note = I('seller_note');
            return $this->result($order->save());
        }else{
            return $this->error('您无此权限');
        }
    }

    public function postOrderPage(){
        $orders = Input::get('orders');
        $error = false;
        if (count($orders)) {
            foreach ($orders as $key => $order) {
                $tmp = Order::find($order['id']);
                $tmp->status = $order['status'];
                if (0==$tmp->status) { //取消订单
                    $tmp->cancel();
                }else{
                    $error = $error | !$tmp->save();
                }
            }
        }else{
            $error = true;
        }
        return $this->result(!$error);
    }

    public function getAlbum(){
        if (!$this->user->store) {
            return $this->error('您尚未创建店铺！');
        }
        $albums = array();
        if ($this->user->store) {
            $albums = $this->user->store->albums();
        }
        $this->data['albums'] = $albums;
        return $this->display('专辑管理',$this->data);
    }

    public function postAlbum(){
        $new_id_map = array();
        $albums = Input::get('albums');
        if (!$albums) {
            return $this->error('专辑未发生改变！');
        }
        if (isset($albums['news'])) {
            // 先插入节点
            foreach ($albums['news'] as $index => $album) {
                $buf = Album::iCreate($album);
                if(!$buf){
                    return $this->error('保存失败'); # todo:详细处理
                }
                $new_id_map[$album['id']] = $buf->id;
                $albums['news'][$index]['id'] = $buf->id;
            }
            // 后确定关系
            foreach ($albums['news'] as $index => $album) {
                $res = Album::find($album['id']);
                if (isset($new_id_map[$album['parent_id']])) {
                    $album['parent_id'] = $new_id_map[$album['parent_id']];
                    $albums['news'][$index]['parent_id'] = $album['parent_id'];
                }
                if( !($res && $res->iUpdate($album))){
                    return $this->error('保存失败'); # todo:详细处理
                }
            }
        }
        if (isset($albums['changes'])) {
            foreach ($albums['changes'] as $index => $album) {
                $res = Album::find($album['id']);
                if (isset($new_id_map[$album['parent_id']])) {
                    $albums['changes'][$index]['parent_id'] = $new_id_map[$album['parent_id']];
                }
                if( !($res && $res->iUpdate($album))){
                    return $this->error('保存失败'); # todo:详细处理
                }
            }
        }
        if (isset($albums['deletes'])) {
            foreach ($albums['deletes'] as $index => $id) {
                $res = Album::find($id);
                $res && $res->delete(); #若找不到该项，不报错
            }
        }
        # 重新生成新数据，用于更新前端，防止重复提交
        return $this->success('',$this->user->store->albums()); 
    }

    public function getManage($page=0){
         // = Goods::pageByStoreId($this->user->store->id,$page);
        $goods = new Goods;
        $this->data['page'] = $goods->iPage(function($query){
            $query->where('store_id','=',$this->user->store->id);
        },$page);
        return $this->display('商品管理',$this->data);
    }

    public function postManageDel(){
        $goods_id = intval(I('goods_id'));
        $page     = intval(I('page'));
        if ($goods_id) { 
            $goods = Goods::find($goods_id);
            if ($page >= 0 && !empty($goods) && $goods->store->id == $this->user->store->id && $goods->delete()) {//检查权限!!!!
                $goods = new Goods;
                $this->data['page'] = $goods->iPage(function($query){
                    $query->where('store_id','=',$this->user->store->id);
                },$page);
                return $this->success('删除成功！',$this->data);
            }
        }
        return $this->error('该商品不存在或已被删除！');
    }

    public function getRefundPage($p=0){
        if ($this->user->store) {
            $order = new Order;
            $page = $order->iPage(function($query){
                $query->where('store_id','=',$this->user->store->id)->where('status','=',6);
            },$p);
            $this->data['page'] = $page;
            return $this->display('订单管理',$this->data);
        }
    }

    public function postRefund(){
        $order = Order::find(I('id'));
        if (($this->user->isAdmin())&&(6==$order->status)) {
            if (I('agree')) {
                $out_trade_no = $order->sn_out;
                $refund_fee = intval(Input::get("refund_fee", $order->amount)*100);
                //商户退款单号，商户自定义，此处仅作举例
                $out_refund_no = md5($out_trade_no);
                //总金额需与订单号out_trade_no对应，demo中的所有订单的总金额为1分
                $total_fee = intval($order->amount*100);
                
                //使用退款接口
                $refund = new Refund_pub();
                //设置必填参数
                //appid已填,商户无需重复填写
                //mch_id已填,商户无需重复填写
                //noncestr已填,商户无需重复填写
                //sign已填,商户无需重复填写
                $refund->setParameter("out_trade_no","$out_trade_no");//商户订单号
                $refund->setParameter("out_refund_no","$out_refund_no");//商户退款单号
                $refund->setParameter("total_fee","$total_fee");//总金额
                $refund->setParameter("refund_fee","$refund_fee");//退款金额
                $refund->setParameter("op_user_id",Config::get('app.wx.mchid'));//操作员WxPayConf_pub::MCHID
                //非必填参数，商户可根据实际情况选填
                //$refund->setParameter("sub_mch_id","XXXX");//子商户号 
                //$refund->setParameter("device_info","XXXX");//设备号 
                //$refund->setParameter("transaction_id","XXXX");//微信订单号
                
                //调用结果
                $refundResult = $refund->getResult();
                
                //商户根据实际情况设置相应的处理流程,此处仅作举例
                if ($refundResult["return_code"] == "FAIL") {
                    echo "通信出错：".$refundResult['return_msg']."<br>";
                    return $this->error();
                }
                // else{
                //     echo "业务结果：".$refundResult['result_code']."<br>";
                //     echo "错误代码：".$refundResult['err_code']."<br>";
                //     echo "错误代码描述：".$refundResult['err_code_des']."<br>";
                //     echo "公众账号ID：".$refundResult['appid']."<br>";
                //     echo "商户号：".$refundResult['mch_id']."<br>";
                //     echo "子商户号：".$refundResult['sub_mch_id']."<br>";
                //     echo "设备号：".$refundResult['device_info']."<br>";
                //     echo "签名：".$refundResult['sign']."<br>";
                //     echo "微信订单号：".$refundResult['transaction_id']."<br>";
                //     echo "商户订单号：".$refundResult['out_trade_no']."<br>";
                //     echo "商户退款单号：".$refundResult['out_refund_no']."<br>";
                //     echo "微信退款单号：".$refundResult['refund_idrefund_id']."<br>";
                //     echo "退款渠道：".$refundResult['refund_channel']."<br>";
                //     echo "退款金额：".$refundResult['refund_fee']."<br>";
                //     echo "现金券退款金额：".$refundResult['coupon_refund_fee']."<br>";
                // }
                $order->status = 7;
            }else{
                $order->status = 8;
            }
            $res = $order->save();
            if ($res && !empty($order->hongbao_id)) {
                $order->hongbao->used = 0;
                unset($order->hongbao->order_id);
                $res = $res && $order->hongbao->save();
            }
            return $this->result($res);
        }
        return $this->error();
    }

    public function getPublish($goods_id=''){
        $user = $this->user;
        $this->data['categories'] = Category::where('store_id','=','1')->get()->toArray();
        $this->data['albums'] = $user->store->albums();
        if ('' != $goods_id) {
            $goods = Goods::find($goods_id);
            $this->data['goods'] = empty($goods) ? '' : $goods->output();
        }
        $this->data['locs'] = Location::iall(Location::LEVEL_ROOM);
        return $this->display('发布商品',$this->data);
    }

    public function postPublish(){
        $post = json_decode(I('data'));
        $store_id  = $this->user->store_id;
        if (empty($store_id)) {
            $this->error('请先开通店铺！');
        }
        $taobao = new \common\taobao\api;
        $file   = $_FILES;
        $images = array();
        $image_names = array();
        $csv_string = '';
        foreach ($_FILES as $key => $file) {
            $file = Input::file($key);
            // 文件是否有效
            if(!$file->isValid()){
                return $this->error('文件无效！');
            }
            // 文件大小判定
            if($file->getSize() > intval(ini_get('upload_max_filesize'))*1024*1024){
                return $this->error('文件大小必须为'.ini_get('upload_max_filesize').'以内');
            }
            // 文件类型判定
            switch ($file->getClientOriginalExtension()) {
                case 'csv':
                    //上传文件的字符编码转换
                    $csv_string = $taobao->unicodeToUtf8(file_get_contents($file->getRealPath()));
                    // 兼容淘宝助理5 ???
                    // $csv_array = explode("\tsyncStatus", $csv_string, 2);
                    // if(count($csv_array) == 2){  $csv_string = $csv_array[1];}

                    $first = stripos($csv_string, "\n");
                    if($first < 20){  
                        $twice = stripos($csv_string, "\n", $first+1);
                        $csv_string = substr($csv_string,$twice+1);
                    }
                    break;
                // case 'tbi':
                //     $images[] = $file;
                //     $image_names[] = $file->getClientOriginalName();
                //     break;
                default: 
                    // todo: 其他文件类型操作
                    // return $this->error('请上传正确的tbi文件');
                    $images[] = $file;
                    $image_names[] = $file->getClientOriginalName();
                    break;
            }
        }
        // 商品分类判定
        $category = $post->category; #todo: upload bug
        if(empty($category)){
            return $this->error('请选择商品分类');
        }
        $category = Category::find($category->id);
        if(empty($category) ){ // !is_array($category) or count($category) == 0
            return $this->error('该商品分类不可用，请重新选择');
        }
        // 专辑判定
        $album = $post->album;
        if (empty($album)) {
            return $this->error('请选择专辑');
        }else if(!Album::find($album->id)) {
            return $this->error('该专辑不可用，请重新选择');
        }
        // 销售区域
        $sale_map = Location::sale_map2str($post->sale_map);
        // 上下线时间转换
        $start_time = strtotime($post->start_time); 
        $end_time = strtotime($post->end_time);
        $now_time = time();
        if ($start_time < $now_time) {
            $start_time = $now_time;
        }
        if ($end_time < $start_time) {
            $end_time = $start_time;
        }
        if (!$post->publish_type) {
            // $images
            $param                   = array();
            $param['category_id'] = $post->category->id;
            $param['album_id']       = $post->album->id;
            $param['store_id']       = $this->user->store->id;
            if (!$post->id) {
                $goods  = Goods::create($param);
            }else{
                $goods  = Goods::find(intval($post->id));

            }
            foreach ($images as $index => $image) {
                $name = Uuid::uuid4()->toString().'.'.$image->getClientOriginalExtension();
                Upload::icreate($name, $goods->store_id, $goods->id);
                $image->move(public_path('img'),$name);
                $post->body = str_replace($image->getClientOriginalName(), $name, $post->body);
            }
            // skus
            $price = 100000000;
            $num = 0;
            foreach ($post->skus as $key => $post_sku) {
                if (empty($post_sku->id)) {
                    $post_sku->goods_id = $goods->id;
                    $sku = new Sku;
                }else{
                    $sku = Sku::find($post_sku->id);
                }
                if (!$sku->iSave($post_sku)) {
                    if (!$post->id){ $goods->forceDelete();} // 删除临时变量 todo: 删除upload？
                    return $this->error($sku->errors());// todo: 详细说明错误原因
                }
                $num += empty($post_sku->num)? 0 : intval($post_sku->num);
                $_price = empty($post_sku->price)? 0 : floatval($post_sku->price);
                if ($price > $_price) { //价格取低者
                    $price = $_price;
                }
            }
            // 非必须数据统一放在最后，新建与更新统一
            $param['name'] = $post->name;
            $param['subtitle'] = $post->subtitle;
            $param['price'] = $price;
            $param['num']   = $num;
            $param['body']  = $post->body;
            $param['image'] = explode(';',$post->body)[0]; //首图
            $param['start_time'] = $start_time;
            $param['end_time'] = $end_time;
            $param['sale_map'] = $sale_map;
            $param['buy_limit'] = $post->buy_limit;
            $param['is_show'] = $post->is_show;
            $goods->update($param);
            return $this->success($goods);
        }else{
            if (''==$csv_string) {
                return $this->error('请上传正确的csv文件');
            }
            // 将文件转换为二维数组形式的商品数据
            $records    = $taobao->csv_parse($csv_string);
            if($records === false){
                return $this->error('文件内字段与系统要求的字段不符,请详细阅读导入说明');
            }
            // 商品数判断,(空间使用，使用期限判断)(todo:从配置中读取)
            $GOODS_MAXNUM = 100;
            $GOODS_INDTAE = 30;
            $goods_num = Goods::countByStoreId($store_id);
            if ($goods_num > $GOODS_MAXNUM && !Store::is_daguike($store_id)) {
                return $this->error('您已经达到了添加商品的上限:'.$GOODS_MAXNUM.'个！');
            }
            $remain_num = $GOODS_MAXNUM - $goods_num;
            // 循环添加数据
            if(is_array($records) and count($records) > 0){
                foreach($records as $k=>$record){
                    if($remain_num>0 and $k>=$remain_num){
                        return $this->error('您已经达到了添加商品的上限:'.$GOODS_MAXNUM.'个！');
                    }
                    $pic_array  = $taobao->get_goods_image($record['image']);
                    if(empty($record['name']))continue;
                    $param                   = array();
                    $param['name']           = $record['name'];
                    $param['category_id'] = $category['id'];
                    $param['store_id']       = $store_id;
                    $param['image']          = $pic_array['image'][0];
                    $param['is_show']        = $record['is_show'];
                    $param['recommend']      = $record['recommend'];
                    $param['oldlevel']       = $record['oldlevel'];//todo
                    $param['express_fee']    = $record['express_fee'];
                    $param['price']          = $record['price'];
                    $param['num']            = $record['num'];
                    $param['start_time']     = $start_time;
                    $param['end_time']       = $end_time;
                    $param['body']           = $record['body']; //TODO：数据过滤，只留图片
                    //$param['sale_map']       = $sale_map; //todo: sale_map

                    // $param['category_name']     = $category['name'];
                    // $param['py_price']    = $record['py_price'];
                    // $param['es_price']    = $record['es_price'];
                    // $param['kd_price']    = $record['kd_price'];
                    // $param['city_id']     = intval($_POST['city_id']);
                    // $param['province_id'] = intval($_POST['province_id']);
                        
                    //修改商品
                    $goods  = Goods::create($param);
                    if($goods){
                        //添加规格表
                        // foreach ($post->skus as $key => $sku) {
                        //     $sku             = array();
                        //     $sku['goods_id'] = $goods->id;
                        //     $sku['name']     = empty($sku->name)?'':$sku->name;
                        //     $sku['price']    = $sku->price;
                        //     $sku['num']      = $sku->num;
                        //     $sku['volume']    = $sku->volume;
                        //     $sku['serial']   = $sku->serial;
                        //     $sku['value']    = $sku->value;
                        //     $sku['salenum']  = 0;
                        //     $sku_id          = Sku::createByGoodsId($goods->id, $sku);
                        //     $res              = $goods->update(array('sku_id'=>$sku_id));
                        //     if (!$res) {
                        //         return $this->error('添加规格表出错！');
                        //     } 
                        // }

                        //商品首图的添加
                        $index = array_search($pic_array['image'][0].'.tbi',$image_names);
                        if(null===$index)continue;
                        $name = Uuid::uuid4()->toString().'.jpg';
                        Upload::icreate($name, $store_id, $goods->id);
                        $images[$index]->move(public_path('img'),$name);

                        // 更新首图url
                        $goods->update(array('image'=>$name));
                    }else{
                        // 导入商品中的错误处理 todo
                        return $this->error('发布失败！');
                    }
                }
            }
        }
        return $this->success();
    }


    public function getLocation($p=''){
        $this->data['location'] = Location::where('level','<','6')->get();
        return $this->display('',$this->data);
    }
}
