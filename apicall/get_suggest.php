<?php
/**
  *
  * ===========================================
  * @Author Ryan
  * @Filename: get_suggest.php
  * @Description: (用一句话描述该文件做什么)
  * @Creation 2014-6-10 下午7:09:17
  * @Modify
  * @version V1.0
  * -----------------------------------------------------------
*/

if ('get_suggest'==strtolower(trim($_REQUEST['m']))){
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