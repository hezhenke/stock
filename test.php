<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: test.php
* @Description: (用一句话描述该文件做什么) 
* @Creation 2014-4-24 下午4:29:32 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

$file_name = "test.log";
$log = "test";

$handle = @fopen($file_name, "a");
if (!file_exists($file_name)) {
	print_r("cannot read");
	fclose($handle);
	
	$handle = @fopen($file_name, "a");
}

if (is_writeable($file_name)) {
	fputs($handle, $log);
}

fclose($handle);

print_r(date('Y-m-d H:i:s',time())."\n");
