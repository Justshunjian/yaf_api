<?php

/**
 * Password.php.
 * User: Administrator
 * Date: 2017/9/19 0019
 * Time: 16:40
 * Desc: 密码处理
 */
class Common_Password
{
    /*
     *密码混淆参数
     */
    const  SALF = 'lvfk_';

    static public function pwdEncode( $pwd ){
        //混淆一部分程序自定义字符串，保证用户的密码安全
        return md5(self::SALF.$pwd);
    }
}