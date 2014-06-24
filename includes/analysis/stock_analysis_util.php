<?php
/**
*
* ===========================================
* @Author Ryan
* @Filename: stock_analysis_util.php
* @Description: 初级处理数据工具，如计算KDJ，BOLL等
* @Creation 2014-5-7 下午1:02:47
* @Modify
* @version V1.0
*
* @基本概念	十字星 	<0.5%
* 			小阳线 	0.5%~1.5%
* 			中阳线	1.5%~3.5%
* 			大阳线	>3.5%
* -----------------------------------------------------------
*/

/*	============================================================================================
 *
 * 	基本函数
 *
 * 	--------------------------------------------------------------------------------------------
 */

/*
 * Params:两天全数据的数组
 * Return:涨跌幅
 * Description：算涨跌幅，正为涨，负为跌 xg:(c-ref(c,1))/c*100
 * Status:Test Good
 */
function cal_percentage($dataArray){
	$t_close = $dataArray[0]['close'];
	$y_close = $dataArray[1]['close'];

	$result = ($t_close-$y_close)/$y_close *100;
	return round($result,2);
}

/*
 * Params:当天全数据的Dic
 * Return:true为阳或平收，false为阴
 * Description：是否为阳线	xg:c>o
 * Status:Test Good
 */
function is_line_red($item){
	$close = $item['close'];
	$open = $item['open'];

	if ($close >= $open) {
		return true;
	}else {
		return false;
	}
}

/*
 * Params:当天全数据的Dic
 * Return:true为十字星，false为否
 * Description：是否为十字星 xg:abs(c-o)/o<0.01;
 * Status:Test Good
 */
function is_star($item){
	$close = $item['close'];
	$open = $item['open'];

	if (abs($close-$open)/$open<0.01) {
		return true;
	}else {
		return false;
	}
}

/*
 * Params:当天全数据的Dic
 * Return:1:正锤子 0:非锤子 -1:倒锤子。注：流行线也算在内
 * Description：是否为锤子;
 * Status:Test Good
 */
function is_hammer($item){
	$open = $item['open'];
	$close = $item['close'];
	$high = $item['high'];
	$low = $item['low'];

	$entity = abs($close-$open)==0?0.01:abs($close-$open);
	$down_shadow = (min(array($open,$close))-$low)==0 ?0.01:(min(array($open,$close))-$low);
	$up_shadow = ($high - min(array($open,$close)))==0?0.01:($high - min(array($open,$close)));

	$down_factor = $down_shadow/$entity;
	$up_factor = $up_shadow/$entity;

	if ($down_factor > 3 && ($down_factor/$up_factor) > 8) {
		return 1;
	}

	if ($up_factor > 3 && ($up_factor/$down_factor) > 8) {
		return -1;
	}

	return 0;
}

/*
 * Params:传入N天的全数据数组
 * Return:当天的N天均线值
 * Description：计算N天均线值
 * Status:Test Good
 */
function MA($dataArray){
	if (is_array($dataArray)) {
		$count = count($dataArray);
		$sum = 0;

		for ($i = 0;$i < $count; $i++){
			$sum += $dataArray[$i]['close'];
		}

		return $sum/$count;

	}else {
		return 0;
	}
}

/*
 * Params:传入6天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：下降途中收长下影
 * Status:Test Good
 */
