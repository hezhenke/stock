<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_handle_csv.php
* @Description: 从csv中导入数据进数据库
* @Creation 2014-4-22 下午4:24:12 
* @Modify 
* @version V1.0   
* 
* @How to use: 根据提示，输入公司6位数代码，或者corp_codes(公司列表)
* 
* -----------------------------------------------------------
*/ 

require_once 'stock_fetch_util.php';

if (IS_DEBUG) {
	// ask for input
	fwrite(STDOUT, "Enter stock code: ");
	
	// get input
	$code = trim(fgets(STDIN));
	
	var_dump("Code Num is : ".$code);
	
	set_corp_data_into_db($code);

}

?>