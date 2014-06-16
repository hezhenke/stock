<?php
/**
  *
  * ===========================================
  * @Author Ryan
  * @Filename: stock_analysis_test.php
  * @Description: (用一句话描述该文件做什么)
  * @Creation 2014-6-11 上午11:29:08
  * @Modify
  * @version V1.0
  * -----------------------------------------------------------
*/

require_once(dirname(__FILE__) . '/../db/ryan_mysql.php');
require_once 'stock_analysis_util.php';

/*
$conn = new ryan_mysql();

		$stock_name = 'test';
		$code = '002423'; //600692
		$table_name = $code;

		$sql = 'SELECT * FROM `'.$table_name.'` WHERE volume > 0 ORDER BY date DESC LIMIT 6';//倒数2天的交易记录
		$detail = $conn->getAll($sql);



		//贯穿形态
		if ($detail && count($detail)>=3) {
			//if ($detail[0]['date'] == date("Y-m-d")) {
				$resultArray = red_gun($detail);

				if ($resultArray[0]) {
					print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$resultArray[1]."\n得分：".$resultArray[2]."\n");
				}
				//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n");
			//}else {
				//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
			//}
		}else {
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}

$conn->close();

exit();
*/


/*
 * ======================================================================================================================================================
 * ======================================================================================================================================================
 * ======================================================================================================================================================
 */

$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes';
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

		/*
		//贯穿形态
		if ($detail && count($detail)>=6) {
			//if ($detail[0]['date'] == date("Y-m-d")) {
			$resultArray = bottom_cross_4_focus($detail);
			if ($resultArray[0]) {
				$isSuggest = $resultArray[0];
				$score = $resultArray[1];
				$reason = $resultArray[2];
				print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$resultArray[1]."\n得分：".$resultArray[2]."\n");
			}
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n");
			//}else {
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
			//}
		}else {
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		*/


		/*
		   测试计算涨跌幅
		if ($detail && count($detail) >= 2) {
		if ($detail[0]['date'] == date("Y-m-d")) {
		$per = cal_percentage($detail);
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n涨跌：".$per."\n");
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}

		 测试是否收阳
		if ($detail) {
		if ($detail[0]['date'] == date("Y-m-d")) {
		$is_red = is_line_red($detail[0]);
		$str = $is_red?"阳":"阴";
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n今日收".$str."\n");
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}

		 测试是否十字星
		if ($detail) {
		if ($detail[0]['date'] == date("Y-m-d")) {
		$is_star = is_star($detail[0]);
		$str = $is_red?"":"未";
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n今日".$str."收十字星\n");
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}

		 测试是否为锤子
		if ($detail) {
		if ($detail[0]['date'] == date("Y-m-d")) {
		$is_hammer = is_hammer($detail[0]);
		if ($is_hammer == 1) {
		$str = '正锤子';
		}elseif ($is_hammer == 0){
		$str = '非锤子';
		}elseif ($is_hammer == -1){
		$str = '倒锤子';
		}

		print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n今日为".$str."\n");
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		}else {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		 */

		/*
		 //长下影
		if ($detail && count($detail)>=3) {
			//if ($detail[0]['date'] == date("Y-m-d")) {

				$resultArray = long_down_shadow_4_focus($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
					print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$resultArray[1]."\n得分：".$resultArray[2]."\n");
				}
				//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n");
				}else {
				//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
				}

			//}else {
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
			//}
		*/


		/*
		 //否极泰来
		if ($detail && count($detail)>=3) {
		if ($detail[0]['date'] == date("Y-m-d")) {
		$resultArray = reverse_bad_to_good($detail);

		if ($resultArray[0]) {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$resultArray[1]."\n得分：".$resultArray[2]."\n");
		}
		//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n");
		}else {
		//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		}else {
		//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		*/

		/*
		 // 多头吞噬
		if ($detail && count($detail)>=3) {
		if ($detail[0]['date'] == date("Y-m-d")) {
		$resultArray = red_eat_green($detail);

		if ($resultArray[0]) {
		print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$resultArray[1]."\n得分：".$resultArray[2]."\n");
		}
		//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n");
		}else {
		//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		}else {
		//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}
		*/

		// 多方炮
		if ($detail && count($detail)>=3) {
			//if ($detail[0]['date'] == date("Y-m-d")) {

			$resultArray = red_gun($detail);
			if ($resultArray[0]) {
				$isSuggest = $resultArray[0];
				$score += $resultArray[1];
				$reason .= $resultArray[2];
				print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$resultArray[1]."\n得分：".$resultArray[2]."\n");
			}
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n");
		}else {
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n今日停牌\n");
		}

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