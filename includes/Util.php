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

require_once 'db/constant.php';

function has_dir_or_create($filePath){
	if (!is_dir($filePath)) {
		mkdir($filePath,0755);
		print_r("dir has created：".$filePath."\n");
	}
}

function log_to_text($logText,$level='INFO'){
	$file_name = ROOT_PATH."/log/info.log";
	$log = '['.date('Y-m-d H:i:s',time()).']:['.$level.']: '.$logText."\n";
	
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
}

function api_err($err, $m='')
{
	data_back('', $m, 0, $err);
}

/**
 *  返回结果集
 *
 *  @param   mixed      $databack   返回的有效数据集
 *  @param   string     $result     请求成功或是失败的标识  0 = 失败 1 = 成功
 *  @param   string     $error_info 为空或是错误类型代号
 *
 */
function data_back($data, $m='', $result=1, $error_info='')
{
	#$data = iconv('GBK' , 'UTF-8' , $data);  //have to process for array input
	//performance_log($data, $m);

	$data_arr = array('data' => $data, 'm'=>$m, 'result' => $result, 'error_info' => $error_info);
	/* json方式 */
	//die(jsonEncode($data_arr));    //把生成的返回字符串打印出来
	die(json_encode($data_arr));
}
