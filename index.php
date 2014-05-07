<?php
	require_once 'includes/db/ryan_mysql.php';

	$table_name = "test";
	$conn = new ryan_mysql();
	$sql = 'select * from '.$table_name;
	$result = $conn->getAll($sql);
	foreach ($result as $item){
		var_dump($item);
	}