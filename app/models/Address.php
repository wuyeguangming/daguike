<?php

class Address extends BaseModel {
    protected $guarded = array('');
    // protected $visible = array('id','loc_room','name','note','phone','user_id');
    protected $visible = array('id','loc_building','name','note','phone','user_id','loc_detail');

    public function user(){
        return $this->belongsTo('User');
    }
    
    static public function getByUser($user){
        $addresses = Address::where('user_id','=',$user->id)->orderBy('updated_at','dsc')->get();
        $res = array();
        if (!count($addresses)) {
            return $addresses;
        }
        foreach ($addresses as $index => $address) {
            $res[$index]['address'] = $address;
            // $res[$index]['location'] = Location::getLocAllByNodeSid($address->loc_room);
            $res[$index]['location'] = Location::getLocAllByNodeSid($address->loc_building);
        }
        return $res;
    }
}
