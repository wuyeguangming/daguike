<?php

class Store extends Eloquent {
    protected $fillable = array('name','user_id','username','loc_province','loc_city','loc_district','loc_community');
   
    // 判断是否是主营
    static public function is_daguike($id){
        return (intval($id) == 1);
    } 
    
    // 返回部分信息
    public function info(){
        if (is_null($this->id)) {
            return null;
        }
        return array(
            'id' => $this->id,
            'name' => $this->name
        );
    }

    // 获取店铺商品专辑
    public function albums(){
        return Album::where('store_id','=',$this->id)->get()->toArray();
    }
}