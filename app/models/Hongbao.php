<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Hongbao extends BaseModel {
    use SoftDeletingTrait;
    protected $dates   = ['deleted_at'];
    protected $softDelete = true; 
    protected $guarded = array('');
    const NAME_SHUA = '刷红包';
    const REFRESH_LIMIT = 5;

    static public function addDay($day){
        $now = date('Y-m-d');
        return date('Y-m-d',strtotime('+'.$day.' day',strtotime($now)));
    }

    static public function createForRegister($user){
        $amounts = [5,5,5,5];
        $conditions = [15,15,15,15];
        $error = true;
        for ($i=0; $i < count($amounts); $i++) { 
            $hongbao =  new Hongbao;
            $hongbao->name = '新用户专享红包';
            $hongbao->time_start = self::addDay(2*$i); //$i
            $hongbao->time_end = self::addDay(2*$i+7);
            $hongbao->user_id = $user->id;
            $hongbao->amount = $amounts[$i];
            // $hongbao->condition = ($hongbao->amount>=4) ? 10 : 0;
            $hongbao->condition = $conditions[$i];
            $error = $error & $hongbao->save();            
        }
        return $error;
    }

    public function valid($goods_amount){
        $now = time();
        if ((time($this->time_start)>$now || $now>time($this->time_end))){
            return '该红包不在有效期内';
        }
        if ($this->used){
            return '该红包已使用过';
        }
        if ($this->condition > $goods_amount) {
            return '该红包需满'.$this->condition.'元才能使用';
        }
        return true;
    }

    public function iused($order){
        $this->order_id = $order->id;
        $this->used = 1;
        return $this->save();
    }

    public function refund(){
        unset($this->order_id);
        $this->used = 0;
        return $this->save();
    }
}