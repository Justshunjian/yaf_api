<?php

/**
 * Item.php.
 * User: Administrator
 * Date: 2017/9/20 0020
 * Time: 16:43
 * Desc: 商品表
 */
class Db_Item extends Db_Base
{
    /**
     * 查找商品信息
     * @param $select   筛选项
     * @param $itemId   商品ID
     * @return array|bool
     */
    public function findItem($select, $itemId){
        $stmt = self::getDb()->prepare('select '.$select.' from `item` where `id`=:id');
        $stmt->bindParam(':id', $itemId);
        $stmt->execute();
        $ret = $stmt->fetchAll();
        if(!ret || count($ret) != 1){
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(6003));
            return false;
        }

        return $ret[0];
    }

    /**
     * 查找账单信息
     * @param $select   筛选项
     * @param $billId   账单ID
     * @return bool
     */
    public function findBill($select, $billId){
        $query = self::getDb()->prepare('select '.$select.' from `bill` where `id`= ?');
        $query->execute( array($billId) );
        $ret = $query->fetchAll();
        if( !$ret || count($ret)!=1 ) {
            list(self::$errno, self::$errmsg) = array_values(Err_Map::get(6009));
            return false;
        }
        return $ret[0];
    }

    /**
     * 创建订单
     * @param $itemId   商品ID
     * @param $uid      用户ID
     * @param $price    商品价格
     * @return bool|int
     */
    public function createBill($itemId, $uid, $price){
        $db = self::getDb();
        try{
            //开启事物
            $db->beginTransaction();
            /**
             * 创建bill
             */
            $query = $db->prepare("insert into `bill` (`itemid`,`uid`,`price`,`status`) VALUES ( ?, ?, ?, 'unpaid') ");
            $ret = $query->execute( array( $itemId, $uid, intval($price) ) );
            if ( !$ret ) {
                list(self::$errno, self::$errmsg) = array_values(Err_Map::get(6006));
                throw new PDOException('创建账单失败');
            }
            $lastId = intval($db->lastInsertId());
            /**
             * 成功创建账单后，需要扣去商品库存1件
             */
            $query = $db->prepare("update `item` set `stock`=`stock`-1 where `id`= ? ");
            $ret = $query->execute( array( $itemId ) );
            if ( !$ret ) {
                list(self::$errno, self::$errmsg) = array_values(Err_Map::get(6007));
                throw new PDOException('扣去商品库存失败');
            }
            $db->commit();
        }catch (PDOException $e){
            $db->rollBack();
            return false;
        }


        return $lastId;
    }
}