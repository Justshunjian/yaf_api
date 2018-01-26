<?php

/**
 * User.php.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 15:40
 */
class UserModel
{
    public $errno;
    public $errmsg;
    private $_dao = null;

    public function __construct()
    {
        $this->_dao = new Db_User();
    }

    /**
     * 注册
     * @param $uname    用户名
     * @param $pwd      密码
     * @return bool
     */
    public function register($uname, $pwd){
        //检查用户名是否存在
        if(!$this->_dao->checkExists($uname)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        //检查密码
        if(strlen($pwd) < 8){
            list($this->errno, $this->errmsg) = array_values(Err_Map::get(1006));
            return false;
        }

        //加密密码
        $password = Common_Password::pwdEncode($pwd);

        //插入
        if(! $this->_dao->addUser($uname, $password)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
         }

        return true;
    }

    /**
     * 登录
     * @param string $uname    用户名
     * @param string $pwd      密码
     * @return bool|int
     */
    public function login($uname, $pwd){
        $userinfo = $this->_dao->find($uname);
        if(!$userinfo){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        if(Common_Password::pwdEncode($pwd) != $userinfo['pwd']){
            list($this->errno, $this->errmsg) = array_values(Err_Map::get(1004));
            return false;
        }

        return intval($userinfo['id']);
    }
}