<?php
require_once('vendor/getid3/getid3.php');
require_once(dirname(__FILE__)."/../lib/utils.php");
require_once(dirname(__FILE__)."/../lib/dbhelper.php");
require_once(dirname(__FILE__)."/../config/config.php");

function main() {
	echo "webplayer cli scanner".PHP_EOL;
	$opts = getopt("f::d:");
	check_opts($opts);
	$dir = $opts['d'];
	$force = isset($opts['f']);
	check_dir($dir);
	echo "finding files ...";
	$files = _glob_recursive($dir."/*.mp3");
	echo "found ".count($files).PHP_EOL;
	if (!$force) {
		$files = filter_existing($files);
	}
	$infos = analyze($files);
	extract_covers($infos);
	$infos = sanitize_dirs($infos, $dir);
	insert_db($infos);
}
function get_hashes() {
	db();
	$sql = "select filename from mp3_items;";
	$res = query_all($sql);
	$res = array_map(function($it) {return md5($it['filename']);}, $res);
	return $res;
}
function filter_existing($files) {
	echo "filtering analyzed files ...".PHP_EOL;
	db();
	$res = array();
	$all_hashes = get_hashes();
	$i = 0;
	$total = count($files);
	foreach($files as $file) {
		if (ceil(1000*$i / $total) % 100 == 0) echo ceil(100*$i/$total)."%...";
		$hashed = str_replace(".jpg","",basename($file));
		$hashed = md5($hashed);
		if (!in_array($hashed, $all_hashes)) {
			$res[] = $file;
		}
		$i++;
	}
	return $res;
}
function sanitize_dirs($infos, $dir) {
	$res = array();
	$dir = rtrim($dir, '/');
	foreach($infos as $item) {
	    $item['filepath'] = str_replace($dir, '', $item['filepath']);
		$res[] = $item;	
	}
	return $res;
}
function extract_covers($infos) {
	echo "extracting cover art ...";
	$total = count($infos);
	$i = 0;
	foreach($infos as $info) {
		if (ceil(1000*$i / $total) % 100 == 0) echo ceil(100*$i/$total)."%...";
		$title = isset($info['comments_html']['title'][0]) ? $info['comments_html']['title'][0] : "unknown";
		$artist = isset($info['comments_html']['artist'][0]) ? $info['comments_html']['artist'][0] : "unknown";
		$album = isset($info['comments_html']['album'][0]) ? $info['comments_html']['album'][0] : "unknown";
		$covername = md5($album);
		if (!file_exists(ART_STORE."/".$covername.".jpg")) {
			//extract from file
			if (!empty($info['comments']['picture'])) {
				foreach($info['comments']['picture'] as $k=>$picture_array) {
					file_put_contents(ART_STORE."/".$covername.".jpg", $picture_array['data']);
				}
			} else {
			//get from google image search api
				$st = urlencode($title." ".$artist." ".$album);
				$json = file_get_contents("http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=$st");
				$arr = json_decode($json);
				$image_url = $arr->responseData->results[0]->url;
				$image_data = file_get_contents($image_url);
				file_put_contents(ART_STORE."/".$covername.".jpg", $image_data);
			}
		}
		$i++;
	}
	echo PHP_EOL;
}
function insert_db($infos) {
	echo "inserting into db...";
	db();
	$total = count($infos);
	$i = 0;
	foreach($infos as $info) {
		if (ceil(1000*$i / $total) % 100 == 0) echo ceil(100*$i/$total)."%...";
		$sql = "insert ignore into mp3_items";
		$sql.= " (id_mp3_items,title,artist,album,year,comment,track,genre,band,";
		$sql.= "composer,publisher,track_number,filepath,filename,filesize,samplerate,playtime_seconds,hash)";
		$sql.= " values (null, '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%d,%d,%d,'%s')";
		insert($sql, 
		   	isset($info['comments_html']['title'][0]) ? $info['comments_html']['title'][0] : "unknown",
			isset($info['comments_html']['artist'][0]) ? $info['comments_html']['artist'][0] : "unknown",
			isset($info['comments_html']['album'][0]) ? $info['comments_html']['album'][0] : "unknown",
		 	isset($info['comments_html']['year'][0]) ?    $info['comments_html']['year'][0] : "",
			isset($info['comments_html']['comment'][0]) ? $info['comments_html']['comment'][0] : "",
			isset($info['comments_html']['track'][0]) ?   $info['comments_html']['track'][0] : "",
			isset($info['comments_html']['genre'][0]) ?   $info['comments_html']['genre'][0] : "",
			isset($info['comments_html']['band'][0]) ?    $info['comments_html']['band'][0] : "",
			isset($info['comments_html']['composer'][0]) ?$info['comments_html']['composer'][0] : "",
			isset($info['comments_html']['publisher'][0]) ?$info['comments_html']['publisher'][0] : "",
			isset($info['comments_html']['track_number'][0]) ? $info['comments_html']['track_number'][0] : "" ,
			$info['filepath'],
			$info['filename'],
			$info['filesize'],
			$info['audio']['sample_rate'],
			$info['playtime_seconds'],
			md5(isset($info['comments_html']['album'][0]) ? $info['comments_html']['album'][0] : "unknown"));
			
		$i++;
	} 
	echo PHP_EOL;
}
function analyze($files) {
	echo "analyzing files ...";
	$getid3 = new getID3;
	$infos = array();
	$total = count($files);
	$i = 0;
	foreach($files as $file) {
		if (ceil(1000*$i / $total) % 100 == 0) echo ceil(100*$i/$total)."%...";
		$info = $getid3->analyze($file);
		getid3_lib::CopyTagsToComments($info);
		$infos[] = $info;
		$i++;
	}
	echo PHP_EOL;
	return $infos;
}
function check_opts($opts) {
	if (!isset($opts['d'])) {
		help();
		exit();
	}
}
function check_dir($dir) {
	if (!is_dir($dir)) {
		echo "That doesn't look like a directory - $dir".PHP_EOL;
		die();
	}
}
function help() {
	echo "Usage:".PHP_EOL;
	echo "-d\tDirectory to scan for mp3's".PHP_EOL;
	echo "-f\tForce scan all (don't skip already hashed files)".PHP_EOL;
}
main();
