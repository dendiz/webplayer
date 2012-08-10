<?php
require_once(dirname(__FILE__).'/../lib/utils.php');
require_once(dirname(__FILE__).'/../config/config.php');
function db() {
	_log('db host', DB_HOST, 'db user', DB_USER, 'db pass', DB_PASS, 'db name',DB_NAME);
	mysql_connect(DB_HOST,DB_USER,DB_PASS) or die('cannot connect db');
	mysql_select_db(DB_NAME) or die('cannot select db: '.mysql_errno()." ".mysql_error());
}
function fetch_all($res) {
	$rows = Array();
	while( ($row = mysql_fetch_assoc($res)) != null) {
		$rows[] = $row;
	}
	return $rows;
}
function insert() {
	$args = func_get_args();
	$sql_tmp = array_shift($args);
	$args = array_map('mysql_real_escape_string', $args);
	_log('insert() args after shift:', var_export($args,true));
	$sql = vsprintf($sql_tmp, $args); 
	_log('sql statement -',$sql);
	$res = mysql_query($sql);
	_log('result of the statement was -', var_export($res,true));
	return $res;
}
function query($sql) {
	_log('sql - ', $sql);
	$res = mysql_query($sql);
	if ($res === false) _log('mysql error no: '.mysql_errno($res). ' message: '.mysql_error($res));
	return $res;
}
function query_one($sql) {
	$res = query($sql);
	return fetch_one($res);
}
function query_all($sql) {
	$res = query($sql);
	return fetch_all($res);
}
function fetch_one($res) {
	return fetch_row($res);
}
function fetch_row($res) {
	return mysql_fetch_assoc($res);
}
