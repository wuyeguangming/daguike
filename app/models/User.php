<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\Confide;
use Zizaco\Confide\ConfideEloquentRepository;
use Zizaco\Entrust\HasRole;
use Carbon\Carbon;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends ConfideUser implements UserInterface, RemindableInterface{
    // user更新某个键值用amend: user->amend()
    use HasRole;
    protected $fillable = array('loc_province' ,'loc_city','loc_district','loc_community','login_count');
    protected $visible = array('id','wx_openid','wx_subscribe','wx_nickname','wx_sex','wx_headimgurl','wx_subscribe_time','wx_unionid','loc_province','loc_city','loc_district','loc_community','loc_building','loc_room','store_id','login_count');

    public function store(){
        return $this->hasOne('Store');
    }

    public function hongbao(){
        return $this->hasMany('Hongbao');
    }

    public function isAdmin(){
        return ($this->username === 'admin'); //todo.!!!!!!!!
    }

    /**
     * Get user by username
     * @param $username
     * @return mixed
     */
    public function getUserByUsername( $username )
    {
        return $this->where('username', '=', $username)->first();
    }

    /**
     * Get user by email
     * @param $email
     * @return mixed
     */
    public function getUserByEmail( $email )
    {
        return $this->where('email', '=', $email)->first();
    }

    /**
     * Get the date the user was created.
     *
     * @return string
     */
    public function joined()
    {
        return String::date(Carbon::createFromFormat('Y-n-j G:i:s', $this->created_at));
    }

    /**
     * Save roles inputted from multiselect
     * @param $inputRoles
     */
    public function saveRoles($inputRoles)
    {
        if(! empty($inputRoles)) {
            $this->roles()->sync($inputRoles);
        } else {
            $this->roles()->detach();
        }
    }

    /**
     * Returns user's current role ids only.
     * @return array|bool
     */
    public function currentRoleIds()
    {
        $roles = $this->roles;
        $roleIds = false;
        if( !empty( $roles ) ) {
            $roleIds = array();
            foreach( $roles as &$role )
            {
                $roleIds[] = $role->id;
            }
        }
        return $roleIds;
    }

    /**
     * Redirect after auth.
     * If ifValid is set to true it will redirect a logged in user.
     * @param $redirect
     * @param bool $ifValid
     * @return mixed
     */
    public static function checkAuthAndRedirect($redirect, $ifValid=false)
    {
        // Get the user information
        $user = Auth::user();
        $redirectTo = false;

        if(empty($user->id) && ! $ifValid) // Not logged in redirect, set session.
        {
            Session::put('loginRedirect', $redirect);
            $redirectTo = Redirect::to('user/login')
                ->with( 'notice', Lang::get('user/user.login_first') );
        }
        elseif(!empty($user->id) && $ifValid) // Valid user, we want to redirect.
        {
            $redirectTo = Redirect::to($redirect);
        }

        return array($user, $redirectTo);
    }

    public function currentUser()
    {
        return (new Confide(new ConfideEloquentRepository()))->user();
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    // 用户基本信息
    public function info(){
        $store = $this->store;
        if ($store) {
            $store = $this->store->info();
        }
        return array(
            'id'            => $this->id,
            'username'      => $this->username,
            'store'         => $store,
            'email'         => $this->email,
            'loc_province'  => $this->loc_province,
            'loc_city'      => $this->loc_city,
            'loc_district'  => $this->loc_district,
            'loc_community' => $this->loc_community
        );
    }

    public function saveOrUpdate($array){
        foreach ($array as $key => $value) {
            $this[$key] = $value;
        }
        if (empty($this->id)) {
            return $this->save();
        }else{
            return $this->updateUniques();
        }
    }

    // todo: 改变validate原则 https://github.com/Zizaco/confide
    static public function createByWx($openid){
        $password = randString(10);
        $user = new User;
        $user['wx_openid']             = $openid;
        $user['password']              = $password;
        $user['password_confirmation'] = $password;
        $user['username']              = '__wx__'.$openid;
        $user['email']                 = '__wx__'.$openid.'@daguike.com';//todo
        $user['confirmed']             = true;
        return $user;
    }
}
