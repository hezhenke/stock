<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_corp_list.php
* @Description: 往公司列表中添加公司及代码 
* @Creation 2014-5-5 下午3:26:05 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once 'stock_fetch_util.php';

if (IS_DEBUG) {
	// ask for input
	fwrite(STDOUT, "Need to drop origin table (yes or no): ");

	// get input
	$needDrop = trim(fgets(STDIN));

	if (strtolower($needDrop) == 'yes') {
		set_corp_list_into_db_from_sina(true);
	}else {
		set_corp_list_into_db_from_sina(false);
	}

}
