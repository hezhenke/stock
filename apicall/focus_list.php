<?php
/**
*
* ===========================================
* @Author Ryan
* @Filename: focus_list.php
* @Description: 获取关注列表
* @Creation 2014-5-19 下午6:23:02
* @Modify
* @version V1.0
*
* 接口参数：	act:0为初始化，1为添加，2为更新，3为删除
* 		  	code:除了0不需要，其他都需要
* 			index:关注的顺序
* -----------------------------------------------------------
*/

if ('focus_list'==strtolower(trim($_REQUEST['m']))){
	$m = strtolower(trim($_SAFEREQUEST['m']));

	$table_name = 'corp_codes';
	$conn = new ryan_mysql();

	$corp_code = $_SAFEREQUEST['code'];
	$act = $_SAFEREQUEST['act'];

	if (!isset($act)) {
		data_back(array(),$m,API_PARAM_MISSING);
	}

	if ($act == '1' || $act == '3'){
		if (!isset($corp_code)) {
			data_back(array(),$m,API_PARAM_MISSING);
		}
	}

	if ($act == '0') {
		$sql = 'select * from '.$table_name;
	}elseif ($act == '1'){
		$sql = "UPDATE corp_codes SET focus = '1' WHERE code = '".$corp_code."'";
	}elseif ($act == '2'){
		$sql = 'select * from '.$table_name;
	}elseif ($act == '3'){
		$sql = "UPDATE corp_codes SET focus = '0' WHERE code = '".$corp_code."'";
	}else {
		data_back(array(),$m,API_PARAM_MISSING);
	}

	$item = $conn->getAll($sql);

	if ($item) {

		data_back($item,$m);
	}else {
		data_back(array(),$m);
	}

	$conn->close();
}
