<?php
require_once(dirname(__FILE__).'/vendor/limonade.php');
require_once(dirname(__FILE__).'/../lib/utils.php');
require_once(dirname(__FILE__).'/../lib/dbhelper.php');
require_once(dirname(__FILE__).'/../config/config.php');

function before($route = array()) {
	db();
}
dispatch("/","home");
function home() {
	return html("index.html.php");
}
dispatch("api/songs/:page", "songs");
function songs($page) {
	$sql = "";
	if ($page == "random") {
		$sql = "select * from mp3_items order by rand() limit ".PER_PAGE;
	} else {
		$start = PER_PAGE * ($page-1);
		$sql = "select * from mp3_items order by title limit $start,".PER_PAGE;	
	}
	$songs = query_all($sql);
	$sql = "select count(*) as total from mp3_items";
	$total = query_one($sql);
	_log(var_export($songs,true));
	return json(array("songlist"=>$songs, "total"=>$total['total']));
}
dispatch('api/album/:hash/songs', 'album_songs');
function album_songs($hash) {
	$sql = "select * from mp3_items where hash = '$hash'";
	$songs = query_all($sql);
	return json($songs);
}
dispatch("api/albums/:page", "albums");
function albums($page=1) {
	$start = PER_PAGE * ($page-1);
	$sql = "select album,artist,hash,count(*) as total,sum(playtime_seconds) as duration,sum(filesize) as size ";
	$sql .="from mp3_items group by album limit $start,".PER_PAGE;
	$albums = query_all($sql);
	$sql = "select count(distinct album) as total from mp3_items";
	$total = query_one($sql);
	return json(array("albumlist"=>$albums, "total"=>$total['total']));
}
dispatch('api/search/:st/:page', "search");
function search($st, $page) {
	$start = PER_PAGE * ($page - 1);
	$st = urldecode($st);
	$st = str_replace(' ','%',$st);
	$st = mysql_real_escape_string($st);
	$sql = "select * from mp3_items ";
	$sql .="where artist like '%$st%' or title like '%$st%' or album like '%$st%' limit $start,".PER_PAGE;
	$songs = query_all($sql);
	$sql = "select count(*) as total from mp3_items where artist like '%$st%' or title like '%$st%' or album like '%$st%'";
	$total = query_one($sql);	
	return json(array("searchlist"=>$songs, "total"=>$total['total']));
}
dispatch('api/song/:id', "song");
function song($id) {
	$sql = "select * from mp3_items where id_mp3_items = $id";
	$song = query_one($sql);
	return json($song);
}
dispatch('api/listened/:id', "listened");
function listened($id) {
	$sql = "update mp3_items set listen_count = listen_count + 1 where id_mp3_items = $id";
	mysql_query($sql);
}
dispatch('api/artist/:page','artist');
function artist($page) {
	$start = PER_PAGE * ($page - 1);
	$sql = "select sum(listen_count) as listens,sum(playtime_seconds) as playtime,group_concat(hash) as hashes,artist ";
	$sql .="from `mp3_items` group by artist limit $start,".PER_PAGE;
	$artists = query_all($sql);

	$sql = "select count(distinct artist) as total from mp3_items";
	$total = query_one($sql);

	return json(array('artists'=>$artists, 'total'=>$total['total']));
		
}
dispatch_post('api/register', 'register');
function register() {
	$email = _post('email');
	$pass  = _post('pass');
	$err = array();
	if (!$email) $err[] = "missing email";
	if (!$pass) $err[] = "missing password";
	if (count($err) > 0) return _error(0, implode("<br>",$err));
	insert("insert into users (email,password) values ('%s','%s')", $email, $pass);
	return json(true);
}
dispatch_post('/api/login', 'login');
function login() {
	$email = _post('email');
	$pass  = _post('pass');
	if (!$email or !$pass) return _error(0, "invalid login");
	$sql = "select * from users where email = '$email' and  password = '$pass'";
	$res = query_one($sql);
	if (!$res or count($res) == 0) return _error(0, "invalid login");
	$_SESSION['id_user'] = $res['id_users'];
	return json(true);
}

dispatch("/api/logout", "logout");
function logout() {
	unset($_SESSION['id_user']);
	return json(true);
}
dispatch('/api/ping', "ping");
function ping() {
	return json(isset($_SESSION['id_user']));
}
run();
