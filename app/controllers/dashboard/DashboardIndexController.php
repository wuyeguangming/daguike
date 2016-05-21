<?php
class DashboardIndexController extends AuthorizedController {
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
        $this->user = Auth::user(); //区别于$user：后者无登录用户信息，是一个初始化model
        $this->data = array();
        if (!empty($this->user)) {
            $this->data['user'] = $this->user->info();
        }
        $this->__path__ = 'site/dashboard/index';
    }

    /**
     * 个人首页
     * 
     * @return View
     */
    public function getIndex(){
        return $this->display('主页', $this->data);
    }

    /**
     * 更改密码
     * 
     * @return View
     */
    public function getPassword(){
        return $this->display('更改密码', $this->data);
    }

    public function postPassword(){
        $input = Input::all();
        if (!Auth::attempt(array('email' => $input['email'], 'password' => $input['password_old']))) {
            return $this->error('原密码错误');
        }
        $input['password'] = $input['password_new'];
        $res = $this->user->resetPassword($input);
        if ($res) {
            $this->sendEmail($this->user,'修改密码提醒','emails/user/password');
        }
        return $this->result($res);
    }

    /**
     * 账户设置
     * @param 
     * @return 
     */
    public function getSettings(){
        return $this->display('账户设置', $this->data);
    }

    public function postSettings(){
        $this->user->loc_province  = intval(Input::get('loc_province'));
        $this->user->loc_city      = intval(Input::get('loc_city'));
        $this->user->loc_district  = intval(Input::get('loc_district'));
        $this->user->loc_community = intval(Input::get('loc_community'));

        if ($this->user->amend() ){ //amend更新
            return $this->success();
        }else{
            return $this->error($this->user->errors()->all());
        }
    }

    public function getStore($value=''){
        dd("string");
    }






    /**
     * Edits a user
     *
     */
    // public function postEdit($user){
    //     // Validate the inputs
    //     $validator = Validator::make(Input::all(), $user->getUpdateRules());
    //     if ($validator->passes()){
    //         $oldUser = clone $user;
    //         $user->username = Input::get( 'username' );
    //         $user->email = Input::get( 'email' );

    //         // todo: 限制更新时间
    //         $user->loc_province  = intval(Input::get('loc_province'));
    //         $user->loc_city      = intval(Input::get('loc_city'));
    //         $user->loc_district  = intval(Input::get('loc_district'));
    //         $user->loc_community = intval(Input::get('loc_community'));

    //         $password = Input::get( 'password' );
    //         $passwordConfirmation = Input::get( 'password_confirmation' );

    //         if(!empty($password)) {
    //             if($password === $passwordConfirmation) {
    //                 $user->password = $password;
    //                 // The password confirmation will be removed from model
    //                 // before saving. This field will be used in Ardent's
    //                 // auto validation.
    //                 $user->password_confirmation = $passwordConfirmation;
    //             } else {
    //                 // Redirect to the new user page
    //                 return Redirect::to('users')->with('error', Lang::get('admin/users/messages.password_does_not_match'));
    //             }
    //         } else {
    //             unset($user->password);
    //             unset($user->password_confirmation);
    //         }
    //         $user->prepareRules($oldUser, $user);
    //         // Save if valid. Password field will be hashed before save
    //         $user->amend();
    //     }

    //     // Get validation errors (see Ardent package)
    //     $error = $user->errors()->all();
    //     if(empty($error)) {
    //         return Redirect::to('user')
    //             ->with( 'success', Lang::get('user/user.user_account_updated') );
    //     } else {
    //         return Redirect::to('user')
    //             ->withInput(Input::except('password','password_confirmation'))
    //             ->with( 'error', $error );
    //     }
    // }
}
