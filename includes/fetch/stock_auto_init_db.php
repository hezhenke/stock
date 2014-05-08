<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_auto_init_db.php
* @Description: (用一句话描述该文件做什么) 
* @Creation 2014-5-8 下午1:58:36 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once 'stock_fetch_util.php';
require_once '../Util.php';

$conn = new ryan_mysql();

//set_corp_list_into_db_from_sina(true);

$sql = 'SELECT * FROM corp_codes';

$result = $conn->getAll($sql);

if ($result) {
	foreach ($result as $item){
		$code = $item['code'];
		
		if (substr($code, 0, 3) == '000') {
		
		}else if (substr($code, 0, 3) == '002') {

		}else {
			
			$success = false;
			$count = 0;
			
			while (!$success && $count < 10){
				down_csv($code);
				print_r($code." csv download success\n");
				$success = set_corp_data_into_db($code);
				print_r($code." init db success\n");
				$count++;
			}
			
			if (!$success) {
				log_to_text('corp code: '.$code.' set data into db failed');
			}
			
		}
	}
}

$conn->close();