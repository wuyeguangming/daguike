<?php
require_once app_path().'/common/Wechat/Wechat.php';
require_once app_path().'/common/Wechat/errCode.php';

class WxGamesController  extends WxController {
    public function __construct(){
        parent::__construct();
        $this->__path__ = 'site/wx/games';
        $this->getUser();
        if (empty($this->user)) {
            return $this->error('请先登录');
        }
    }

    public function getHongbao(){
        $today = date('Y-m-d');
        $this->data['hongbao'] = Hongbao::where('user_id','=',$this->user->id)
            ->where('name','=',Hongbao::NAME_SHUA)
            ->where('created_at','>',$today)
            ->where('created_at','<',date('Y-m-d',strtotime('+1 day',strtotime($today))))
            ->first();
        return $this->display('刷红包',$this->data);
    }

    public function postHongbao(){
        $today = date('Y-m-d');
        $hongbao = Hongbao::where('user_id','=',$this->user->id)
            ->where('name','=',Hongbao::NAME_SHUA)
            ->where('created_at','>',$today)
            ->where('created_at','<',date('Y-m-d',strtotime('+1 day',strtotime($today))))
            ->first();
        if (!count($hongbao)) {
            $hongbao = new Hongbao;
            $hongbao->time_start = Hongbao::addDay(0);
            $hongbao->time_end = Hongbao::addDay(1);
        }else if($hongbao->refresh_cnt >= Hongbao::REFRESH_LIMIT){
            return $this->error('次数已达上限');
        }
        $hongbao->user_id = $this->user->id;
        $hongbao->name = Hongbao::NAME_SHUA;
        $hongbao->condition = rand(1500,2000);
        $hongbao->amount = rand($hongbao->condition*0.01,$hongbao->condition*0.20);
        $hongbao->condition = $hongbao->condition/100;
        $hongbao->amount = $hongbao->amount/100;
        $hongbao->refresh_cnt = $hongbao->refresh_cnt + 1;
        $hongbao->save();
        $this->data['hongbao'] = $hongbao;
        return $this->success($this->data);
    }
}