<?php
class OrderGoods extends BaseModel {
    protected $guarded = array('');
    // protected $visible = array('id', 'store_id', 'updated_at', 'image','name','price','start_time','end_time','body','skus','sale_map','album_id','album','category');
    public function sku(){
        return $this->belongsTo('Sku');
    }
}