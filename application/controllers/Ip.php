<?php

/**
 * Ip.php.
 * User: Administrator
 * Date: 2017/9/18 0018
 * Time: 14:36
 */
class IpController extends Yaf_Controller_Abstract
{
    /**
     * IP所属地查询
     * @return bool
     */
    public function getAction()
    {
        $ip = Common_Request::getRequest('ip', '');
        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            echo Common_Request::responseByArray(Err_Map::get(5001));
            return FALSE;
        }

        $model = new IpModel();

        if ($data = $model->get($ip)) {
            echo Common_Request::responseByArray(Err_Map::get(0), $data);
        } else {
            echo Common_Request::response($model->errno, $model->errmsg);
        }

        return false;
    }
}