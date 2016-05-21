<?php
class UserIndexController extends BaseController {
    /**
     * User Model
     * @var User
     */
    protected $user;

    /**
     * Inject the models.
     * @param User $user
     */
    public function __construct(User $user){
        parent::__construct();
        $this->user = $user;
        $this->__path__ = 'site/user/index';
    }

    /**
     * 个人首页
     * 
     * @return View
     */
    public function getIndex(){
        return Redirect::to('/dashboard');
    }


    /**
     * 注册页
     *
     */
    public function getCreate(){
        if(Auth::check()){
            return $this->redirect('/');
        }
        return $this->display('注册');
    }

    /**
     * 保存新用户
     *
     */
    public function postIndex(){
        $this->user->username = Input::get( 'username' );
        $this->user->email    = Input::get( 'email' );
        $password             = Input::get( 'password' );
        $passwordConfirmation = Input::get( 'password_confirmation' );

        $this->user->loc_province  = intval(Input::get('loc_province'));
        $this->user->loc_city      = intval(Input::get('loc_city'));
        $this->user->loc_district  = intval(Input::get('loc_district'));
        $this->user->loc_community = intval(Input::get('loc_community'));

        if(!empty($password)) {
            if($password === $passwordConfirmation) {
                $this->user->password = $password;
                // The password confirmation will be removed from model
                // before saving. This field will be used in Ardent's
                // auto validation.
                $this->user->password_confirmation = $passwordConfirmation;
            }
        } else {
            unset($this->user->password);
            unset($this->user->password_confirmation);
        }

        // Save if valid. Password field will be hashed before save
        $this->user->save();
        if ( $this->user->id ){
            return $this->success();
        }else{
            if ($this->user->getUserByUsername($this->user->username)) {
                return $this->error('用户名已被注册');
            }
            if ($this->user->getUserByEmail($this->user->email)) {
                return $this->error('邮箱已被注册');
            }
            // Get validation errors (see Ardent package)
            return $this->error($this->user->errors()->all());
        }
    }


    /**
     * 登陆
     *
     */
    public function getLogin(){
        if(Auth::check()){
            return $this->redirect('/');
        }
        return $this->display('登录');
    }

    public function postLogin() {
        $input = array(
            'email'    => Input::get( 'username' ), // May be the username too
            'username' => Input::get( 'username' ), // May be the username too
            'password' => Input::get( 'password' ),
            'remember' => Input::get( 'remember' ),
        );
        if ( Confide::logAttempt( $input, true ) ) { // logAttempt(..,true):仅限邮箱已确认账户登陆
            $this->success();
        } else {
            if ( Confide::isThrottled( $input ) ) { 
                return $this->error('尝试次数过多，请稍后再试。');
            } elseif ( ! $this->user->checkUserExists( $input ) ) {
                return $this->error('用户名、电子邮箱不存在，或尚未注册。');
            } elseif ( ! $this->user->isConfirmed( $input ) ) {
                return $this->error('您的账户尚未验证，请验证您的电子邮箱后再登录。');
            } else {
                return $this->error('密码错误。');
            }
        }
    }

    /**
     * 邮箱验证
     *
     * @param  string  $code
     */
    public function getConfirm( $code ){
        if ( Confide::confirm( $code ) ){
            $data = array('info'=>'您的账户已验证！您现在可以登录了。');
        }else{
            $data = array('info'=>'验证码错误！');
        }
        return $this->display('邮箱验证',$data);
    }

    /**
     * 忘记密码
     *
     */
    public function getForgot(){
        return $this->display('忘记密码');
    }

    public function postForgot() {
        if( Confide::forgotPassword( Input::get( 'email' ) ) ) {
            return $this->success();
        }else{
            return $this->error('该邮箱地址尚未被注册。');
       }
    }

    /**
     * 重置密码
     *
     */
    public function getReset( $token ){
        $data = array(
            'token' => $token
        );
        return $this->display('重置密码',$data);
    }

    public function postReset(){
        $input = array(
            'token'=>Input::get( 'token' ),
            'password'=>Input::get( 'password' ),
            'password_confirmation'=>Input::get( 'password_confirmation' ),
        );
        if( Confide::resetPassword( $input ) ){
            return $this->success();
        }else{
            return $this->error('密码无效，请重试。');
        }
    }

    /**
     * 登出
     *
     */
    public function getLogout(){
        Confide::logout();
        return Redirect::to('/user/login');
    }


    /**
     * 获取用户信息 todo
     * @param $username
     * @return mixed
     */
    public function getProfile($username){
        // $userModel = new User;
        // $user = $userModel->getUserByUsername($username);

        // // Check if the user exists
        // if (is_null($user)){
        //     return App::abort(404);
        // }

        // return View::make('site/user/profile', compact('user'));
    }
}
