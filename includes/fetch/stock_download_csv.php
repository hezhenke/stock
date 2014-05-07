<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_download_csv.php
* @Description: 下载各公司的csv 
* @Creation 2014-4-25 下午2:52:07 
* @Modify 
* ps: 	深市数据链接：http://table.finance.yahoo.com/table.csv?s=000001.sz 
* 		上市数据链接：http://table.finance.yahoo.com/table.csv?s=600000.ss
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

	down_csv($code);
}
