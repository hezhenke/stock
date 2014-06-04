<?php
/**
*
* ===========================================
* @Author Ryan
* @Filename: stock_fetch_tape.php
* @Description: 抓取 上证、深成指、中小板指和创业板指数
* @Creation 2014-5-12 上午11:31:56
* @Modify
* @version V1.0
* -----------------------------------------------------------
*/

require_once 'stock_fetch_util.php';
require_once(dirname(__FILE__) . '/../Util.php');

set_tape_into_db();