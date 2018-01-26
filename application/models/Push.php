<?php

/**
 * Push.php.
 * User: Administrator
 * Date: 2017/9/18 0018
 * Time: 10:46
 */

/**
 * 引入小米推送的lib
 */

require_once __DIR__.'/../library/ThirdParty/xmpush/php/sdk/autoload.php';

use xmpush\Constants;
use xmpush\Sender;
use xmpush\IOSBuilder;

/**
 * 推送模型
 */
class PushModel
{
    public $errno;
    public $errmsg;

    /**
     * PushModel constructor.
     */
    public function __construct()
    {
    }

    /**
     * 别名推送
     * @param $cid  别名
     * @param $title    推送标题
     * @param $msg      提示内容
     * @return bool
     */
    public function sendToAlias($cid, $title, $msg){
        Constants::useOfficial();
        Constants::setBundleId('com.Ddqi.iSmartCam');
        Constants::setSecret('s2f8q9CMAUW3nV18A7lBxA==');

        $sender = new Sender();

        $message = new IOSBuilder();
        $message->description($msg);
        $message->soundUrl('default');
        $message->badge('1');
        $message->extra('payload', "{'title':{$title},'msg':{$msg}}");
        $message->build();

        $res = $sender->sendToAlias($message, $cid, 3);

        if($res->getErrorCode() != 0){
            list($this->errno, $this->errmsg) = Err_Map::get(7003);
            $this->errmsg .= '，原因：'.json_encode($res->getRaw());
            return false;
        }

        return true;
    }
}