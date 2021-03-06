<?php

/**
 * Wxpay.php.
 * User: Administrator
 * Date: 2017/9/18 0018
 * Time: 17:04
 */
$qrcodeLibPath = dirname(__FILE__).'/../library/ThirdParty/Qrcode/';
include_once( $qrcodeLibPath.'Qrcode.php' );

/**
 * Class WxpayController
 */
class WxpayController extends Yaf_Controller_Abstract
{
    /**
     * 生成账单
     * @return bool
     */
    public function createbillAction() {
        $itemid = Common_Request::getRequest( "itemid", "" );
        if( !$itemid ) {
            echo Common_Request::responseByArray(Err_Map::get(6001));
            return FALSE;
        }

        /**
         * 检查是否登录
         */
        session_start();
        if( !isset($_SESSION['user_token_time']) || !isset($_SESSION['user_token']) || !isset($_SESSION['user_id'])
            || md5( "salt".$_SESSION['user_token_time'].$_SESSION['user_id'] ) != $_SESSION['user_token'] ) {
            echo Common_Request::responseByArray(Err_Map::get(6002));
            return FALSE;
        }

        // 调用Model
        $model = new WxpayModel();
        if ( $lastId=$model->createbill( $itemid, $_SESSION['user_id'] ) ) {
            echo Common_Request::responseByArray(Err_Map::get(0), array('lastId'=>$lastId));
        } else {
            echo Common_Request::response($model->errno, $model->errmsg);
        }

        return FALSE;
    }

    /**
     * 生成账单微信付款二维码
     * @return bool
     */
    public function qrcodeAction() {
        $billId = Common_Request::getRequest( "billid", false );
        if( !$billId ) {
            echo Common_Request::responseByArray(Err_Map::get(6008));
            return FALSE;
        }

        // 调用Model
        $model = new WxpayModel();
        if ( $data=$model->qrcode( $billId ) ) {
            /**
             * 输出二维码
             */
            QRcode::png($data);

        } else {
            echo Common_Request::response($model->errno, $model->errmsg);
        }
        return FALSE;
    }

    /**
     * 微信付款回调
     * @return bool
     */
    public function callbackAction(){
        $model = new WxpayModel();
        $model->callback();
        echo Common_Request::responseByArray(Err_Map::get(0));
        return TRUE;
    }
}