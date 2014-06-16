<?php
/**
*
* ===========================================
* @Author Ryan
* @Filename: stock_analysis_model.php
* @Description: 分析模型_4_关注列表
* @Creation 2014-4-25 下午2:45:59
* @Modify
* @version V1.0
*
* 使用：	1、贯穿形态	a.大阴线后面紧跟一个大阳线；b.大阳的实体深入大阴的中位之上；c.开盘越低，收盘越高，反转可能越大
* 		2、长下影	下降途中收长下影
* 		3、否极泰来 	a.最低价高于昨日大阴线实体的中间位；b.中阳线应无上影或上影极短；c.不能缩量，要适度放量；d.最高价高于大阴线最高价
* -----------------------------------------------------------
*/

require_once(dirname(__FILE__) . '/../db/ryan_mysql.php');
require_once(dirname(__FILE__) . '/../../push/push_service.php');
require_once 'stock_analysis_util.php';

$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes where focus = 1';
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
				$resultArray = bottom_cross_4_focus($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
				}
			}
		}

		// 长下影
		if ($detail && count($detail)>=6) {
			if ($detail[0]['date'] == date("Y-m-d")) {
				$resultArray = long_down_shadow_4_focus($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
				}
			}
		}

		// 否极泰来
		if ($detail && count($detail)>=6) {
			if ($detail[0]['date'] == date("Y-m-d")) {
				$resultArray = reverse_bad_to_good($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
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

	push_noti("关注列表的推荐已完成~~");
}

$conn->close();
