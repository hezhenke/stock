<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_analysis_model.php
* @Description: 分析模型
* @Creation 2014-4-25 下午2:45:59 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once '../db/ryan_mysql.php';
require_once 'stock_analysis_util.php';

$table_name = '002229';// 002229  300357
$conn = new ryan_mysql();

$sql = 'SELECT `close` FROM `'.$table_name.'` WHERE volume > 0 ORDER BY date DESC LIMIT 34';//倒数26天的交易记录

$result = $conn->getAll($sql);

if ($result) {
	// 组装近9天的原始数据
	$originArray_1_12 = array_slice($result, 0, 12);
	$originArray_1_26 = array_slice($result, 0, 26);
	$originArray_2_12 = array_slice($result, 1, 12);
	$originArray_2_26 = array_slice($result, 1, 26);
	$originArray_3_12 = array_slice($result, 2, 12);
	$originArray_3_26 = array_slice($result, 2, 26);
	$originArray_4_12 = array_slice($result, 3, 12);
	$originArray_4_26 = array_slice($result, 3, 26);
	$originArray_5_12 = array_slice($result, 4, 12);
	$originArray_5_26 = array_slice($result, 4, 26);
	$originArray_6_12 = array_slice($result, 5, 12);
	$originArray_6_26 = array_slice($result, 5, 26);
	$originArray_7_12 = array_slice($result, 6, 12);
	$originArray_7_26 = array_slice($result, 6, 26);
	$originArray_8_12 = array_slice($result, 7, 12);
	$originArray_8_26 = array_slice($result, 7, 26);
	$originArray_9_12 = array_slice($result, 8, 12);
	$originArray_9_26 = array_slice($result, 8, 26);

// 	$EMA_1_12 = cal_EMA($originArray_1_12);
// 	print_r($EMA_1_12."\n");
	$EMA_1_26 = cal_EMA($originArray_1_26);
	print_r($EMA_1_26."\n");
	
// 	$DIFF_1 = $EMA_1_12-$EMA_1_26;
// 	print_r($DIFF_1."\n");
}

$conn->close();
