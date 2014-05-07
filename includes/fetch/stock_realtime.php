<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_realtime.php
* @Description: 走sina数据接口，获得实时盘面情况 
* @Creation 2014-4-22 下午4:22:19 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once 'stock_fetch_util.php';

if (IS_DEBUG) {
	// ask for input
	fwrite(STDOUT, "Enter stock code: ");
	
	// get input
	$code = trim(fgets(STDIN));
	
	var_dump("Code Num is : ".$code);
	set_realtime_data_into_db($code);
}
