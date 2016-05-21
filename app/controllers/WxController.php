<?php
require_once app_path().'/common/Wechat/Wechat.php';
require_once app_path().'/common/Wechat/errCode.php';
require_once app_path().'/common/baidu/hm.php';

class WxController  extends BaseController {
    public $wx;
    public $wx_options;
    public $data;
    public $user;
    public function __construct(){
        parent::__construct();
        $this->__path__ = 'site/wx/index';
        session_start();
        $this->wx_options = Config::get('app.wx');
        $this->wx = new Wechat($this->wx_options);
        if ('localhost'!=$_SERVER['HTTP_HOST'] && (!Config::get('app.debug') || isWeixin()) && !isset($_SESSION['wx_openid'])) {
            $code = isset($_GET['code'])?$_GET['code']:'';
            if (empty($code)) {
                // 第一次获取跳转路径
                // $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $url = Input::get('_referer', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);//注意区分$this->data['wx']['url']
                if (!$url) {
                    die('获取用户授权失败');//todo
                }
                $oauth_url = $this->wx->getOauthRedirect($url,"wx_auth_redirect",'snsapi_base');//state:wx_auth_redirect随意
                return Redirect::to($oauth_url)->send();
            }else{
                // 同意授权后带有code
                $json = $this->wx->getOauthAccessToken();
                if (!$json) {
                    die('获取用户授权失败，请重新确认');//todo
                }
                $_SESSION['wx_openid'] = $json["openid"];
                $_SESSION['wx_auth'] = $this->wx->checkAuth(); //获取access_token
            }
        }
        $this->data = array();
        $this->data['wx'] = array();
        $this->data['wx']['auth']       = isset($_SESSION['wx_auth'])?$_SESSION['wx_auth'] : '';
        $this->data['wx']['openid']    = isset($_SESSION['wx_openid'])?$_SESSION['wx_openid'] : '';
        $this->data['wx']['url']        = Input::get('_referer', 'http://'.$_SERVER['HTTP_HOST'].'/#'.$_SERVER['REQUEST_URI']);//注意'/#/'!!!
        $this->data['user'] = $this->user;
        if (Config::get('app.debug')) {
            $this->data['wx_session'] = $_SESSION;
        }
        $_hmt = new _HMT( Config::get('app.baidu._hm'));
        $this->data['_hm'] = $_hmt->trackPageView();
    }

    public function js_auth(){
        $this->data['wx']['js_debug']    = Config::get('app.wx.js_debug', false);
        $this->data['wx']['js_sign']    = isWeixin() ? $this->wx->getJsSign($this->data['wx']['url']) : '';//获取JsApi使用签名
        $this->data['wx']['js_ticket']  = isWeixin() ? $this->wx->getJsTicket() : '';//获取JSAPI授权TICKET
        if (isWeixin() && !$this->data['wx']['js_ticket']) {
            echo "获取js_ticket失败！<br>";
            echo '错误码：'.$this->wx->errCode;
            echo ' 错误原因：'.ErrCode::getErrText($this->wx->errCode);
            exit;
        }
    }

    public function getUser(){
        $this->user = User::where('wx_openid','=',$this->data['wx']['openid'])->first();
        $this->data['user'] = $this->user;
    }
}
