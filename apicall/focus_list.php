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
* 接口参数：	act:0为查询，1为添加，2为更新，3为删除
* 		  	code:除了0不需要，其他都需要
* 			index:关注的顺序
* -----------------------------------------------------------
*/ 

if ('focus_list'==strtolower(trim($_REQUEST['m']))){
	$m = strtolower(trim($_SAFEREQUEST['m']));

	$corp_code = $_SAFEREQUEST['code'];
	$act = $_SAFEREQUEST['act'];

	$table_name = 'marathon_login';

	$conn = new ryan_mysql();
	$sql = 'select * from '.$table_name.' where invite_code="'.$invite_code.'"';
	$item = $conn->getRow($sql);


	if ($item) {
		$sql = 'update '. $table_name . ' SET '
				. ' device_token = "'.$device_token.'"'
						. ' where invite_code = "'.$invite_code.'"';

		$conn->query($sql);
		data_back($item,$m);
	}else {
		data_back(array(),$m);
	}

	$conn->close();
}