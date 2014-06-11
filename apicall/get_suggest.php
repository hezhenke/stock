<?php
/**
  *
  * ===========================================
  * @Author Ryan
  * @Filename: get_suggest.php
  * @Description: 获取推荐列表
  * @Creation 2014-6-10 下午7:09:17
  * @Modify
  * @version V1.0
  *
  * 接口参数：	rtime:请求的数据日期
  *
  *
  * -----------------------------------------------------------
  */

if ('get_suggest'==strtolower(trim($_REQUEST['m']))){
	$m = strtolower(trim($_SAFEREQUEST['m']));

	$table_name = 'suggest_list';
	$conn = new ryan_mysql();

	$rtime = $_SAFEREQUEST['rtime'];
	//$act = $_SAFEREQUEST['act'];

	if (!isset($rtime)) {
		$rtime = date("Y-m-d");
	}

	$sql = 'select * from '.$table_name.' where date = "'.$rtime.'"';

	$item = $conn->getAll($sql);

	if ($item) {

		data_back($item,$m);
	}else {
		data_back(array(),$m);
	}

	$conn->close();
}