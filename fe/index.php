<?php
require_once(dirname(__FILE__).'/vendor/limonade.php');
require_once(dirname(__FILE__).'/../lib/utils.php');
require_once(dirname(__FILE__).'/../lib/dbhelper.php');
require_once(dirname(__FILE__).'/../config/config.php');

function before($route = array()) {
	db();
	if (@$route['options']['authenticate'])
		if (!isset($_SESSION['id_user'])) return _error(0, "required authentication");

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
dispatch('api/playlists', 'playlists', array('authenticate'=>true));
function playlists() {
	$user_id = $_SESSION['id_user'];

	$sql = "select pl.id_playlists, pl.name, sum(song.playtime_seconds) as duration, count(song.id_mp3_items) as total ,";
	$sql .="group_concat(song.hash) as hashes ";
	$sql .="from playlists pl, playlist_song ps, mp3_items song ";
	$sql .="where pl.id_playlists = ps.fk_id_playlist and song.id_mp3_items = ps.fk_id_mp3_item ";
	$sql .="and pl.fk_id_user = $user_id";

	_log('playlists sql', $sql);
	$res = query_all($sql);
	$total = count($res);
	return json(array("playlists"=>$res, "total"=>$total));
}
dispatch_post('api/playlist/save', "playlist_save", array('authenticate'=>true));
function playlist_save() {
	$name = _post('name');
	$ids = _post('songs');
	$user = $_SESSION['id_user'];
	if (!$name) return _error(0, "missing name for playlist");
	if (!$ids) return _error(0, "missing song list for playlist");
	_log('saving ids', $ids);
	insert("replace into playlists (fk_id_user, name) values (%d,'%s')", $user, $name);
	$id = mysql_insert_id();
	$ids = explode(",", $ids);
	foreach($ids as $song) {
		insert("insert into playlist_song (fk_id_playlist, fk_id_mp3_item) values (%d, %d)", $id, $song);
	}
	return json(true);
}
dispatch('/api/playlist/get/:id', "playlist_get");
function playlist_get($id) {
	$sql = "select song.* from mp3_items song, playlist_song ps ";
	$sql .="where song.id_mp3_items = ps.fk_id_mp3_item and ps.fk_id_playlist = $id";
	$res = query_all($sql);
	return json($res);
}
dispatch('api/token/get', 'token_get');
function token_get() {
	db();
	//TODO: number of token generation request per min should be max 10.
	$user_id = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;
	$token = _guid(); 
	$sql = "select count(*) as total from stream_access where ";
	$sql .="fk_id_user = $user_id and created_at BETWEEN DATE_SUB(NOW() , INTERVAL 10 MINUTE) and NOW();";
	$res = query_one($sql);
	$total = $res['total'];
	if ($total > 6) return json(false);
	$sql = "insert into stream_access (fk_id_user, token) values(%d, '%s')";
	insert($sql, $user_id, $token);
	return json($token);
}
run();
