<?php
require_once app_path().'/common/Wechat/Wechat.php';
require_once app_path().'/common/Wechat/errCode.php';

class WxUserController  extends WxController {
    public function __construct(){
        parent::__construct();
        $this->__path__ = 'site/wx/user';
    }

    //直接用浏览器访问，如 m.daguike.cn/wx/user/menu-create
    public function getMenuCreate(){
        //设置菜单，注意 url必须带协议开头，如http://
        $newmenu =  Config::get('app.wx.menus');
        if ($this->wx->createMenu($newmenu)) {
            ss($this->wx->getMenu());
        }else{
            ss($this->wx->errMsg);
        }
    }
}