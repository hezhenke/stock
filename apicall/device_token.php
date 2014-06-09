<?php
/**
  *
  * ===========================================
  * @Author Ryan
  * @Filename: device_token.php
  * @Description: 上传device_token
  * @Creation 2014-6-9 下午12:10:29
  * @Modify
  * 接口参数：	token:device_token
  * @version V1.0
  * -----------------------------------------------------------
*/

if ('device_token'==strtolower(trim($_REQUEST['m']))){
	$m = strtolower(trim($_SAFEREQUEST['m']));

	$table_name = 'user';
	$conn = new ryan_mysql();

	$token = $_SAFEREQUEST['token'];
//	$act = $_SAFEREQUEST['act'];

	if (!isset($token)) {
		data_back(array(),$m,API_PARAM_MISSING);
	}

	$sql = "select * from ".$table_name." where device_token = '".$token."'";
	$item = $conn->getAll($sql);
	if (count($item) == 0) {
		$sql = "insert into ".$table_name." set device_token = '".$token."'";
		$conn->query($sql);
	}

	data_back(array(),$m);

	$conn->close();
}