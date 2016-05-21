<?php

class Sku extends BaseModel {
    protected $guarded = array('');
    
    //todo: 确定范围
    public static $rules = array(
        'price'  => 'required|numeric|min:0|max:100000000', //注意，必须numeric|min配套使用！！
        'volume' => 'required|numeric|min:0|max:3',//todo 
        'num'    => 'required|numeric|min:0|max:100000000',
        'serial' => 'sometimes|between:0,100',
        // 'value'  => 'required',
        // 'name' => 'required'
    );

    protected $_auto = array(
        array('price','floatval',3,'function') ,
        array('num','intval',3,'function') ,
        array('volume','intval',3,'function') ,
        array('serial','trim',3,'function') ,
        array('value','serialize',3,'function') ,
        // 'name' => '',
    );
    
    static public function findByGoodsId($goods_id){
        $skus = Sku::where('goods_id','=',$goods_id)->orderBy('num','desc')->get();
        $output = array();
        foreach ($skus as $index => $sku) {
            $output[$index]           = array();
            $output[$index]['id']     = $sku->id;
            $output[$index]['value']  = empty($sku->value) ? '' : unserialize($sku->value);
            $output[$index]['serial'] = $sku->serial;
            $output[$index]['price']  = floatval($sku->price);
            $output[$index]['volume'] = $sku->volume;
            $output[$index]['num']    = $sku->num;
            $output[$index]['name']   = $sku->name;
        }
        return $output;
    }

    public function decrease($num=0){
        $this->num = $this->num - $num;
        if ($this->num < 0) {
            $this->num = 0;
        }
        return $this->save(); //todo 判断
    }
}