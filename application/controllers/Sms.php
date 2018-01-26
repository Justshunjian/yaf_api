<?php

/**
 * Sms.php.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 16:51
 */
class SmsController extends Yaf_Controller_Abstract
{
    /**
     *
     */
    public function indexAction(){

    }

    /**
     * 短信发送
     * @return bool
     */
    public function sendAction(){
        $submit = Common_Request::getRequest('submit', 0);
        if($submit != "1"){
            echo Common_Request::responseByArray(Err_Map::get(4001));
            return FALSE;
        }

        //获取参数
        $uid = Common_Request::postRequest('uid', false);
        $contents = Common_Request::postRequest('contents', false);

        if(empty($contents) || empty($uid)){
            echo Common_Request::responseByArray(Err_Map::get(4002));
            return FALSE;
        }
        $model = new SmsModel();
        if($model->send(trim($uid), trim($contents))){
            echo Common_Request::responseByArray(Err_Map::get(0));
        }else{
            echo Common_Request::response($model->errno, $model->errmsg);
        }

        return false;
    }
}