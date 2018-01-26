<?php

/**
 * Base.php.
 * User: Administrator
 * Date: 2017/9/19 0019
 * Time: 16:51
 * Desc:
 */
class Db_Base
{
    const DB_NAME = 'yaf_api';
    const UNAME = 'root';
    const PWD = 'daqi-123456';

    private static $_db = null;
    protected static $errno = 0;
    protected static $errmsg = '';

    public static function getDb(){
        if ( self:: $_db == null) {
            self::$_db = new PDO('mysql:host=127.0.0.1;dbname='.self::DB_NAME,
                self::UNAME,
                self::PWD,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
                    /*
                     * 不设置下面这行的话，PDO会在拼SQL时候，把int 0转成string 0
                     */
                    PDO::ATTR_EMULATE_PREPARES => false,
                ));
        }
        return self::$_db;
    }

    public function errno(){
        return self::$errno;
    }

    public function errmsg(){
        return self::$errmsg;
    }
}