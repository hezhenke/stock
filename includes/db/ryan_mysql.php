<?php
/**
 * mysql 连接库
 * ============================================================================
 * $Author: 	Ryan
 * $Filename: 	ryan_mysql.php
 * $Creation：	2012-11-29
 * $Modify：
 *----------------------------------------------------------------------------
 */

require(dirname(__FILE__) . '/db-config.php');

class ryan_mysql {
	public $link_id;
	public $init_success;
	private $settings   = array();

	function __construct($db_host = DB_HOST, $db_user = DB_USERNAME, $db_password = DB_PASSWORD, $db_name = DB_NAME, $charset = 'utf-8') {
		$init_success = $this->connect($db_host, $db_user, $db_password, $db_name, $charset);
		$this->settings = array(
				'dbhost'   => $db_host,
				'dbuser'   => $db_user,
				'dbpw'     => $db_password,
				'dbname'   => $db_name,
				'charset'  => $charset,
		);
	}

	function connect($db_host, $db_user, $db_password, $db_name = '', $charset = 'utf-8'){
		if (PHP_VERSION >= '4.2') {
			$this->link_id = @mysql_connect ( $db_host, $db_user, $db_password, true );
		} else {
			$this->link_id = @mysql_connect ( $db_host, $db_user, $db_password );

			mt_srand ( ( double ) microtime () * 1000000 ); // 对 PHP 4.2 以下的版本进行随机数函数的初始化工作
		}

		if (! $this->link_id) {
			$this->ErrorMsg ( 'Can\'t connect mysql' );
			return false;
		}

		mysql_query ( "set names 'utf8'", $this->link_id );

		if ($db_name) {
			if ($this->select_database($db_name) === false) {
				echo $db_name;
				$this->ErrorMsg ( "Can't select MySQL database($db_name)!" );
				return false;
			} else {
				return true;
			}
		} else {
			$this->ErrorMsg ( 'PLZ configure db_name' );
			return false;
		}
	}

	function ErrorMsg($msg) {
		header ( 'Content-type: text/html; charset=utf-8' );
		echo $msg;
		echo "<br/>";
		echo '网站正在维护,请稍后......';
		exit ();
	}

	function __destruct() {

	}

	function close()
	{
		return mysql_close($this->link_id);
	}

	function select_database($dbname)
	{
		return mysql_select_db($dbname, $this->link_id);
	}

	function query($sql,$type='') {
		if (!$this->link_id) {
			$this->connect($this->settings['dbhost'], $this->settings['dbuser'], $this->settings['dbpw'], $this->settings['dbname'], $this->settings['charset']);
		}

		if (!($result = mysql_query($sql,$this->link_id))){
			$this->ErrorMsg('MySQL Query Error');
			return false;
		}

		return $result;
	}

	function getOne($sql,$limited=FALSE) {
		if ($limited) {
			$sql = trim($sql . " LIMIT 1 ");
		}
		$res = $this->query($sql);
		if ($res !== false) {
			$row = mysql_fetch_row($res);
			if ($row !== false) {
				return $row[0];
			}else{
				return '';
			}
		}else {
			return false;
		}
	}

	function getAll($sql)
	{
		$res = $this->query($sql);
		if ($res !== false)
		{
			$arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $arr[] = $row;
            }

            return $arr;
		}
		else
		{
			return false;
		}
	}

	function getRow($sql, $limited = false)
	{
		if ($limited == true)
		{
			$sql = trim($sql . ' LIMIT 1');
		}

		$res = $this->query($sql);
		if ($res !== false)
		{
			return mysql_fetch_assoc($res);
		}
		else
		{
			return false;
		}
	}
}

?>