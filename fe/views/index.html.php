<!DOCTYPE html>
<?php 
	require_once(dirname(__FILE__)."/../../config/config.php");
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Webplayer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<script type="text/javascript">
		WP = {};
		WP.STATIC_HOST = "<?php echo STATIC_HOST ?>";
		WP.STREAM_HOST = "<?php echo STREAM_HOST ?>";
		WP.PER_PAGE = <?php echo PER_PAGE ?>;
		WP.login_status = <?php echo isset($_SESSION['id_user']) ? "true" : "false"; ?>
	</script>
    <link href="<?php echo STATIC_HOST ?>/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="<?php echo STATIC_HOST?>/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?php echo STATIC_HOST?>/css/ui-lightness/jquery-ui-1.8.22.custom.css" rel="stylesheet">
    <link href="<?php echo STATIC_HOST?>/css/webplayer.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body style="padding-bottom: 60px;">
	<div id="navbar-container" class="container"></div>
	<div style="display: none;height: 220px; position: fixed; left:240px; top:39px; z-index: 9999; background-color: #222; width: 14px; padding: 10px;" id="slide-container">
		<div style="height:200px;" id="volume-slider"></div> 
	</div>
    <div class="container-fluid">
		<div class="row-fluid">
			<div class="span3 sidebar" id="playqueue-container" style="position: fixed"></div>
			<div class="span9 main-content" style="margin-left: 300px">
				<a href="javascript:;" id="randomize" style="float: right; margin-top:7px;">randomize</a>
				<ul class="nav nav-tabs">
					<li class="active" id="songs-tab-header"><a href="#!/songs/1">Songs</a></li>
					<li id="albums-tab-header"><a href="#!/albums/1">Albums</a></li>
					<li id="artists-tab-header"><a href="#!/artists/1">Artists</a></li>
					<li id="playlists-tab-header"><a href="#!/playlists/1">Playlists</a></li>
					<li id="search-tab-header"><a href="#search-modal" data-toggle="modal">Search</a></li>
				</ul>
				<div class="tab-content" style="overflow: visible">
					<div class="tab-pane active" id="songs-tab"></div>
					<div class="tab-pane" id="albums-tab" style="display:none"></div>
					<div class="tab-pane" id="searches-tab" style="display:none"></div>
					<div class="tab-pane" id="artists-tab" style="display:none"></div>
					<div class="tab-pane" id="playlists-tab" style="display:none"></div>
				</div>	
			</div>
		</div>
    </div> <!-- /container -->
	<div id="jplayer" style="height: 0px; width: 0px"></div>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
	<script src="js/jquery.jplayer.min.js"></script>
	<script src="js/jquery.keyz.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-ui-1.8.22.custom.min.js"></script>
    <script src="js/limonade.js"></script>
    <script src="js/webplayer.js"></script>
	<script type="text/javascript">
		L.default_route = "#!/songs/1";
		L.dispatch('/artists/:page', WP.artistlist_page);
		L.dispatch("/songs/:page", WP.songlist_page);
		L.dispatch('/albums/:page', WP.albumlist_page);
		L.dispatch('/playlists/:page', WP.playlist_page);
		L.dispatch('/search/:st/:page', WP.searchlist_page);
	</script>
	<?php require_once('navbar.html.php'); ?>
	<?php require_once('playqueue.html.php'); ?>
	<?php require_once('songlist.html.php'); ?>
	<?php require_once('searchlist.html.php'); ?>
	<?php require_once('albumlist.html.php'); ?>
	<?php require_once('artistlist.html.php'); ?>
	<?php require_once('playlist.html.php'); ?>
	<?php require_once('search-modal.html.php'); ?>
	<?php require_once('login-modal.html.php'); ?>
	<?php require_once('playlist-save-modal.html.php'); ?>
  </body>
</html>

