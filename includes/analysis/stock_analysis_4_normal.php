<?php
/**
  *
  * ===========================================
  * @Author Ryan
  * @Filename: stock_analysis_4_normal.php
  * @Description: 分析模型_4_通用
  * @Creation 2014-6-9 下午6:24:15
  * @Modify
  * @version V1.0
  * -----------------------------------------------------------
*/

require_once(dirname(__FILE__) . '/../db/ryan_mysql.php');
require_once 'stock_analysis_util.php';

$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes where focus = 0';
$result = $conn->getAll($sql);
if ($result) {
	foreach ($result as $corp){
		$stock_name = $corp['name'];
		$code = $corp['code'];
		$table_name = $code;

		$score = 0;
		$reason = '';
		$isSuggest = FALSE;
		$focus = 1;

		$sql = 'SELECT * FROM `'.$table_name.'` WHERE volume > 0 ORDER BY date DESC LIMIT 6';//倒数6天的交易记录
		$detail = $conn->getAll($sql);

		//贯穿形态
		if ($detail && count($detail)>=6) {
			if ($detail[0]['date'] == date("Y-m-d")) {
				$resultArray = bottom_cross_4_normal($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score = $resultArray[1];
					$reason = $resultArray[2];
				}
			}
		}

		// 写入数据库
		if ($isSuggest) {
			$date = date("Y-m-d");

			$sql = 'select * from suggest_list where code="'.$code.'" and date="'.$date.'"';
			$item = $conn->getAll($sql);
			if (count($item) == 0) {
				$sql = 'insert into suggest_list set code="'.$code.'",date="'.$date
				.'",score="'.$score.'",reason="'.$reason.'",focus="'.$focus.'"';
			}else{
				$sql = 'update suggest_list set score="'.$score.'",reason="'.$reason
				.'",focus="1" where code="'.$code.'"';
			}
			$conn->query($sql);
		}
	}
}

$conn->close();