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
 * 			2、长下影	下降途中收长下影
 * 			3、否极泰来 	a.最低价高于昨日大阴线实体的中间位；b.中阳线应无上影或上影极短；c.不能缩量，要适度放量；d.最高价高于大阴线最高价
 * 			4、多头吞噬	a.阳线吃掉昨日阴线的所有实体；b.强度与阳线有关，最好是连阴线的阴线也一起吞噬。多头吞噬强于贯穿形态
 * 			5、多方炮	a.前天收阳，涨幅大于2%；昨天收阴，振幅大于3%；今天收阳，涨幅大于2%；b.中间阴线的实体要在两个阳线实体之间
 *
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
		$analysis_date = date("Y-m-d");
		$stock_name = $corp['name'];
		$code = $corp['code'];
		$table_name = $code;

		$score = 0;
		$reason = '';
		$isSuggest = FALSE;
		$focus = 1;

		$sql = 'SELECT * FROM `'.$table_name.'` WHERE volume > 0 ORDER BY date DESC LIMIT 31';//倒数6天的交易记录
		$detail = $conn->getAll($sql);

		//贯穿形态
		if ($detail && count($detail)>=6) {
			if ($detail[0]['date'] == $analysis_date) {
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
			if ($detail[0]['date'] == $analysis_date) {
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
			if ($detail[0]['date'] == $analysis_date) {
				$resultArray = reverse_bad_to_good($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
				}
			}
		}

		// 多头吞噬
		if ($detail && count($detail)>=6) {
			if ($detail[0]['date'] == $analysis_date) {
				$resultArray = red_eat_green_4_focus($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
				}
			}
		}

		// 多方炮
		if ($detail && count($detail)>=6) {
			if ($detail[0]['date'] == $analysis_date) {
				$resultArray = red_gun($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
				}
			}
		}

		// 一日穿N线
		if ($detail && count($detail)>=30) {
			if ($detail[0]['date'] == $analysis_date) {
				$resultArray = red_cross_average_line($detail);
				if ($resultArray[0]) {
					$isSuggest = $resultArray[0];
					$score += $resultArray[1];
					$reason .= $resultArray[2];
				}
			}
		}

		if ($isSuggest) {
			//print_r("股票名：".$stock_name."\n股票代码：".$code."\n收盘价：".$detail[0]['close']."\n推荐理由：".$reason."\n得分：".$score."\n");
		}

		// 写入数据库
		if ($isSuggest) {

			// 阳线放量
			if ($detail && count($detail)>=6) {
				if ($detail[0]['date'] == $analysis_date) {
					$resultArray = volume_increase($detail);
					if ($resultArray[0]) {
						$isSuggest = $resultArray[0];
						$score += $resultArray[1];
						$reason .= $resultArray[2];
					}
				}
			}

			$tempArray = array_slice($detail, 0, 2);
			$percent = cal_percentage($tempArray);
			$close = $detail[0]['close'];

			$reason = substr($reason, 0, strlen($reason)-1);
			$reason .= "。";

			$sql = 'select * from suggest_list where code="'.$code.'" and date="'.$analysis_date.'"';
			$item = $conn->getAll($sql);
			if (count($item) == 0) {
				$sql = 'insert into suggest_list set code="'.$code.'",name="'.$stock_name.'",date="'.$analysis_date
				.'",score="'.$score.'",reason="'.$reason.'",close="'.$close.'",percent="'.$percent.'",focus="'.$focus.'"';
			}else{
				$sql = 'update suggest_list set score="'.$score.'",reason="'.$reason
				.'",close="'.$close.'",percent="'.$percent.'",focus="'.$focus.'" where code="'.$code.'"';
			}
			$conn->query($sql);
		}
	}

	push_noti("关注列表的推荐已完成~~");
}

$conn->close();
