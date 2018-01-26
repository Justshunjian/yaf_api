<?php
require __DIR__ . '/../vendor/autoload.php';
use \Curl\Curl;

$cookieFile = "/tmp/_tmp_test_api_cookie_file_".rand();

$host = "http://127.0.0.1";
$curl = new Curl();
//$curl->setCookieJar( $cookieFile );
$itemId = 1;
$uname = 'lvfk';
$pwd = '12341234';

/**
 * 生成订单
 */
$curl->post( $host."/yaf_api/public/wxpay/createbill?itemid=".$itemId, array());
if ($curl->error) {
    die( 'Error: ' . $curl->error_code . ': ' . $curl->error_message . "\n" );
} else {
	$rep = json_decode( $curl->response, true );
	if( $rep['errno']!==0 ) {
		echo '未登录创建账单，失败为正常。返回信息:'.$rep['errmsg']."\n";
		echo '尝试登陆账号...'."\n";
		$curl->post( $host."/yaf_api/public/user/login?submit=1", array(
						'uname' => $uname,
						'pwd'	=> $pwd,
					));
		if ($curl->error) {
			die( 'Error: ' . $curl->error_code . ': ' . $curl->error_message . "\n" );
		} else {
			$rep = json_decode( $curl->response, true );
			if( $rep['errno']!==0 ) {
				die( '用户登录失败，错误信息:'.$rep['errmsg']."\n" );
			}
			echo "登陆成功！\n";
		}

		/**
		 * 重新生成订单
		 */
		$curl->post( $host."/yaf_api/public/wxpay/createbill?itemid=".$itemId, array());
		if ($curl->error) {
			die( 'Error: ' . $curl->error_code . ': ' . $curl->error_message . "\n" );
		} else {
            var_dump($curl->response);
			$rep = json_decode( $curl->response, true );
			if( $rep['errno']!==0 ) {
				echo '已登陆状态下，创建账单，失败。返回信息:'.$rep['errmsg']."\n";
			} else {
				echo "生成订单成功(登陆情况下)\n";
			}
		}
	
	} else {
		echo "生成订单成功(未登陆情况下)\n";
	}
}

echo "微信支付接口测试完毕。\n";
$curl->close();
unlink( $cookieFile );
