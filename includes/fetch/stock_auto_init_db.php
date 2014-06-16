<?php
/**
*
* ===========================================
* @Author Ryan
* @Filename: stock_auto_init_db.php
* @Description: 初始化数据库
* @Creation 2014-5-8 下午1:58:36
* @Modify
* @version V1.0
* -----------------------------------------------------------
*/

// http://download.finance.yahoo.com/d/quotes.csv?s=600320.ss&f=sl1d1t1c1ohgv&e=.csv
// http://ichart.yahoo.com/table.csv?s=000001.ss&a=0&b=1&c=2010

require_once 'stock_fetch_util.php';
require_once(dirname(__FILE__) . '/../Util.php');

set_corp_list_into_db_from_sina(FALSE); 	//创建corp_codes列表，并导入公司列表

/*
$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes';

$result = $conn->getAll($sql);

if ($result) {
	foreach ($result as $item){
		$code = $item['code'];

		if (substr($code, 0, 3) == '002') {
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

		if (substr($code, 0, 3) == '000') {

		}else if (substr($code, 0, 3) == '002') {

		}else if (substr($code, 0, 3) == '300') {

		}else if (substr($code, 0, 3) == '600' && intval(substr($code, 3, 3)) > 317) {
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
*/