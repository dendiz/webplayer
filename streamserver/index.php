<?php
require_once(dirname(__FILE__)."/vendor/limonade.php");
require_once(dirname(__FILE__)."/../lib/dbhelper.php");
require_once(dirname(__FILE__)."/../lib/utils.php");
require_once(dirname(__FILE__)."/../config/config.php");
dispatch('stream/test','stream_test');
function stream_test() {
	return json('test passed');
}
dispatch('stream/:token/:file', 'stream');
function stream($token, $file) {
	db();
	$sql = "select * from stream_access where token = '$token' and used = 0";
	$decoded_file = escapeshellcmd(urldecode($file));
	_log('file', $file, 'decoded', $decoded_file);
	$res = query_one($sql);
	if ($res and count($res) > 0) {
		$sql = "update stream_access set used=1 where token = '$token'";
		mysql_query($sql);
		return render_file(STREAM_STORE."/".$decoded_file);
	}
	return json(false);
}

run();
