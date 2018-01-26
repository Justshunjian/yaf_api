<?php

/**
 * Art.php.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 17:53
 */
class ArtController extends Yaf_Controller_Abstract
{
    /**
     * 首页
     * @return bool
     */
    public function indexAction(){
        return $this->listAction();
    }

    /**
     * 文章添加
     * @param int $artId    文章ID
     * @return bool
     */
    public function addAction($artId=0){
        if(!Admin_Object::isAdmin()){
            echo Common_Request::responseByArray(Err_Map::get(2000));
            return FALSE;
        }

        $submit = Common_Request::getRequest('submit', 0);
        if($submit != "1"){
            echo Common_Request::responseByArray(Err_Map::get(2001));
            return FALSE;
        }

        //获取参数
        $title = Common_Request::postRequest('title', false);
        $contents = Common_Request::postRequest('contents', false);
        $author = Common_Request::postRequest('author', false);
        $cate = Common_Request::postRequest('cate', false);

        if(empty($title) || empty($contents) || empty($author) || !is_numeric($cate)){
            echo Common_Request::responseByArray(Err_Map::get(2002));
            return FALSE;
        }

        $model = new ArtModel();
        if($lastId = $model->add(trim($title), trim($contents), trim($author), trim($cate), $artId)){
            echo Common_Request::responseByArray(Err_Map::get(0), array('lastId'=>$lastId));
        }else{
            echo Common_Request::response($model->errno, $model->errmsg);
        }

        return false;
    }

    /**
     * 文章编辑
     * @return bool
     */
    public function editAction(){
        if(!Admin_Object::isAdmin()){
            echo Common_Request::responseByArray(Err_Map::get(2000));
            return FALSE;
        }

        $artId = Common_Request::getRequest('artId', "0");
        if($artId && is_numeric($artId)){
            return $this->addAction($artId);
        }

        echo Common_Request::responseByArray(Err_Map::get(2003));
        return FALSE;
    }

    /**
     * 文章删除
     * @return bool
     */
    public function delAction(){
        if(!Admin_Object::isAdmin()){
            echo Common_Request::responseByArray(Err_Map::get(2000));
            return FALSE;
        }

        $artId = Common_Request::postRequest('artId', false);
        if(is_numeric($artId) && $artId){
            $model = new ArtModel();
            if($model->del($artId)){
                echo Common_Request::responseByArray(Err_Map::get(0));
            }else{
                echo Common_Request::response($model->errno, $model->errmsg);
            }
        }else{
            echo Common_Request::responseByArray(Err_Map::get(2003));
        }

        return false;
    }

    /**
     * 文章状态修改
     * @return bool
     */
    public function statusAction(){
        if(!Admin_Object::isAdmin()){
            echo Common_Request::responseByArray(Err_Map::get(2000));
            return FALSE;
        }

        $artId = Common_Request::postRequest('artId', false);
        $status = Common_Request::postRequest('status', false);
        if(is_numeric($artId) && $artId){
            $model = new ArtModel();
            if($model->status($artId, $status)){
                echo Common_Request::responseByArray(Err_Map::get(0));
            }else{
                echo Common_Request::response($model->errno, $model->errmsg);
            }
        }else{
            echo Common_Request::responseByArray(Err_Map::get(2003));
        }

        return false;
    }

    /**
     * 获取文章
     * @return bool
     */
    public function getAction(){
        $artId = Common_Request::postRequest('artId', false);
        if(is_numeric($artId) && $artId){
            $model = new ArtModel();
            if($data = $model->get($artId)){
                echo Common_Request::responseByArray(Err_Map::get(0), $data);
            }else{
                echo Common_Request::responseByArray(Err_Map::get(2012));
            }
        }else{
            echo Common_Request::responseByArray(Err_Map::get(2003));
        }

        return false;

    }

    /**
     * 拉取文章列表
     * @return bool
     */
    public function listAction(){
        $pageNo = Common_Request::getRequest('pageNo', 0);
        $pageSize = Common_Request::getRequest('pageSize', 10);
        $cate = Common_Request::getRequest('cate', 0);
        $status = Common_Request::getRequest('status', 'online');

        $model = new ArtModel();
        if($data = $model->list($pageNo, $pageSize, $cate, $status)){
            echo Common_Request::responseByArray(Err_Map::get(0), $data);
        }else{
            echo Common_Request::response($model->errno, $model->errmsg);
        }
        return false;
    }
}