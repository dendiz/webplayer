<?php
require_once('vendor/getid3/getid3.php');
require_once(dirname(__FILE__)."/../lib/utils.php");
require_once(dirname(__FILE__)."/../lib/dbhelper.php");
require_once(dirname(__FILE__)."/../config/config.php");

function main() {
	echo "webplayer album art fetcher".PHP_EOL;
	$files = get_art_files(ART_STORE);
	$all_hashes = get_hashes();
	$to_fetch = filter_existing($files, $all_hashes);
	fetch($to_fetch);
}
function sanitize_name($file) {
	return str_replace('.jpg','',basename($file));
}
function get_art_files($where) {
	$files = _glob_recursive($where."/*.jpg");
	$files = array_map('sanitize_name', $files);
	return $files;
}

function get_hash($it) {
	return $it['hash'];
}
function get_hashes() {
	db();
	$sql = "select distinct hash from mp3_items;";
	$res = query_all($sql);
	$res = array_map('get_hash', $res);
	return $res;
}

function filter_existing($files, $hashes) {
	$res = array_diff($hashes, $files);
	echo "album art already found:".count($files).PHP_EOL;
	echo "album count in db .....:".count($hashes).PHP_EOL;
	echo "still need to get ".count($res)." album art".PHP_EOL;
	return $res;

}

function fetch($hashes) {
	db();
	$i=0;
	foreach($hashes as $hash) {
		if ($i == 100) {
			echo "reached 100 api call limit. quitting...".PHP_EOL;
			die();
		}
		$sql = "select artist, title, album from mp3_items where hash='$hash'";
		$res = query_one($sql);
		$covername = md5($res['album']);
		$st = urlencode($res['title']." ".$res['artist']." ".$res['album']);
		echo "fetching album art for ".urldecode($st).PHP_EOL;
		$json = file_get_contents("http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=$st");
		$arr = json_decode($json);
		$image_url = $arr->responseData->results[0]->url;
		$image_url = str_replace('%25','%',$image_url);
		$image_data = file_get_contents($image_url);
		if ($image_data === false) {
			$try = 1;
			while($try < 10) {
				echo "image data was bad ($image_url), trying another image (attemp $try)".PHP_EOL;
				$image_url = $arr->responseData->results[$try]->url;
				echo "new attemp url $image_url".PHP_EOL;
				$image_data = file_get_contents($image_url);
				if ($image_data !== false) break;
				$try++;
			}
		}
		file_put_contents(ART_STORE."/".$covername.".jpg", $image_data);
		$i++;
	}

}
main();
