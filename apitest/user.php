<?php
/**
 * user.php.
 * User: Administrator
 * Date: 2017/9/19 0019
 * Time: 11:46
 * Desc: 测试用户API
 */

require_once __DIR__.'/../vendor/autoload.php';
use Curl\Curl;

$host = "http://localhost";
$curl = new  Curl();
$uname = 'apitest_uname_'.rand();
$pwd = 'apitest_pwd_'.rand();

/**
 * 注册接口验证
 */
$curl->post($host.'/yaf_api/public/user/register', array(
    'uname' => $uname,
    'pwd' => $pwd,
));
if ($curl->error) {
    die('Error:'.$curl->error_code.':'.$curl->error_message);
}
else {
    $rep = json_decode($curl->response, true);
    if($rep['errno'] != 0){
        die('Error:注册用户失败，注册接口异常，错误信息:'.$rep['errmsg']."\n");
    }
    echo '注册用户接口成功，注册新用户:'.$uname."\n";
}

/**
 * 登录接口验证
 */
$curl->post($host.'/yaf_api/public/user/login?submit=1', array(
    'uname' => $uname,
    'pwd' => $pwd,
));
if ($curl->error) {
    die('Error:'.$curl->error_code.':'.$curl->error_message);
}
else {
    $rep = json_decode($curl->response, true);
    if($rep['errno'] != 0){
        die('Error:登录失败，登录接口异常，错误信息:'.$rep['errmsg']."\n");
    }
    echo '用户登录成功，新用户:'.$uname.",密码:".$pwd."\n";
}

echo 'check done!';