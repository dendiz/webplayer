<?php
function _human_time($timestamp) {
	$difference = time() - $timestamp;
	$periods = array("sec", "min", "hour", "day", "week",
	"month", "years", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");

	if ($difference > 0) { // this was in the past
	$ending = "ago";
	} else { // this was in the future
	$difference = -$difference;
	$ending = "to go";
	}
	for($j = 0; $difference >= $lengths[$j]; $j++)
	$difference /= $lengths[$j];
	$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	$text = "$difference $periods[$j] $ending";
	return $text;
}
function _slug($text) {
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    if (function_exists('iconv'))
    {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text))
    {
        return 'n-a';
    }
    return (strlen($text) > 50) ? substr($text, 0,50):$text;
}
function _ellipsis($text, $th) {
	if (strlen($text) > $th) return substr($text, 0, $th-3)."...";
	return $text;
}
function _rand_url() {
	return md5(time()).md5(rand());
}
function _post($key, $default = false) {
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}
function _get($key, $default = false) {
	return isset($_GET[$key]) ? $_GET[$key] : $default;
}
function _error($code, $msg) {
	return json((array("error_code" => $code, "user_message" => $msg)));
}

function _log() {
	$msg = implode(' ', func_get_args());
	$msg = date("H:i:s") . " - " . $msg . PHP_EOL;
	file_put_contents("/tmp/webplayer.log", $msg, FILE_APPEND);
}
function _glob_recursive($pattern, $flags = 0)
{
	$files = glob($pattern, $flags);
	
	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
	{
		$files = array_merge($files, _glob_recursive($dir.'/'.basename($pattern), $flags));
	}
	
	return $files;
}
function _guid() {
	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), 
		mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535),
		mt_rand(0, 65535), mt_rand(0, 65535));
}
