<?php

/**
 * Mail.php.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 14:23
 */
require_once __DIR__.'/../../vendor/autoload.php';
use Nette\Mail\Message;

/**
 * 邮件模型
 */
class MailModel
{
    public $errno;
    public $errmsg;
    private $_dao = null;

    /**
     * MailModel constructor.
     */
    public function __construct()
    {
        $this->_dao = new Db_User();
    }

    /**
     * 发送邮件
     * @param $uid  用户ID
     * @param $title   邮件标题
     * @param $contents 邮件内容
     * @return bool
     */
    public function send($uid, $title, $contents){
        if(!($userEmail = $this->_dao->findEmail($uid))){
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL)){
            list($this->errno, $this->errmsg) = array_values(Err_Map::get(3004));
            $this->errmsg .= '，邮箱地址为：'.$userEmail;
            return false;
        }

        $mail = new Message();
        $mail->setFrom('eSmartCam@elinksmart.com','PHP API实战')
            ->addTo($userEmail)
            ->setSubject($title)
            ->setHtmlBody($contents);

        $mailer = new \Nette\Mail\SmtpMailer([
            'host'=>'smtp.exmail.qq.com',
            'username'=>'eSmartCam@elinksmart.com',
            'password'=>'Elink123',
            'secure'=>'ssl'
        ]);

        $mailer->send($mail);
        return true;
    }
}