function long_down_shadow_4_focus($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);

	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3) < -8 || ($y_per_1+$y_per_2)<-8) {

		$t_detail = $dataArray[0];
		$y_detail = $dataArray[1];

		$y_close = $y_detail['close'];
		$y_open = $y_detail['open'];
		$y_high = $y_detail['high'];
		$y_volume = $y_detail['volume'];
		$y_mid = ($y_close+$y_open)/2;

		$t_close = $t_detail['close'];
		$t_open = $t_detail['open'];
		$t_high = $t_detail['high'];
		$t_low = $t_detail['low'];
		$t_volume = $t_detail['volume'];

		$down_shadow = (min(array($t_open,$t_close))-$t_low)==0 ?0.01:(min(array($t_open,$t_close))-$t_low);

		if ($t_high == $t_close) {
			return array(false,0,'');
		}

		// 收长下影，并且幅度大于8
		if ($down_shadow/($t_high-$t_low)>0.5 && ($t_high-$t_low)*100/$y_close>8 && $y_per_1<0){
			$reasonStr = "收出长下影";
			$score = 10;

			if ($down_shadow/($t_high-$t_low)>0.8) {
				$reasonStr .= ",并且下影子很长";
				$score += 10;
			}

			if ($t_close >= $t_open) {
				$reasonStr .= ",今日最终收阳线";
				$score += 10;
			}

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
* Return:数组{'true or false','分析','推荐权重'}
* Description：下降途中收长下影
* Status:Test Good
*/
function long_down_shadow_4_normal($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);

	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3) < -8 || ($y_per_1+$y_per_2)<-8) {
		$t_detail = $dataArray[0];
		$y_detail = $dataArray[1];

		$y_close = $y_detail['close'];
		$y_open = $y_detail['open'];
		$y_high = $y_detail['high'];
		$y_volume = $y_detail['volume'];
		$y_mid = ($y_close+$y_open)/2;

		$t_close = $t_detail['close'];
		$t_open = $t_detail['open'];
		$t_high = $t_detail['high'];
		$t_low = $t_detail['low'];
		$t_volume = $t_detail['volume'];

		$down_shadow = (min(array($t_open,$t_close))-$t_low)==0 ?0.01:(min(array($t_open,$t_close))-$t_low);

		if ($t_high == $t_close) {
			return array(false,0,'');
		}

		// 收长下影，并且幅度大于8
		if ($down_shadow/($t_high-$t_low)>0.5 && ($t_high-$t_low)*100/$y_close>8 && $y_per_1<0){
			$reasonStr = "收出长下影";
			$score = 10;

			if ($down_shadow/($t_high-$t_low)>0.8) {
				$reasonStr .= ",并且下影子很长";
				$score += 10;
			}

			if ($t_close >= $t_open) {
				$reasonStr .= ",今日最终收阳线";
				$score += 10;
			}

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：否极泰来 1.最低价高于昨日大阴线实体的中间位；2.中阳线应无上影或上影极短；3.不能缩量，要适度放量；4.最高价高于大阴线最高价
 * Status:Test Good
 */
function reverse_bad_to_good($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);

	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3) < -8 || ($y_per_1+$y_per_2)<-8) {

		$t_detail = $dataArray[0];
		$y_detail = $dataArray[1];

		$y_close = $y_detail['close'];
		$y_open = $y_detail['open'];
		$y_high = $y_detail['high'];
		$y_volume = $y_detail['volume'];
		$y_mid = ($y_close+$y_open)/2;

		$t_close = $t_detail['close'];
		$t_open = $t_detail['open'];
		$t_high = $t_detail['high'];
		$t_low = $t_detail['low'];
		$t_volume = $t_detail['volume'];

		//未跌满5个点以上，或者实体没有到4个点的
		if ($y_per_1>-4 || ($y_open-$y_close)*100/$y_close < 2) {
			return array(false,0,'');
		}

		// 1.最低价高于昨日大阴线实体的中间位；2.中阳线应无上影或上影极短；3.不能缩量，要适度放量；4.最高价高于大阴线最高价
		if ($t_low > $y_mid && ($t_high-$t_close)/$y_close<0.003 && $t_volume > $y_volume && $t_high >= $y_high) {
			$reasonStr = "形成否极泰来";
			$score = 30;

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：贯穿形态 1.大阴线后面紧跟一个大阳线；2.大阳的实体深入大阴的中位之上；3.开盘越低，收盘越高，反转可能越大
 * Status:Test Good
 */
function bottom_cross_4_focus($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);


	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3)<-8 || ($y_per_1+$y_per_2)<-8) {

		$t_detail = $dataArray[0];
		$y_detail = $dataArray[1];

		$y_close = $y_detail['close'];
		$y_open = $y_detail['open'];
		$y_mid = ($y_close+$y_open)/2;

		$t_close = $t_detail['close'];
		$t_open = $t_detail['open'];

		//未跌满4个点以上，或者实体没有到2个点的，或开盘高于昨日收盘
		if ($y_per_1>-4 || ($y_open-$y_close)*100/$y_close < 2 || $t_open > $y_close) {
			return array(false,0,'');
		}

		if ($t_close > $y_mid && $t_close < $y_open) {
			$reasonStr = "形成贯穿形态";
			$score = 10;

			$temp_per = round(($t_open-$y_close)*100/$y_close,2); // 是否低开1%以上
			if ($temp_per <= -1) {
				$score += 5;
				$reasonStr .= "，且低开".abs($temp_per).'%';
			}

			$temp_per = round(($y_open-$t_close)*100/$y_close,2); // 收盘是否离大阴线上沿很近
			if ($temp_per < 2) {
				$score += 10;
				$reasonStr .= ",最终收盘离昨日开盘价差".$temp_per.'%';
			}

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
* Return:数组{'true or false','分析','推荐权重'}
* Description：贯穿形态 1.大阴线后面紧跟一个大阳线；2.大阳的实体深入大阴的中位之上；3.开盘越低，收盘越高，反转可能越大
* Status:Test Good
*/
function bottom_cross_4_normal($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);


	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3)<-8 || ($y_per_1+$y_per_2)<-8) {

		$t_detail = $dataArray[0];
		$y_detail = $dataArray[1];

		$y_close = $y_detail['close'];
		$y_open = $y_detail['open'];
		$y_mid = ($y_close+$y_open)/2;

		$t_close = $t_detail['close'];
		$t_open = $t_detail['open'];

		//未跌满4个点以上，或者实体没有到2个点的
		if ($y_per_1>-4 || ($y_open-$y_close)*100/$y_close < 2 || $t_open > $y_close) {
			return array(false,0,'');
		}

		if ($t_close > $y_mid && $t_close < $y_open) {
			$reasonStr = "形成贯穿形态";
			$score = 10;

			$temp_per = round(($t_open-$y_close)*100/$y_close,2); // 是否低开1%以上
			if ($temp_per <= -1) {
				$score += 5;
				$reasonStr .= "，且低开".abs($temp_per).'%';
			}

			$temp_per = round(($y_open-$t_close)*100/$y_close,2); // 收盘是否离大阴线上沿很近
			if ($temp_per < 2) {
				$score += 10;
				$reasonStr .= ",最终收盘离昨日开盘价差".$temp_per.'%';
			}

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：多头吞噬 1.阳线吃掉昨日阴线的所有实体；2.强度与阳线有关，最好是连阴线的阴线也一起吞噬。多头吞噬强于贯穿形态
 * Status:Test Good
 */
function red_eat_green_4_focus($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);

	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3) < -8 || ($y_per_1+$y_per_2)<-8) {
		$t_per = cal_percentage($dataArray);//计算今日涨跌幅

		print_r(123);

		if ($y_per_1<-1 && $t_per>2){

			$t_detail = $dataArray[0];
			$y_detail = $dataArray[1];

			$y_close = $y_detail['close'];
			$y_open = $y_detail['open'];
			$y_high = $y_detail['high'];
			$y_low = $y_detail['low'];

			$t_close = $t_detail['close'];
			$t_open = $t_detail['open'];

			if ($t_close>$y_open && $t_open<$y_close) {
				$reasonStr = "形成多头吞噬形态";
				$score = 25;

				if ($t_open<$y_low) {
					$score += 5;

				}

				if ($t_close>$y_high){
					$score += 10;
					$reasonStr .= ",吞噬了下影线";
				}

				if ($score == 40) {
					$reasonStr .= ",并呈现吞噬所有影线的完美状态";
				}

				return array(true,$score,$reasonStr);
			}
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：多头吞噬 1.阳线吃掉昨日阴线的所有实体；2.强度与阳线有关，最好是连阴线的阴线也一起吞噬。多头吞噬强于贯穿形态
 * Status:Test Good
 */
function red_eat_green_4_normal($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);

	if (($y_per_1+$y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_1+$y_per_2+$y_per_3) < -8 || ($y_per_1+$y_per_2)<-8) {
		$t_per = cal_percentage($dataArray);//计算今日涨跌幅

		if ($y_per_1<-1 && $t_per>2){

			$t_detail = $dataArray[0];
			$y_detail = $dataArray[1];

			$y_close = $y_detail['close'];
			$y_open = $y_detail['open'];
			$y_high = $y_detail['high'];
			$y_low = $y_detail['low'];

			$t_close = $t_detail['close'];
			$t_open = $t_detail['open'];

			if ($t_close>$y_open && $t_open<$y_close) {
				$reasonStr = "形成多头吞噬形态";
				$score = 25;

				if ($t_open<$y_low) {
					$score += 5;

				}

				if ($t_close>$y_high){
					$score += 10;
					$reasonStr .= ",吞噬了下影线";
				}

				if ($score == 40) {
					$reasonStr .= ",并呈现吞噬所有影线的完美状态";
				}

				return array(true,$score,$reasonStr);
			}
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入4天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：多方炮 1.前天收阳，涨幅大于2%；昨天收阴，振幅大于3%；今天收阳，涨幅大于2%；2.中间阴线的实体要在两个阳线实体之间；
 * Status:Test Good
 */
function red_gun($dataArray){
	if (count($dataArray) < 4) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 0, 2);
	$t_per = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	if ($t_per>2 && $y_per_2>2) {
		$t_detail = $dataArray[0];
		$y_detail_1 = $dataArray[1];
		$y_detail_2 = $dataArray[2];

		$t_close = $t_detail['close'];
		$t_open = $t_detail['open'];
		$t_high = $t_detail['high'];
		$t_low = $t_detail['low'];

		$y_close_1 = $y_detail_1['close'];
		$y_open_1 = $y_detail_1['open'];
		$y_high_1 = $y_detail_1['high'];
		$y_low_1 = $y_detail_1['low'];

		$y_close_2 = $y_detail_2['close'];
		$y_open_2 = $y_detail_2['open'];
		$y_high_2 = $y_detail_2['high'];
		$y_low_2 = $y_detail_2['low'];

		if ($y_close_1>$y_open_2 && $y_close_1>$t_open && $y_open_1>$y_open_2 && $y_open_1>$t_open &&
			$y_open_1<$y_close_2 && $y_open_1<$t_close && $y_close_1<$y_close_2 && $y_close_1<$t_close) {
			$reasonStr = "形成多方炮";
			$score = 15;

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入6天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：放量 1.当天收阳，且大于2% 2.当天的量为昨天的2-5倍 3.当天的量为前5天平均量的2-5倍
 * Status:Test Good
*/
function volume_increase($dataArray){
	if (count($dataArray) < 6) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 0, 2);
	$t_per = cal_percentage($tempArray);

	if ($t_per>2) {
		$t_volume = $dataArray[0]['volume'];
		$y_volume_1 = $dataArray[1]['volume'];
		$y_volume_2 = $dataArray[2]['volume'];
		$y_volume_3 = $dataArray[3]['volume'];
		$y_volume_4 = $dataArray[4]['volume'];
		$y_volume_5 = $dataArray[5]['volume'];

		$y_average = ($y_volume_1+$y_volume_2+$y_volume_3+$y_volume_4+$y_volume_5)/5;

		$y1_factor = round($t_volume/$y_volume_1,2);
		$y5_factor = round($t_volume/$y_average,2);

		if ($y1_factor >= 2 && $y1_factor <= 5) {
			$reasonStr = "今日阳线放量，为昨天的".$y1_factor."倍";
			$score = 10;

			if ($y5_factor >= 2 && $y5_factor <= 5) {
				$reasonStr .= "且为前5日平均量的".$y5_factor."倍";
				$score = 10;
			}

			return array(true,$score,$reasonStr);
		}
	}

	return array(false,0,'');
}

/*
 * Params:传入30天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：一阳穿N线 1.当天的5日线和10日线走平（0.5%） 2.拉出一根大阳线穿过5日、10日、20日甚至30日线
 * Status:Test Good
*/
function red_cross_average_line($dataArray){
	if (count($dataArray) < 30) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 0, 2);
	$t_per = cal_percentage($tempArray);

	if ($t_per>3.5) {
		$tempArray = array_slice($dataArray, 0, 5);
		$ma_5 = MA($tempArray);
		$tempArray = array_slice($dataArray, 0, 10);
		$ma_10 = MA($tempArray);
		$tempArray = array_slice($dataArray, 0, 20);
		$ma_20 = MA($tempArray);
		$tempArray = array_slice($dataArray, 0, 30);
		$ma_30 = MA($tempArray);

		$t_close = $dataArray[0]['close'];
		$t_open = $dataArray[0]['open'];

		$y_close = $dataArray[1]['close'];

		if ($t_close > $ma_5 && $t_close > $ma_10 && $t_close > $ma_20 && $t_open < $ma_5 && $t_open < $ma_10 && $t_open < $ma_20) {
			// 一阳穿N线
			$reasonStr = "今日收出大阳线，一阳穿破5日、10日和20日均线";
			$score = 25;

			if ($t_close > $ma_30 && $t_open < $ma_30) {
				$reasonStr .= ",并且突破了30日均线";
				$score += 10;
			}

			return array(true,$score,$reasonStr);
		}elseif ($t_close > $ma_5 && $t_close > $ma_10 && $t_close > $ma_20 && $y_close < $ma_5 && $y_close < $ma_10 && $y_close < $ma_20){
			// 跳空高开
			$reasonStr = "今日收出跳空高开大阳线，一阳穿破5日、10日和20日均线";
			$score = 30;

			if ($t_close > $ma_30 && $t_open < $ma_30) {
				$reasonStr .= ",并且突破了30日均线";
				$score += 10;
			}

			return array(true,$score,$reasonStr);
		}
	}
	return array(false,0,'');
}

/*
 * Params:传入7天的全数据数组
 * Return:数组{'true or false','分析','推荐权重'}
 * Description：1.大阴线，十字星，小阳或中阳
 * Status:TODO--未测试
 */
function green_star_red($dataArray){
	if (count($dataArray) < 7) {
		return array(false,0,'');
	}

	$tempArray = array_slice($dataArray, 1, 2);
	$y_per_1 = cal_percentage($tempArray);//计算昨天涨跌幅

	$tempArray = array_slice($dataArray, 2, 2);
	$y_per_2 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 3, 2);
	$y_per_3 = cal_percentage($tempArray);

	$tempArray = array_slice($dataArray, 4, 2);
	$y_per_4 = cal_percentage($tempArray);

	if (($y_per_2+$y_per_3+$y_per_4)<-8 || ($y_per_4+$y_per_2+$y_per_3) < -8 || ($y_per_1+$y_per_2)<-8) {

		$t_detail = $dataArray[0];
		$y_detail = $dataArray[1];

		if (is_star($y_detail) && cal_percentage($dataArray) > 1.5) {
			$reasonStr = "形成买点信号";
			$score = 10;

			if (cal_percentage($dataArray) > 3) {
				$score += 5;
			}

			return array(true,$score,$reasonStr);

		}
	}

	return array(false,0,'');
}

/*	============================================================================================
 *
* 	两点判别 (今天与昨天， 今天与平均值等）
*
* 	--------------------------------------------------------------------------------------------
*/

// 阴转阳 xg:ref(c,1)<ref(o,1) and c>o;
function green_to_red($dataArray){
	$t_open = $dataArray[0]['open'];
	$t_close = $dataArray[0]['close'];
	$y_open = $dataArray[1]['open'];
	$y_close = $dataArray[1]['close'];

	if ($y_close<$y_open && $t_close>$t_open) {
		return true;
	}else{
		return false;
	}
}

// 股价上穿N日均线，N为传入数组的长度 xg:cross(c,ma(c,30));
function up_cross($dataArray){
	$t_close = $dataArray[0]['close'];
	$y_close = $dataArray[1]['close'];
	$ma = MA($dataArray);

	if ($y_close<$ma && $t_close>$ma) {
		return true;
	}else{
		return false;
	}
}

// 股价下穿N日均线
function down_cross($dataArray){
	$t_close = $dataArray[0]['close'];
	$y_close = $dataArray[1]['close'];
	$ma = MA($dataArray);

	if ($y_close>$ma && $t_close<$ma) {
		return true;
	}else{
		return false;
	}
}


// 4，前天和今天为阴线，并且两笔阴线最低点相等
// xg:ref(c,2)<ref(o,2) AND c<o AND ref(l,2)=l;

// 5，最低价小于昨天的最低价但收盘后为阳线。
// xg:l<ref(l,1) and c>o;

// 6, 30日均线走平或走高的个股
// xg:ma(c,30)>ref(ma(c,30),1);


/*
 *
 *基本教程：
----------------------------------------
1. 概念
收盘价：c,close
开盘价: o,open
最高价: h,high
最低价 l,low
成交量: v，volume
股本: capital(手数）
换手率: (vol/capital)
个股分时图上都有一条黄线,叫做均价线： a/capital*100;
量比的含义： v/ref(v,1)    // 当前成交量与前一天成交量之比
股价上涨：c>ref(c,1)
涨幅：(c-ref(c,1))/c*100;
----------------------------------------
2. 函数
n日内最高的x：hhv(x,n)
n日内最低的x：llv(x,n)
n日内平均线值: ma(x,n)
向前引用:     ref(x,n)
A线，B线交叉: cross(A,B)
计算满足条件的天数：count(cond,n)
求n日内的和：sum(value,n)

举例:
    10天换手率之和: sum(v/capital,10)
    20天内涨停的天数: count((c/ref(c,1)>1.09, 20)
    cross(c,ma(c,5)), 收盘价上穿5日均线
    cross(ma(c,5),c)，5日均线下穿股价
----------------------------------------
甲: 当前形态
1. 收阴线
    c<o
2. 收阳线
    c>o
3. 上影线
    c<h
4. 下影线
    c>l
5. 下影线长于上影线
    xg:(c-l)>(h-c);
6. 流通盘小于4000万 的选股公式
    xg:CAPITAL/100<4000;
7, K线收十字星
    abs(c-o)/o<0.01;
8. 大福震荡选股
    xg:h/ref(c,1)>1.09 and ref(c,1)/l>1.09;
9. 大部分股票跌到43日均线时就会反弹，如果在43日均线附近买入，短线成功率占90%以上，即时选出还差几分钱就跌到43日均线的股票。
    xg:abs(c-ma(c,43))/ma(c,43)<0.01;
---------------------------------------
乙：  两点判别 (今天与昨天， 今天与平均值等）

1，量比大于1.1 同时换手大于3,涨幅>7个点的公式
    xg:v/ref(v,1)>1.1 and v/CAPITAL*100>3 and (c/ref(c,1) > 1.07)

2，"阴转阳"的公式(也就是昨天收阴线,今天收阳线)
    xg:ref(c,1)<ref(o,1) and c>o;
3，上穿30日均线的公式
    xg:cross(c,ma(c,30));
4，前天和今天为阴线，并且两笔阴线最低点相等
    xg:ref(c,2)<ref(o,2) AND c<o AND ref(l,2)=l;

5，最低价小于昨天的最低价但收盘后为阳线。
    xg:l<ref(l,1) and c>o;

6, 30日均线走平或走高的个股
    xg:ma(c,30)>ref(ma(c,30),1);

7，选周线跳空缺口的个股。
    周期选周线
    xg:HIGH<REF(LOW,1)  ;跳空
    xg:LOW>REF(HIGH,1)  ;跳高

8，前天到今天两天内,累计下跌等于或超过20%选股.
    xg:(ref(c,2)-c)/c>0.2;
----------------------------------------
丙：n 天以来的走势（最高值，最低值，平均值等）
1. 30个交易日内,股价跌幅达到一半的股票
    xg:(hhv(c,30)-c)/hhv(c,30)>0.5;

2，今天的量 是5天平均的 2倍以上
    xg:v>ma(v,5)*2;
    缩量
    xg:vol/ma(vol,10)<0.3;

3，股价是25天以来新低.
    xg:c<ref(llv(c,25),1);

4，股价回抽20日均线选股公式
    xg:ref(c,1)>ma(c,20) and cross(ma(c,20),c);

5，一根大阳线,至少有5个点以上,再连着出现二根振幅不大于5的十字星或是小K 线
    xg:(ref(c,3)-ref(c,4))/ref(c,3)>0.05 and count((h-l)/l<0.05,2)=2;

6，一阳上穿10，20，30日线，量能是前一天3倍。
    xg:cross(c,ma(c,10)) and cross(c,ma(c,20)) and cross(c,ma(c,30)) and v>ref(v,1)*3;

7，三线包容
    共三根K线，
    第一根K线为阴线；
    第二根K线为阴线，最高价低于第一根K线 ，最低价高于第一根K线；
        第三根K线阴阳不限，最高价低于第二根K线，最低价高于第二根K线。满足后发出信号
    w1:=ref(c,2)<ref(o,2);
    w2:=ref(c,1)<ref(o,1) and ref(h,2)>ref(h,1) and ref(l,2)<ref(l,1);
    w3:=ref(h,1)>h and ref(l,1)<l;
    xg:w1 and w2 and w3;

8，选出10天内出现了涨停的个股
    xg:count(c/ref(c,1)>1.09,10)>0;

9， 连续5天，当日的5日均线减前一天的5日均线差值小于1.
    xg:count((ma(c,5)-ref(ma(c,5),1))<1,5)=5;

10，5日均线向上 流通盘小于5000万 换手率大于3的选股公式
    var1:CAPITAL/100<5000;
    var2:=VOL/CAPITAL<3%;
    var3:ma(c,5)>ref(ma(c,5),1);
    xg:var1 and var2 and var3;

11，30日内到本日收盘价下跌20%的公式
    xg:hhv(c,30)/c>1.2;

12，连续4天的收盘价格波动幅度在3%之内连续走平或向上
    xg:count(abs(c-ref(c,1)/ref(c,1))<0.03 and c>ref(c,1),3)=3;

13，换手率10天80%以上的选股公式
    xg:sum(VOL/CAPITAL*100,10)>80;

14，60个交易日内，涨幅大于9.9%（涨停）的天数
    count(c/ref(c,1)>1.099,60);

15，昨天跌幅大于3%，今天开盘高开2%以上的选股公式。
    xg:ref(c,1)/ref(c,2)<0.97 and o/ref(c,1)>1.02;

16，选出周换手率小于10%的股票
    sum(VOL/CAPITAL*100,5)<10;

17，成交量创20日新低，并且股价也创20日新低
    xg:v=llv(v,20) and c=llv(c,20);

18，５日不涨,绿线收盘选票指标
    count(c<o,5)=5;
----------------------------------------

使用指标公式
1，选股：ＥＸＰＭＡ　５日与１０日金叉　并且第二天的收盘价在ＥＸＰＭＡ的 ５日线以上．
    cross(ref(ema(c,5),1),ref(ema(c,10),1)) and c>ema(c,5);

2，请编买入公式：
    1. 将BIAS指标的参数设置为24日，将KD指标的参数设置为9；3；3。
    2. BIAS指标要小于-6，这只是确认该股超跌的初选条件。
    3. KD指标产生黄金交叉，K线上穿D线。
    4. KD交叉同时，KD指标中的D值要小于16。
    BIAS3 :=(CLOSE-MA(CLOSE,24))/MA(CLOSE,24)*100,colorff00ff;
    RSV:=(CLOSE-LLV(LOW,9))/(HHV(HIGH,9)-LLV(LOW,9))*100;
    K:=SMA(RSV,3,1),COLORWHITE;
    D:=SMA(K,3,1),COLORYELLOW;
    xg:BIAS3<-6 and cross(k,d) and d<16;


3，kdj的J从上向下穿越O轴发出信号
    RSV:=(CLOSE-LLV(LOW,9))/(HHV(HIGH,9)-LLV(LOW,9))*100;
    K:=SMA(RSV,3,1),COLORWHITE;
    D:=SMA(K,3,1),COLORYELLOW;
    J:=3*K-2*D,COLORFF00FF;
    xg:cross(0,j);

4，"今日MACD等于昨天的MACD ,且macd>0" 的指标
    DIFF:=EMA(CLOSE,12)-EMA(CLOSE,26);
    DEA:=EMA(DIFF,9);
    MACD:=2*(DIFF-DEA);
    xg:macd=ref(macd,1) and macd>0

----------------------------------------
1，连续２天收盘价跌破３０日均线，就显示＂卖出＂信号
    a1:count(c<ma(c,30),2)=2;
    drawtext(a1,c,卖出),colorgreen;
2，股票在这条均线上在8天以上，上下幅度不大，
    a1:abs((c-ma(c,120))/ma(c,120))<0.1;xg:count(a1,8)=8;
3， 连续三天高开高走的选股公式        // 要看他的历史位置及幅度
    count(o>ref(c,1) and c>o,3)=3;
4，n天内曾经有m个涨停
    count(c/ref(c,1)>1.09,n)>m;
5，股价比昨天的高，量比昨天小。连续 n 天, 价升量减
    count(c>ref(c,1) and v<ref(v,1), n) = n
6，连续3天每天的最低价都比前一天高
    count(l>ref(c,1),3)=3;
7，股价超过五日均价的15%以上，并给出卖出信号卖出:
    c/ma(c,5)>1.15;
8，连收两个十字星的选股公式
    count(abs((c-o)/o<0.01),2)=2;
9，n横盘天数，n1横盘的上下幅度
    REF(((HHV(H,N)-LLV(L,N))/LLV(L,N)),1)<=(n1/100)and ref(v,1)<ref(ma(v,5),1) and cross(v,ma(v,5)) and cross(v,ma(v,10));
10，收盘价连续8天都站在5日均线的股票。
    count(c>ma(c,5),8)=8;
11，振幅大于5%，收盘价大于3.5小于15元，三日均线大于昨日的三日均线。
    a1:=h/l>1.05;
    a2:=c>3.5 and c<15;
    a3:=ma(c,3)>ref(ma(c,3),1);
    xg:a1 and a2 and a3;
12,收盘价下有60天前的远期获利盘n%。
    aa:WINNER(ref(c,60 ))*100;
13，连续三日收阳 后日收盘高于前日 且每日收于当日最高价附近 每日涨幅不大于4%选股:
    count(c>o and c>ref(c,1) and abs((h-c)/c)<0.01 and c/ref(c,1)<1.04,3)=3;

14，股价突破5ma买入，跌破5ma卖出。买入和卖出用箭头表示 （副图）
    a1:cross(c,ma(c,5));
    a2:cross(ma(c,5),c);
    drawtext(a1,low*0.98,↑),colorred;
    drawtext(a2,h*1.02,↓),color00ffff;
15，历史高点到前一高点画线
    POLYLINE(h>=hhv(c,0),h);
16，均线中增加一根线,这根线的价格是5日线的1.2倍
    aa:ma(c,5)*1.2;
17，N天前换手率大于8%
    REF(VOL/CAPITAL*100,N)>8;
18，一条均线18MA 股价上涨后“缩量”回调到18MA
    XG:COUNT(V<REF(V,1) AND C<O,N)=N AND C>MA(C,18);
    N “缩量”回调的天数
19，1。昨日量是n天以来最低量，（n可调）；2。今日量是昨日量的m倍，（m可 调）；3。今日k线收阳线；
    xg:ref(v,1)=llv(ref(v,1),n) and v>ref(v,1)*m and c>o;
20，第一天收阴，第二天，第三天，第四天，收出红三兵
    XG:REF(C,3)<REF(C,4) AND COUNT(C>O,3)=3;
21，最近3天(包括昨天.前天)股价突破250日均线COUNT(C>MA(C,250),3)=3;
22，60与120均线距离在n%内
    XG:ABS((MA(C,60)-MA(C,120))/MA(C,120))<N/100;
23，5日均线倾角大于60度
    X:(ATAN((MA(C,5)/REF(MA(C,5),1)-1)*100)*180/3.14115926)>60
    也可以这样：
    X:(ATAN((EMA(C,5)/REF(EMA(C,5),1)-1)*100)*180/3.14115926)>60;
25,K线沿着5日均线往上爬的选股公式
    w1:=ma(c,5);
    w2:=abc(c-w1)/w1<0.01;
    xg:count(w1>ref(w1,1),5)=5 and w2;
26,平台整理的公式,整理时间和整理幅度可调
    (HHV(CLOSE,N)-LLV(CLOSE,N))/LLV(CLOSE,N)<=(N1/100);N整理时间,N1整理幅度
27,选出当日最低价在10日均线上下0.05%内，收盘价在均线上方的个股
    abs(l-ma(c,10))<0.005 and c>ma(c,10);
28,第一天股票涨停，第二天成交量是第一天成交量的１倍,.那第三天就是买点
    w1:=ref(c,2)/ref(c,3)>1.1;
    w2:=ref(v,1)/ref(v,2)>2;
    xg:w1 and w2;
29,3日均线上穿10日均线，KDJ有效金叉
    RSV:=(CLOSE-LLV(LOW,9))/(HHV(HIGH,9)-LLV(LOW,9))*100;
    K:=SMA(RSV,3,1);
    D:=SMA(K,3,1);
    J:=3*K-2*D;
    w1:=cross(ma(c,3),ma(c,10));
    xg:w1 and cross(k,d);
30,连续3-5天,每天的收盘价涨跌幅不超过1%.
    w1:abs(c-o)<0.01;
    xg:count(w1,5)>=3;
31,MACD在8天以内两次金叉
    DIFF:=(EMA(CLOSE,12) - EMA(CLOSE,26));
    DEA:=EMA(DIFF,9),COLORBLUE,LINETHICK0;
    MACD:=2*(DIFF-DEA);
    w1:cross(DIFF,dea);
    xg:count(w1,8)>=2;
32,于20日均线相差2个百分点的
    w1:abs(c-ma(c,20)/ma(c,20)*100<2;
    N日均线相差M个百分点的公式h
    w1:abs(c-ma(c,n)/ma(c,n)*100<m;        //n：N日 m：M个百分点
33, 股价回调到14日均线的选股 abs(c-ma(c,14))/ma(c,14)<0.005;
34,MACD的拐点公式
    DIFF:=(EMA(CLOSE,12) - EMA(CLOSE,26));
    DEA:=EMA(DIFF,9),COLORBLUE,LINETHICK0;
    MACD:=2*(DIFF-DEA);
    拐点:ref(macd,2)>ref(macd,1) and ref(macd,1)<macd;
35,跳空高开收阳线LOW>REF(HIGH,1) and c>o;
36,涨停过300日线的选股公式c/ref(c,1)>1.09 and cross(c,ma(c,300));
37，成交量是前5天中10天均量线中最小值的3.5倍以上
    w1:=llv(ma(v,10),5);
    xg:v>w1*3.5;
38，跳空高开后，三天内没有回补缺口
    ref(LOW,2)>REF(HIGH,3) and l>ref(LOW,2);
39， {5日内第二大量}
    zdl:=hhv(v,5);
    v0:=IF(v>=zdl,0,v);
    v1:=IF(ref(v,1)>=zdl,0,ref(v,1));
    v2:=IF(ref(v,2)>=zdl,0,ref(v,2));
    v3:=IF(ref(v,3)>=zdl,0,ref(v,3));
    v4:=IF(ref(v,4)>=zdl,0,ref(v,4));
    第二大量:MAX(MAX(MAX(MAX(v0,v1),v2),v3),v4);
    DRAWTEXTABS( 0,10 ,第一大量 + zdl+ 第二大量+第二大量);同理可求出第三大 、第四大和最小量.
    实现简单的排列.这个公式好比是一件完整产品中的一个零部件,解决了一个思路问题.
40，{5日均线倾角大于60度}
    X:(ATAN((MA(C,5)/REF(MA(C,5),1)-1)*100)*180/3.14115926)>60;


累积能量线 OBV (On Balance Volume )
收集派发（Accumulation Distribution）
指数平均数（EXPMA）
MACD称为指数平滑异动平均线(Moving Average Convergence and Divergence)
DIFF线 收盘价短期、长期指数平滑移动平均线间的差  DEA线  DIFF线的M日指数平滑移动平均线  MACD线 DIFF线与DEA线的差，彩色柱状线  参数：SHORT(短期)、LONG(长期)、M 天数，一般为12、26、9
 *
 *
 *
 *
 */

/*
DIF线　（Difference）收盘价短期、长期指数平滑移动平均线间的差 　　
DEA线　（Difference Exponential Average）DIFF线的M日指数平滑移动平均线 　　
MACD线　DIFF线与DEA线的差，彩色柱状线 　　
参数：SHORT(短期)、LONG(长期)、M天数，一般为12、26、9 　　公式如下所示： 　　
　　加权平均指数（DI）=（当日最高指数+当日收盘指数+2倍的当日最低指数） 　　
　　十二日平滑系数（L12）=2/（12+1）=0.1538 　　
　　二十六日平滑系数（L26）=2/（26+1）=0.0741 　　
　　十二日指数平均值（12日EMA）=L12×当日收盘指数 + 11/（12+1）×昨日的12日EMA 　　
　　二十六日指数平均值（26日EMA）=L26×当日收盘指数 + 25/（26+1）×昨日的26日EMA

　　DIFF : EMA(CLOSE,SHORT) - EMA(CLOSE,LONG);
　　DEA : EMA(DIFF,M);
　　MACD : 2*(DIFF-DEA), COLORSTICK

	DIFF : EMA(CLOSE,12) - EMA(CLOSE,26);
	DEA  : EMA(DIFF,9);
	MACD : 2*(DIFF-DEA) ,COLORSTICK;
*/




function cal_EMA($dataArray){
	$count = count($dataArray);

	$sum = 0;
	$result = 0;

	for ($i = 1; $i <= $count; $i++){
		$sum += $i;
	}

	for ($i = 0, $j = $count; $i <$count; $i++, $j--){
		print_r($j."\n");
		print_r($dataArray[$i]['close']."\n");
		print_r($sum."\n");

		$result += ($j*$dataArray[$i]['close'])/$sum;
	}

	return $result;
}
