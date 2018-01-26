<?php

/**
 * Art.php.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 14:45
 */
class ArtModel
{
    public $errno;
    public $errmsg;
    private $_dao = null;

    public function __construct()
    {
        $this->_dao = new Db_Art();
    }

    /**
     * 添加文章
     * @param $title    文章标题
     * @param $contents 文章内容
     * @param $author   文章作者
     * @param $cate     文章分类
     * @param int $artId    文章ID
     * @return int
     */
    public function add($title, $contents, $author, $cate, $artId=0){
        $isEdit = false;
        if($artId != 0 && is_numeric($artId)){
            if(! $this->_dao->count($artId)){
                $this->errno = $this->_dao->errno();
                $this->errmsg = $this->_dao->errmsg();
                return false;
            }
            $isEdit = true;
        }else{
            if(! $this->_dao->countCate($cate)){
                $this->errno = $this->_dao->errno();
                $this->errmsg = $this->_dao->errmsg();
                return false;
            }
        }

        if(!($ret = $this->_dao->insertAndUpdate($isEdit, $title, $contents, $author, $cate, $artId))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        return $ret;

    }

    /**
     * 文章删除
     * @param $artId    文章ID
     * @return bool
     */
    public function del($artId){
        if(! $this->_dao->del($artId)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
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
    public function status( $artId, $status='offline'){
        if(! $this->_dao->status($artId, $status)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        return true;
    }

    /**
     * 获取文章信息
     * @param  $artId 文章ID
     * @return array|bool
     */
    public function get($artId){
        $artInfo = $this->_dao->find($artId);
        if(empty($artInfo)){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        //获取分类名称
        if(! ($cateName = $this->_dao->findCateName($artInfo['cate']))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        $artInfo['cateName'] = $cateName;

        $data = array(
            'id' => intval($artId),
            'title' => $artInfo['title'],
            'contents' => $artInfo['contents'],
            'author' => $artInfo['author'],
            'cateName' => $artInfo['cateName'],
            'cateId' => intval($artInfo['cate']),
            'ctime' => $artInfo['ctime'],
            'mtime' => $artInfo['mtime'],
            'status' => $artInfo['status'],
        );

        return $data;
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
        if(!($ret = $this->_dao->list($pageNo, $pageSize, $cate, $status))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        $data = array();
        $cateInfo = array();

        foreach ($ret as $item) {
            if(isset($cateInfo[$item['cate']])){
                $cateName = $cateInfo[$item['cate']];
            }else{
                //获取分类名称
                if(! ($cateName = $this->_dao->findCateName($item['cate']))){
                    $this->errno = $this->_dao->errno();
                    $this->errmsg = $this->_dao->errmsg();
                    return false;
                }

                $cateInfo[$item['cate']] = $cateName;
            }

            //正文切割
            $contents = mb_strlen($item['contents'])>30 ? mb_substr($item['contents'], 0, 30).'...' : $item['contents'];

            $data[] = array(
                'id' => intval($item['id']),
                'title' => $item['title'],
                'contents' => $contents,
                'author' => $item['author'],
                'cateName' => $cateName,
                'cateId' => $item['cateId'],
                'ctime' => $item['ctime'],
                'mtime' => $item['mtime'],
                'status' => $item['status'],
            );
        }
        return $data;
    }
}