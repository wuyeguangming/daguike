<?php

class Upload extends Eloquent {
    protected $guarded = array('');

    static public function icreate($name,$store_id,$item_id){
        return Upload::create(array(
            'name' =>    $name,
            'store_id' =>$store_id,
            'item_id' => $item_id
        ));
    }
}