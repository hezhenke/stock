<?php

define('ROOT_PATH' , dirname(__FILE__) . '/../../');
define('UPLOAD_FILE_PATH', 'ROOT_PATH'.'uploads/');
define('PIC_FILE_EXTERN', 'jpg');

define('IS_DEBUG', true);

//define('ROOT_PATH', preg_replace('/includes(.*)/i', '', str_replace('\\', '/', __FILE__)));

define('DB_CHARSET', 'utf-8');

define('API_UNAVAILABLE' , 1);      //服务不可用
define('API_TIME_OUT'    , 2);      //请求/执行超时
define('API_UNKNOWN_VER' , 3);      //版本丢失
define('API_NEED_UPDATE' , 4);      //API需要升级
define('API_PARAM_MISSING',5);      //缺少必要的参数

define('API_VERIFY_FAIL' , 6);      //身份验证失败
define('API_DATA_FAIL'   , 7);      //数据异常
define('API_DB_ERROR'    , 8);      //数据库执行失败
define('API_ERROR'       , 9);      //服务器导常
define('API_PERMISSIONS' , 10);      //用户权限不够
define('API_BAD_SIGN'    , 11);      //签名无效
define('API_BAD_PASS'    , 12);      //密码错误
define('API_IP_BLOCK'    , 13);      //
define('API_SIGN_ERR'    , 14);      //签名错误


$valid_api = array('login','getlocation','upload_location','push_test','focus_list');
?>