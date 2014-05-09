<?php
/**   
* 
* ===========================================
* @Author Ryan
* @Filename: stock_fetch_Util.php
* @Description: 收集获取数据所用的function 
* @Creation 2014-5-5 下午4:14:45 
* @Modify 
* @version V1.0   
* -----------------------------------------------------------
*/ 

require_once '../db/ryan_mysql.php';
require_once '../db/constant.php';
require_once '../Util.php';

define('BASE_URI', 'http://table.finance.yahoo.com/table.csv?s=');

// http://table.finance.yahoo.com/table.csv?s=300357.sz 深证的url
// http://table.finance.yahoo.com/table.csv?a=0&b=1&c=2012&d=3&e=19&f=2012&s=600000.ss 上证的url

// 读取csv，导入公司每日具体数据进数据库
function set_corp_data_into_db($code){
	$file_folder = ROOT_PATH.'resource/';
	$file_path = '';
	$file_name = '';

	if (strlen($code) != 6) {
		die("stock code error \n");
	}

	if (substr($code, 0, 1) == "6") {
		$file_path = $file_folder.'sh/sh';
		print_r("shanghai stock \n");
	}else if(substr($code, 0, 1) == "0" || substr($code, 0, 1) == "3"){
		$file_path = $file_folder.'sz/sz';
		print_r("shenzhen stock \n");
	}else {
		die("stock code error \n");
	}

	$file_name = $file_path.$code.'.csv';

	if ($file = fopen($file_name, "r")) {
		print_r("open csv success \n");

		$conn = new ryan_mysql();
		// 创建表单

		$corp_code = $code;

		$sql = 'select name from corp_codes where code="'.$code.'"';
		$result = $conn->getOne($sql);

		if ($result) {
			$corp_name = $result;
		}else {
			return print_r("查无此上市公司，请检查公司代码\n");
		}

		$table_name = $corp_code;


		$sql = 'DROP TABLE IF EXISTS `'.$table_name.'`';
		$conn->query($sql);

		// create table if not exists table
		$sql = 'CREATE TABLE IF NOT EXISTS `'.$table_name.'` ('
				.'`date` date NOT NULL,'
						.'`open` decimal(10,2) NOT NULL DEFAULT 0.00,'
								.'`high` decimal(10,2) NOT NULL DEFAULT 0.00,'
										.'`low` decimal(10,2) NOT NULL DEFAULT 0.00,'
												.'`close` decimal(10,2) NOT NULL DEFAULT 0.00,'
														.'`volume` varchar(255) NOT NULL DEFAULT 0,'
																		.'PRIMARY KEY (`date`)'
																				.') ENGINE=MyISAM DEFAULT CHARSET=utf8';
		$conn->query($sql);

		// 获取数据
		// 公司交易数据
		// 0:date  1:open  2:high  3:low  4:close  5:volume  6:adj_close
		$dataArray = array();
		while ($data = fgetcsv($file)) {
			if ($data[0] != "Date") {
				array_push($dataArray, $data); // 除去csv中第一行的列说明
			}
		}

		krsort($dataArray); // 排倒序
		
		$isSuccess = true;
		
		if (count($dataArray) == 0) {
			log_to_text('corp code: '.$code.' csv down fail!');
		}
		
		foreach ($dataArray as $item){
			
			$date = $item[0];
			
			if (strlen($date) == 10 && (substr($date, 0, 1) == '1' || substr($date, 0, 1) == '2')) {
				$open = $item[1];
				$high = $item[2];
				$low = $item[3];
				$close = $item[4];
				$volume = $item[5];
				
				$sql = 'insert into `'.$table_name.'` set date="'.$date.'",open="'.$open
					.'",high="'.$high.'",low="'.$low.'",close="'.$close
					.'",volume="'.$volume
					.'" on duplicate key update open="'.$open.'",high="'.$high
					.'",low="'.$low.'",close="'.$close.'",volume="'.$volume.'"';
		
				if ($conn->query($sql)) {
					//print_r($date." insert success \n");
				}
				$isSuccess = true;
			}else {
				$isSuccess = false;
			}
		}

		fclose($file);
		$conn->close();
		
		return $isSuccess;
	}else {
		return false;
		die("can not open csv file, PLZ check!\n");
	}
}

