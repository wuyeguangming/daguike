<?php
require_once app_path().'/common/Wechat/Wechat.php';
require_once app_path().'/common/Wechat/errCode.php';

class WxApiController  extends Controller {
    private $wx_options;
    public function __construct(){
        $this->wx_options = Config::get('app.wx');
        $this->wx = new Wechat($this->wx_options);
    }

    // 本地测试 http://localhost/wx/valid?signature=bb9c43b1ced8980ecf4ed2fb0432ca1da7ccbb56&echostr=2097117075455520763&timestamp=1422534961&nonce=1214213220
    public function getIndex(){
        $this->wx->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $this->wx->getRev()->getRevType();
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $this->wx->transfer_customer_service(Config::get('app.wx.kf', '00@idaguike'))->reply();
                exit;
                break;
            case Wechat::MSGTYPE_EVENT:
                $event = $this->wx->getRev()->getRevEvent();
                if ($event['event'] == Wechat::EVENT_SUBSCRIBE) {
                    $this->wx->text(Config::get('app.wx.reply.subscribe', '感谢关注大贵客，祝您购物愉快！'))->reply();
                }
                if ($event['event'] == Wechat::EVENT_MENU_VIEW) {
                    // 点击一次就记录一次，作为登录次数
                    $openid = $this->wx->getRevFrom();
                    $user = User::where('wx_openid','=',$openid)->first();
                    $user->login_count = $user->login_count + 1;
                    $user->amend();
                }
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                // $this->wx->text("你好，感谢您关注大贵客！")->reply();
                break;
        }
    }

    public function postIndex(){
        return $this->getIndex();
    }

}