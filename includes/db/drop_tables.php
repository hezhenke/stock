<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: drop_tables.php
* @Description: 批量删除表 
* @Creation 2014-5-9 下午12:33:20 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once 'ryan_mysql.php';

$conn = new ryan_mysql();

$sql = 'SELECT * FROM corp_codes';

$result = $conn->getAll($sql);

if ($result) {
	foreach ($result as $item){
		$table_name = $item['code'];
		$sql = 'DROP TABLE IF EXISTS `'.$table_name.'`';
		$conn->query($sql);
	}
}

$conn->close();