// 下载雅虎上各公司的csv
function down_csv($code){
	if (substr($code, 0, 1) == "6") {
		$url = BASE_URI.$code.'.ss';
		$filePath = ROOT_PATH.'resource/sh/';
		has_dir_or_create($filePath);
		$file = ROOT_PATH.'resource/sh/sh'.$code.'.csv';
		print_r("shanghai stock \n");
	}else if(substr($code, 0, 1) == "0" || substr($code, 0, 1) == "3"){
		$url = BASE_URI.$code.'.sz';
		$filePath = ROOT_PATH.'resource/sz/';
		has_dir_or_create($filePath);
		$file = ROOT_PATH.'resource/sz/sz'.$code.'.csv';
		print_r("shenzhen stock \n");
	}else {
		die("error stock code\n");
	}

	//从
	$url = $url.'&a=0&b=1&c=2014';
	
	httpcopy($url,$file);
}

function httpcopy($url, $file="", $timeout=60) {
	$file = empty($file) ? pathinfo($url,PATHINFO_BASENAME) : $file;
	$dir = pathinfo($file,PATHINFO_DIRNAME);
	!is_dir($dir) && @mkdir($dir,0755,true);
	$url = str_replace(" ","%20",$url);

	if(function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$temp = curl_exec($ch);
		if(@file_put_contents($file, $temp) && !curl_error($ch)) {
			return $file;
		} else {
			return false;
		}
	} else {
		$opts = array(
				"http"=>array(
						"method"=>"GET",
						"header"=>"",
						"timeout"=>$timeout)
		);
		$context = stream_context_create($opts);
		if(@copy($url, $file, $context)) {
			//$http_response_header
			return $file;
		} else {
			return false;
		}
	}
}

// 把及时数据写入数据库
function set_realtime_data_into_db($code){
	$html = get_realtime_data_from_sina($code);

	// 数据处理
	$rawData = explode("\"",$html)[1];
	//print_r($rawData."\n");

	// 判断是否code输入有误
	if (empty($rawData)) {
		exit("代码输入有误，或已退市\n");
	}

	/*
	 0：”大秦铁路”，股票名字；
	1：”27.55″，今日开盘价；
	2：”27.25″，昨日收盘价；
	3：”26.91″，当前价格；
	4：”27.55″，今日最高价；
	5：”26.20″，今日最低价；
	6：”26.91″，竞买价，即“买一”报价；
	7：”26.92″，竞卖价，即“卖一”报价；
	8：”22114263″，成交的股票数，由于股票交易以一百股为基本单位，所以在使用时，通常把该值除以一百；
	9：”589824680″，成交金额，单位为“元”，为了一目了然，通常以“万元”为成交金额的单位，所以通常把该值除以一万；
	10：”4695″，“买一”申请4695股，即47手；
	11：”26.91″，“买一”报价；
	12：”57590″，“买二”
	13：”26.90″，“买二”
	14：”14700″，“买三”
	15：”26.89″，“买三”
	16：”14300″，“买四”
	17：”26.88″，“买四”
	18：”15100″，“买五”
	19：”26.87″，“买五”
	20：”3100″，“卖一”申报3100股，即31手；
	21：”26.92″，“卖一”报价
	(22, 23), (24, 25), (26,27), (28, 29)分别为“卖二”至“卖四的情况”
	30：”2008-01-11″，日期；
	31：”15:05:32″，时间；
	http://hq.sinajs.cn/list=sz002229
	*/

	$rawDataArray = explode(",", $rawData);

	$stock_name = trim($rawDataArray[0]);
	$open = $rawDataArray[1];
	$current = $rawDataArray[3];
	$high = $rawDataArray[4];
	$low = $rawDataArray[5];
	$volume = $rawDataArray[8];
	$date = $rawDataArray[30];

	$table_name = $code;
	$conn = new ryan_mysql();

	if ($volume == 0) {
		print_r("股票名：".$stock_name."\n今日停牌\n");
	}else {
		print_r("股票名：".$stock_name."\n股票代码：".$table_name."\n开盘价：".$open."\n当前价：".$current."\n最高价：".$high."\n最低价：".$low."\n成交量：".$volume."\n日期：".$date."\n");
	}
	
	// create table if not exists table
	$sql = 'CREATE TABLE IF NOT EXISTS `'.$table_name.'` ('
			.'`date` date NOT NULL,'
			.'`open` decimal(10,2) NOT NULL DEFAULT 0.00,'
					.'`high` decimal(10,2) NOT NULL DEFAULT 0.00,'
							.'`low` decimal(10,2) NOT NULL DEFAULT 0.00,'
									.'`close` decimal(10,2) NOT NULL DEFAULT 0.00,'
											.'`volume` varchar(255) NOT NULL DEFAULT 0,'
															.'PRIMARY KEY (`date`)'
																	.') ENGINE=MyISAM DEFAULT CHARSET=utf8';
	$conn->query($sql);

	$sql = 'insert into `'.$table_name.'` set date="'.$date.'",open="'.$open
	.'",high="'.$high.'",low="'.$low.'",close="'.$current.'",volume="'.$volume
	.'" on duplicate key update open="'.$open.'",high="'.$high
	.'",low="'.$low.'",close="'.$current.'",volume="'.$volume.'"';

	if ($conn->query($sql)) {
		print_r($date." insert success \n");
	}

	$conn->close();
}

