<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Goods extends BaseModel {
    use SoftDeletingTrait;
    protected $dates   = ['deleted_at'];
    protected $softDelete = true; 
    protected $guarded = array('');
    protected $visible = array('id', 'click', 'store_id', 'updated_at', 'image','name','subtitle','price','start_time','end_time','body','skus','sale_map','album_id','album','category','num','buy_limit','is_show');
    
    public function store(){
        return $this->belongsTo('Store');
    }

    public function album(){
        return $this->belongsTo('Album');
    }

    public function skus(){
        return $this->hasMany('Sku');
    }

    public function category(){
        return $this->belongsTo('Category');
    }

    // todo: 过滤敏感项
    public function output(){
        $this->album       = $this->album()->first()->toArray();
        $this->category = $this->category()->first()->toArray();
        // $this->sku        = $this->sku;// unserialize($this->sku);
        // $this->sku_name   = $this->sku_name;// unserialize($this->sku_name);
        $this->skus = Sku::findByGoodsId($this->id);
        return $this->toArray();
    }

    static public function countByStoreId($store_id=''){
        return parent::where('store_id','=',$store_id)->count();
    }

    static public function pageByStoreId($store_id,$page){
        return self::iPage(self::where('store_id','=',$store_id),$page);
    }
}