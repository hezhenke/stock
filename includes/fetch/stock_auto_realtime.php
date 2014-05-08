<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_auto_realtime.php
* @Description: (用一句话描述该文件做什么) 
* @Creation 2014-5-8 下午3:41:43 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once 'stock_fetch_util.php';
$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes';

$result = $conn->getAll($sql);

if ($result) {
	foreach ($result as $item){
		$code = $item['code'];

		set_realtime_data_into_db($code);
	}
}

$conn->close();