function get_realtime_data_from_sina($code){
	$baseURI = "http://hq.sinajs.cn/list=";
	$requestURL = "";

	if (substr($code, 0, 1) == "6") {
		$requestURL = $baseURI."sh".$code;
		print_r("shanghai stock \n");
	}else if(substr($code, 0, 1) == "0" || substr($code, 0, 1) == "3"){
		$requestURL = $baseURI."sz".$code;
		print_r("shenzhen stock \n");
	}else {
		print_r("wrong code");
	}

	$html = file_get_contents($requestURL);

	$html = iconv("gb2312", "utf-8//IGNORE",$html);
	//print_r($html."\n");
	return $html;
}

// 通过读取sina接口，获取公司列表
function set_corp_list_into_db_from_sina($needDrop){
	$table_name = 'corp_codes';


	if ($needDrop) {
		$conn = new ryan_mysql();

		$sql = 'CREATE TABLE IF NOT EXISTS `'.$table_name.'` ('
				.'`code` char(10) NOT NULL,'
						.'`name` char(40) NOT NULL,'
								//		.'`id` int(11) NOT NULL AUTO_INCREMENT,'
		.'PRIMARY KEY (`code`)'
										.') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

		$conn->query($sql);
		$conn->close();
	}

	request_data_with_prefix('000');
	request_data_with_prefix('002');
	request_data_with_prefix('300');
	request_data_with_prefix('600');
	request_data_with_prefix('601');

}

function request_data_with_prefix($prefix){
	$conn = new ryan_mysql();
	$table_name = 'corp_codes';

	for ($i = 0; $i<=999 ; $i++){
		if ($i<10){
			$code = $prefix.'00'.$i;
		}else if ($i<100 && $i>=10) {
			$code = $prefix.'0'.$i;
		}else {
			$code = $prefix.$i;
		}

		$html = get_realtime_data_from_sina($code);

		// 数据处理
		$rawData = explode("\"",$html)[1];
		//print_r($rawData."\n");

		// 判断是否code输入有误
		if (empty($rawData)) {
			print_r($code.'不存在此公司');
		}else {
			$rawDataArray = explode(",", $rawData);
			
			$corp_code = $code;
			$corp_name = trim($rawDataArray[0]);
			$sql = 'insert into `'.$table_name.'` set code="'.$corp_code.'",name="'.$corp_name.'" on duplicate key update name="'.$corp_name.'"';
			if ($conn->query($sql)) {
				print_r($corp_name." insert success \n");
			}
		}
	}

	$conn->close();
}

// 读取csv，导入上市公司列表进数据库
function set_corp_list_into_db_from_csv($filename){
	$table_name = '';

	$file_path = ROOT_PATH.'resource/';
	$file_name = $file_path.$filename.'.csv';

	if (!file_exists($file_name)) {
		die("file do not exist \n");
	}

	if ($file = fopen($file_name, "r")) {
		//公司列表
		$table_name = $filename;
		$conn = new ryan_mysql();

		$sql = 'DROP TABLE IF EXISTS `'.$table_name.'`';
		$conn->query($sql);

		$sql = 'CREATE TABLE IF NOT EXISTS `'.$table_name.'` ('
				.'`code` char(10) NOT NULL,'
						.'`name` char(40) NOT NULL,'
								//		.'`id` int(11) NOT NULL AUTO_INCREMENT,'
		.'PRIMARY KEY (`code`)'
										.') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

		$conn->query($sql);

		while ($data = fgetcsv($file)) {
			$corp_code = $data[0];
			$corp_name = $data[1];
			$sql = 'insert into `'.$table_name.'` set code="'.$corp_code.'",name="'.$corp_name.'" on duplicate key update name="'.$corp_name.'"';
			if ($conn->query($sql)) {
				print_r($corp_name." insert success \n");
			}
		}

		fclose($file);
		$conn->close();
	}
}