<?php

class PublicController extends Controller {
    public $_controller;
    /**
     * Initializer.
     *
     * @access   public
     * @return \BaseController
     */
    public function __construct(){
        $this->_controller = Request::header('controller');
    }


    /**
     * Send email using the lang sentence as subject and the viewname
     *
     * @param mixed $title
     * @param mixed $view
     * @param array $params
     * @return bool.
     */
    public function sendEmail($user, $title, $view, $params = array() ){
        $this->__email__ = array(
            'user' => $user,
            'title' => $title
        );
        $params['user'] = $user;
        return Mail::send($view, $params, function($message){
            $message->to($this->__email__['user']['email'], $this->__email__['user']['username'])->subject($this->__email__['title']);
        });
    }

    public function sendEmailToAdmin($title, $view, $params = array()){
        $this->__email__ = array(
            'username' => 'admin',
            'email' => Config::get('app.email.admin','admin@daguike.com'),
            'title' => $title
        );
        return Mail::send($view, $params, function($message){
            $message->to($this->__email__['email'], $this->__email__['username'])->subject($this->__email__['title']);
        });
    }

    public function is_ajax(){
        return (Request::header('accept') == 'application/json, text/plain, */*') || Request::ajax();
    }
    
    public function display($title,$data=array()){
        $data['_token'] = Session::getToken();
        // 自动判断ajax请求和html请求，'application..'是angular的ajax判断方法
        if ($this->is_ajax()) {
            return json_encode($data);
        }
        return View::make($this->__path__,array(
            'title' => $title,
            'data'  => json_encode($data)
        ));
    }

    public function result($res){
        if ($res) {
            return $this->success('',$res);
        }else{
            return $this->error();
        }
    }

    public function error($info='',$data=''){
        return Response::json(array(
            'code' => -1,
            'info' => $info,
            'data' => $data,
            '_token' => Session::getToken()
        ));
    }
    public function success($info='',$data=''){
        if (empty($info) && empty($data) && !empty($this->data)) {
            $data = $this->data;
        }elseif (is_array($info)) {
            $data = $info;
            $info = '';
        }
        return Response::json(array(
            'code' => 1,
            'info' => $info,
            'data' => $data,
            '_token' => Session::getToken()
        ));
    }

    public function redirect($path=''){
        if ($this->is_ajax()) {
            return Response::json(array(
                'url' => $path
            ));
        }
        return Redirect::to($path);
    }
}