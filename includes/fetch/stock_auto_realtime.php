<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_auto_realtime.php
* @Description: 自动化获取实时数据入数据库 
* @Creation 2014-5-8 下午3:41:43 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once 'stock_fetch_util.php';
require_once '../Util.php';

$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes';

$result = $conn->getAll($sql);

log_to_text('fetch realtime data START');

if ($result) {
	foreach ($result as $item){
		$code = $item['code'];

		set_realtime_data_into_db($code);
	}
}

log_to_text('fetch realtime data END');

$conn->close();