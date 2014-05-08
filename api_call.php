<?php
/**
 *
* ===========================================
* @Author Ryan
* @Filename: api_call.php
* @Description: 入口文件
* @Creation 2014-5-8 下午3:41:43
* @Modify
* @version V1.0
* -----------------------------------------------------------
*/

define('STOCK_APP' , true);
define('APP_ROOT' , dirname(__FILE__) . '/');

require_once(APP_ROOT.'includes/db/constant.php');
require_once(APP_ROOT.'includes/Util.php');
require_once(APP_ROOT.'includes/db/ryan_mysql.php');

header('Content-type: text/html; charset=utf-8');

$api_wanted = strtolower(trim($_REQUEST['m']));

if (!in_array($api_wanted, $valid_api))
	api_err(API_PARAM_MISSING,$api_wanted);

$_SAFEREQUEST = $_GET;
if (empty($_SAFEREQUEST)) {
	$_SAFEREQUEST = $_POST;
}

require_once(APP_ROOT.'/apicall/'.$api_wanted.'.php');

?>