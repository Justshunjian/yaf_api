<?php

/**
 * Art.php.
 * User: Administrator
 * Date: 2017/9/20 0020
 * Time: 11:43
 * Desc: 文章数据库表管理
 */
class Db_Art extends Db_Base
{
    /**
     * 根据文章ID查找文章数量
     * @param $artId    文章ID
     * @return bool
     */
    public function count($artId){
        $stmt = self::getDb()->prepare('select count(`id`) as c from `art` where `id`=?');
        $stmt->bindParam(1, $artId);
        $stmt->execute();
        $ret = $stmt->fetchAll();
        if(!$ret || $ret[0]['c'] != 1){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2004));
            return false;
        }
        return true;
    }

    /**
     * 根据文章ID查找文章的基本信息
     * @param $artId    文章ID
     * @return bool
     */
    public function find($artId){
        $stmt = self::getDb()->prepare('select `title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `id`=:id');
        $stmt->bindParam(":id", intval($artId));
        $stmt->execute();
        $ret = $stmt->fetchAll();
        if(!$ret){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2009));
            self::$errmsg .= ',ErrorInfo:'.end($stmt->errorInfo());
            return false;
        }
        return $ret[0];
    }

    /**
     * 根据分类ID查找分类
     * @param $cate 分类ID
     * @return bool
     */
    public function countCate($cate){
        $stmt = self::getDb()->prepare('select `id` from `cate` where `id`=:id');
        $stmt->bindParam(':id', $cate);
        $stmt->execute();
        $ret = $stmt->fetchAll();
        if(empty($ret) || $ret[0]['id'] == 0){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2005));
            self::$errmsg .= $cate.'，请先创建该分类';
            return false;
        }
        return true;
    }

    /**
     * 查找分类信息
     * @param $cateId   分类ID
     * @return bool
     */
    public function findCateName($cateId){
        //获取分类信息
        $stmt = self::getDb()->prepare('select `name` from `cate` where `id`=:id');
        $stmt->bindParam(":id", intval($cateId));
        $stmt->execute();
        $ret = $stmt->fetchAll();
        if(!$ret){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2010));
            self::$errmsg .= '，ErrorInfo:'.end($stmt->errorInfo());
            return false;
        }
        return $ret[0]['name'];
    }

    /**
     * 插入|更新 文章
     * @param $isEdit   插入|更新标志位
     * @param $title    文章标题
     * @param $contents 文章内容
     * @param $author   文章作者
     * @param $cate     文章分类
     * @param int $artId    文章ID
     * @return bool
     */
    public function insertAndUpdate($isEdit, $title, $contents, $author, $cate, $artId=0){
        //操作数据库
        $data = array($title, $contents, $author, intval($cate));
        if($isEdit){
            $stmt = self::getDb()->prepare('update `art` set `title`=?, `contents`=?, `author`=?, `cate`=? where `id`=?');
            $data[] = $artId;
        }else{
            $stmt = self::getDb()->prepare('insert into `art` (`title`, `contents`, `author`, `cate`) values(?,?,?,?)');
        }

        $ret = $stmt->execute($data);
        if(empty($ret)){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2006));
            self::$errmsg .= ',ErrorInfo:'.end($stmt->errorInfo());
            return false;
        }

        if($isEdit){
            return intval($artId);
        }

        return intval(self::getDb()->lastInsertId());
    }

    /**
     * 文章删除
     * @param $artId    文章ID
     * @return bool
     */
    public function del($artId){
        $stmt = self::getDb()->prepare('delete from `art` where `id`=:id');
        $stmt->bindParam(":id", intval($artId));
        if(!$stmt->execute()){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2007));
            self::$errmsg .= ',ErrorInfo:'.end($stmt->errorInfo());
            return false;
        }
        return true;
    }

    /**
     * 更新文章状态
     * @param $artId    文章ID
     * @param string $status    文章状态
     * @return bool
     */
    public function status($artId, $status='offline'){
        $stmt = self::getDb()->prepare('update `art` set `status`=:status where `id`=:id');
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", intval($artId));
        if(!$stmt->execute()){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2008));
            self::$errmsg .= ',ErrorInfo:'.end($stmt->errorInfo());
            return false;
        }
        return true;
    }

    /**
     * 分页获取文章列表
     * @param int $pageNo   页码
     * @param int $pageSize 每页数量
     * @param int $cate     分类ID
     * @param string $status    文章状态
     * @return array|bool
     */
    public function list($pageNo=0, $pageSize=10, $cate=0, $status='online'){
        $start = $pageNo*$pageSize+($pageNo==0?0:1);
        if($cate==0){
            $cols = array($status,intval($start),intval($pageSize));
            $sql = 'select `id`,`title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `status`=? order by `ctime` limit ?,?';
        }else{
            $cols = array(intval($cate),$status,intval($start),intval($pageSize));
            $sql = 'select `id`,`title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `cate`=? and `status`=? order by `ctime` limit ?,?';
        }
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($cols);
        $ret = $stmt->fetchAll();
        if(!$ret){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(2011));
            self::$errmsg .= ',ErrorInfo:'.end($stmt->errorInfo());
            return false;
        }

        return $ret;
    }

}