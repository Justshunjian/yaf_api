<?php

/**
 * Mail.php.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 14:23
 */
class SmsModel
{
    public $errno;
    public $errmsg;
    private $_dao = null;

    public function __construct()
    {
        $this->_dao = new Db_User();
    }

    public function send($uid, $contents){
        if(!($userMobile = $this->_dao->findMobile($uid))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        if(!$userMobile || !is_numeric($userMobile) || strlen($userMobile) != 11){
            list($this->errno, $this->errmsg) = array_values(Err_Map::get(4004));
            $this->errmsg .= '，手机号为：'.$userMobile;
            return false;
        }

        $smsUid = 'shunjian';
        $smsPwd = 'shunjian-yunxing';
        $sms = new ThirdParty_Sms($smsUid, $smsPwd);
        $contentParam = array('code'=>rand(1000,9999));

        $template = '100006';
        $result = $sms->send($userMobile, $contentParam, $template);

        if($result['stat'] != '100'){
            list($this->errno, $this->errmsg) = array_values(Err_Map::get(4005));
            $this->errmsg .= $result['stat']."({$result['message']})";
            return false;
        }

        return true;
    }
}