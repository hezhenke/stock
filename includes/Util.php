<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: Util.php
* @Description: (用一句话描述该文件做什么) 
* @Creation 2014-4-25 下午3:33:42 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

function has_dir_or_create($filePath){
	if (!is_dir($filePath)) {
		mkdir($filePath,0755);
		print_r("dir has created：".$filePath."\n");
	}
}