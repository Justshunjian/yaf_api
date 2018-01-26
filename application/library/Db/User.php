<?php

/**
 * User.php.
 * User: Administrator
 * Date: 2017/9/19 0019
 * Time: 16:57
 * Desc: 用户表数据库操作
 */
class Db_User extends Db_Base
{

    /**
     * 查找用户
     * @param $uname    用户名
     * @return bool|用户信息
     */
    public function find( $uname ){
        $stmt = self::getDb()->prepare('select `id`,`pwd` from `user` where `name`=:name');
        $stmt->bindParam(':name', $uname);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$ret || count($ret) != 1){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(1003));
            return false;
        }

        return $ret[0];
    }

    /**
     * 检查用户是否存在
     * @param $uname    用户名
     * @return bool
     */
    public function checkExists( $uname ){
        $stmt = self::getDb()->prepare('select count(*) as c from `user` where `name`=:name');
        $stmt->bindParam(':name', $uname);
        $stmt->execute();
        $count = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //检查用户名是否存在
        if($count[0]['c'] != 0){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(1005));
            return false;
        }

        return true;
    }

    /**
     * 添加新用户
     * @param $uname    用户名
     * @param $password 密码
     * @return bool
     */
    public function addUser($uname, $password){
        //插入
        $query = self::getDb()->prepare('insert into `user`(`id`,`name`,`pwd`,`reg_time`) values (null,?,?,?)');
        $query->bindParam(1, $uname);
        $query->bindParam(2, $password);
        $query->bindParam(3, date('Y-m-d H:i:s'));

        $ret = $query->execute();

        if(!$ret){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(1007));
            return false;
        }

        return true;
    }

    /**
     * 查找用户的邮箱
     * @param $uid  用户ID
     * @return bool
     */
    public function findEmail($uid){
        $stmt = self::getDb()->prepare('select `email` from `user` where `id`=?');
        $stmt->execute(array(intval($uid)));
        $ret = $stmt->fetchAll();
        if(!$ret || empty($ret[0]['email'])){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(3003));
            return false;
        }

        return $ret[0]['email'];
    }
    /**
     * 查找用户的手机号
     * @param $uid  用户ID
     * @return bool
     */
    public function findMobile($uid){
        $stmt = self::getDb()->prepare('select `mobile` from `user` where `id`=?');
        $stmt->execute(array(intval($uid)));
        $ret = $stmt->fetchAll();
        if(!$ret || empty($ret[0]['mobile'])){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(4003));
            return false;
        }
        return $ret[0]['mobile'];
    }
}