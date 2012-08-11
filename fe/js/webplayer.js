WP.current_song = null;
WP.playqueue = []
WP.playlists = []
WP.songlist_total = 0;
WP.songlist_page = 1;
WP.human_filesize = function(size) {
    var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    var i = 0;
    while(size >= 1024) {
        size /= 1024;
        ++i;
    }
    return size.toFixed(1) + ' ' + units[i];
}
WP.human_duration = function(d) {
	//convert duration in seconds to human readlable format
	d = parseInt(d);
	var mins = Math.floor(d/60);
	if (mins < 10) mins = "0"+mins;
	var secs = d % 60;
	if (secs < 10) secs = "0" + secs;
	return mins+":"+secs;
}
WP.in_playqueue = function(item, playqueue) {
	var res = $.grep(playqueue, function(it) { return it.id_mp3_items == item.id_mp3_items });
	return res.length > 0;
}
WP.in_playlist = function(item, playlists) {
	var all = $.map(playlists, function(it) { return it.items });
	var res = $.grep(all, function(it)	{ return it.id_mp3_items == item.id_mp3_items });
	return res.length > 0;
}
WP.artistlist_page = function(page) {
	if (!page) page =1;
	WP.clear_listpage();
	WP.get_artist_list(page);
	$("#artists-tab-header").addClass('active');
	$("#artists-tab").show();
}
WP.playlist_page = function(page) {
	console.log('get playlist');
	if (!page) page = 1;
	WP.clear_listpage();
	WP.get_play_lists(page);
	$("#playlists-tab-header").addClass('active');
	$("#playlists-tab").show();
	$(".add-pq-btn").die().live('click', function() {WP.add_pq_playlist($(this).attr('playlist-id'))});
}
WP.songlist_page = function(page) {
	if (!page) page = 1;
	WP.clear_listpage();
	WP.get_song_list(page);
	$("#songs-tab-header").addClass('active');
	$("#songs-tab").show();
	//add to pq buttons
	$(".add-pq-btn").die().live('click', function() {WP.add_pq($(this).attr('song-id'))});
}
WP.albumlist_page = function(page) {
	if (!page) page = 1;
	WP.clear_listpage();
	WP.get_album_list(page);
	$("#albums-tab-header").addClass('active');
	$("#albums-tab").show();
	$(".add-pq-btn").die().live('click', function() {WP.add_pq_album($(this).attr('album-hash'))});
}
WP.searchlist_page = function(st, page) {
	if (!page) page = 1;
	WP.clear_listpage();
	WP.get_search_list(st, page);
	$("#search-tab-header").addClass('active');
	$("#searches-tab").show();
	$(".add-pq-btn").die().live('click', function() {WP.add_pq_search($(this).attr('song-id'))});
}
WP.clear_listpage = function() {
	$("ul.nav.nav-tabs li").removeClass('active');
	$(".tab-content .tab-pane").hide();
	$(".add-pq-btn").die('click');
}
WP.add_pq_playlist = function(playlist_id) {
	$.get('api/playlist/get/'+playlist_id, function(data) {
		$.each(data, function(i,it) {
			WP.playqueue.push(it);
		});	
		WP.render_playqueue(WP.playqueue);
	});
}
WP.add_pq_search = function(songid) {
	$.get('api/song/'+songid, function(data) {
		WP.playqueue.push(data);
		WP.render_playqueue(WP.playqueue);
	});
}
WP.add_pq = function(songid) {
	var song = $.grep(WP.songlist, function(it) {return it.id_mp3_items == songid}).car();
	WP.playqueue.push(song);
	WP.render_playqueue(WP.playqueue);
	WP.render_songlist(WP.songlist, WP.songlist_total, WP.current_song, WP.playqueue, WP.playlists, WP.songlist_page);
}
WP.add_pq_album = function(albumhash) {
	$.get('api/album/'+albumhash+'/songs', function(data) {
		$.each(data, function(i,it) {
			WP.playqueue.push(it);
		});	
		WP.render_playqueue(WP.playqueue);
	});
}
WP.get_play_lists = function(page) {
	$.get('api/playlists', function(data) {
		WP.render_playlists(data.playlists, data.total, page);
	});
}
WP.get_search_list = function(st, page) {
	$.get('api/search/'+st+'/'+page, function(data) {
		WP.render_searchlist(data.searchlist, data.total, WP.current_song, WP.playqueue, WP.playlists, st, page);	
	});
}
WP.get_artist_list = function(page) {
	$.get('api/artist/'+page, function(data) {
		WP.render_artistlist(data.artists, data.total, page);
	})
}
WP.get_album_list = function(page) {
	$.get('api/albums/'+page, function(data) {
		WP.render_albumlist(data.albumlist, data.total, page);
	});
}
WP.get_song_list = function(page) {
	$.get('api/songs/'+page, function(data) {
		WP.render_songlist(data.songlist, data.total, WP.current_song, WP.playqueue, WP.playlists, page);
		WP.songlist = data.songlist;
		WP.songlist_total = data.total;
		WP.songlist_page = page;
	});
}
WP.render_playlists = function(playlists, total, page) {
	L.html("#playlists-tab", "#playlists_tmpl", {
		playlists: playlists, total: total, page:page
	});
}
WP.render_artistlist = function(artists, total, page) {
	L.html("#artists-tab", "#artistlist_tmpl", {
		artistlist: artists, total: total, page:page
	});
}
WP.render_searchlist = function(list, total, current,playqueue, playlists, st, page) {
	L.html("#searches-tab", "#searchlist_tmpl", {
		searchlist: list, total:total, st:st, page:page, 
		current_song: current, playqueue:playqueue, playlists:playlists
	});
}
WP.render_songlist = function(songlist, total, current, pq, pls ,page) {
	L.html("#songs-tab","#songlist_tmpl", {songlist: songlist,
		total: total, current_song: current,playqueue: pq, playlists: pls, page: page});
}
WP.render_albumlist = function(albumlist, total, page) {
	L.html('#albums-tab', '#albumlist_tmpl', {albumlist: albumlist, total:total, page:page});
}
WP.render_playqueue = function(pq) {
	L.html("#playqueue-container","#playqueue_tmpl", {playqueue: pq});
	var viewport = $(window).height();
	$("#playqueue-nav").height(viewport-150);
}
WP.render_navbar = function(current) {
	L.html("#navbar-container","#navbar_tmpl", {item: current});
	$("#volume-slider").slider({
		orientation: "vertical",
		min:0,
		max:100,
		stop: function(ev, ui) {
			$("#jplayer").jPlayer("volume", ui.value/100);
		}
	});
	$("#progress-slider").slider({
		orientation: "horizontal",
		min: 0,
		max: 100,
		stop: function(event, ui) {
			console.log('sliding to', ui.value);
			WP.player_playhead(ui.value);
		}
		
	});
	$("#volume-btn").unbind('click').bind('click', function() {
		if ($(this).hasClass('active')) $(this).removeClass('active');
		else $(this).addClass('active');
		$("#slide-container").toggle();
	});
	$("#search").keyz({
		"enter": WP.search_submit	
	});
	$("#loader").ajaxStart(function(){
		$(this).css({"visibility":"visible"});
	}).ajaxStop(function() {
		$(this).css({"visibility":"hidden"});
	});
}
WP.search_submit = function() {
	var st = $("#search").val();
	console.log('search submit', st);
	window.location.href = "#!/search/"+encodeURIComponent(st)+"/1";
}
WP.remove_pq = function(songid) {
	WP.playqueue = $.grep(WP.playqueue, function(it) {return it.id_mp3_items != songid});
	WP.render_playqueue(WP.playqueue);
	WP.render_songlist(WP.songlist, WP.songlist_total, WP.current_song, WP.playqueue, WP.playlists);
}
WP.art_url = function(item) {
	if (!item) return WP.STATIC_HOST+"/img/speaker.png";
	return WP.STATIC_HOST + "/albumart/" + item.hash + ".jpg";
}
WP.empty_pq = function() {
	WP.playqueue = [];
	WP.render_playqueue(WP.playqueue);
	WP.render_songlist(WP.songlist, WP.songlist_total, WP.current_song, WP.playqueue, WP.playlists);
}
WP.stream_host = function(mp3_item) {
	var sh = WP.STREAM_HOST + "/" + encodeURIComponent(mp3_item.filepath.substring(1)) 
				+ "/" + encodeURIComponent(mp3_item.filename);
	console.log(sh);
	return sh;
}
WP.set_current_song = function() {
	$.get("api/token/get", function(data) {
		var tokenized = WP.STREAM_HOST+"/stream/"+data+"/"+encodeURIComponent(WP.current_song.filepath.substring(1))+"|"+
			encodeURIComponent(WP.current_song.filename);
		console.log("stream url", tokenized);
		$("#jplayer").jPlayer("setMedia", {mp3: tokenized});
		WP.toggle_play();
	});
}
WP.player_pause = function() {
	$('#play-btn').removeClass('playing');
	$("#play-btn").addClass('paused');

	$("#jplayer").jPlayer('pause');
	$("#play-btn").find('img').attr('src',WP.STATIC_HOST + "/img/glyphicons_173_play.png");
}
WP.player_playhead = function(val) {
	$("#jplayer").jPlayer("playHead", val);
}
WP.player_play = function() {
	if (!WP.current_song && WP.playqueue.length == 0) {
		console.error('load a file to the playqueue first');
		return;
	}
	if ($("#play-btn").hasClass('playing')) {
		WP.player_pause();
		return;
	}
	if (!$("#play-btn").hasClass('paused')) {
		WP.current_song = WP.playqueue[0];
		WP.render_navbar(WP.current_song);
		WP.set_current_song();
	} else WP.toggle_play();
}
WP.player_next = function() {
	var idx = WP.index_pq(WP.current_song);
	if (idx == WP.playqueue.length - 1) return;
	idx++;
	WP.current_song = WP.playqueue[idx];
	WP.render_navbar(WP.current_song);
	WP.render_playqueue(WP.playqueue);
	WP.set_current_song();
}
WP.player_prev = function() {
	var idx = WP.index_pq(WP.current_song);
	if (idx == 0) return;
	idx--;
	WP.current_song = WP.playqueue[idx];
	WP.render_navbar(WP.current_song);
	WP.render_playqueue(WP.playqueue);
	WP.set_current_song();
}
WP.toggle_play = function() {
	$("#jplayer").jPlayer("play");
	$("#play-btn").find('img').attr('src',WP.STATIC_HOST + "/img/glyphicons_174_pause.png");
	$("#play-btn").addClass('playing');
	$('#play-btn').removeClass('paused');
}
WP.play_pq = function(songid) {
	var idx = WP.index_pq({id_mp3_items: songid}); //send a pseudo object
	WP.current_song = WP.playqueue[idx];
	WP.render_navbar(WP.current_song);
	WP.render_playqueue(WP.playqueue);
	WP.set_current_song();
}
WP.player_timeupdate = function(player) {
	$("#timeupdate").html(WP.human_duration(player.status.currentTime));
	$("#progress-slider").slider("value",player.status.currentPercentAbsolute); 
	if (player.status.currentTime > 10 && !WP.current_song.listened ) {
		WP.current_song.listened = true;
		$.get('api/listened/'+WP.current_song.id_mp3_items, function() {
			WP.current_song.listen_count++;
		});
	}
}
WP.unique = function(arrVal) {
	var uniqueArr = [];
	for (var i = arrVal.length; i--; ) {
		var val = arrVal[i];
		if ($.inArray(val, uniqueArr) === -1) {
			uniqueArr.unshift(val);
		}
	}
	return uniqueArr;
}
WP.index_pq = function(item) {
	//return the index of the item in the playqueue
	for(var i=0,len = WP.playqueue.length;i<len;i++) {
		var pq_item = WP.playqueue[i];
		if (pq_item.id_mp3_items == item.id_mp3_items) return i;
	}
	return null;
}
WP.save_pq_modal = function() {
	//save pq as a pl
	if (!WP.login_status) {
		console.error('need auth to save pl');
		return;
	}
	if (WP.playqueue.length == 0) {
		console.error('no items in playqueue');
		return;
	}
	$("#playlist-save-modal").modal('show');
}
WP.save_pq = function() {
	var name = $("#playlist-name").val();
	var songs =$.map(WP.playqueue, function(it) { return it.id_mp3_items }); 
	console.log('save playlist', songs);
	$.post('/api/playlist/save', {
		name: name,
		songs: songs.join()
	}, function(data) {
		if (!!data.error_code) {
			console.error(data.error_message);
			return;
		}
	});
}
WP.randomize_list = function() {
	WP.get_song_list("random");
}
WP.bind_events = function() {
	//remove from pq buttons
	$(".remove-pq-btn").live('click', function() {WP.remove_pq($(this).attr('song-id'))});
	$(".play-pq-btn").live('click', function() { WP.play_pq($(this).attr('song-id'));})
	//empty pq
	$(".empty-pq-btn").live('click', WP.empty_pq);
	//save pq
	$(".save-pq-btn").live('click', WP.save_pq_modal);
	//player buttons
	$("#play-btn").live('click', WP.player_play);
	$("#next-btn").live('click', WP.player_next);
	$("#prev-btn").live('click', WP.player_prev);
	//randomize button
	$("#randomize").click(WP.randomize_list);
	$("#modal-search-query").keyz({
		"enter":function() {
			$("#search-modal").modal('hide');
			window.location.href='#!/search/'+$('#modal-search-query').val()+'/1'}
	});
}
WP.register = function() {
	var email = $("#modal-login-email").val();
	var pass  = $("#modal-login-password").val();
	console.log('registering with', email, 'and', pass);
	$.post('api/register', {
		"email": email,
		"pass": pass
	}, function(data) {
		if (!!data.error_code) {
			console.error(data.error_message);
			return;
		}
		WP.login(email, pass);
	});
}
WP.login = function(email, pass) {
	if (!email) email = $("#modal-login-email").val();
	if (!pass) pass = $("#modal-login-password").val();
	console.log('login with', email, 'and', pass);
	$.post('api/login', {"email":email, "pass":pass}, function(data) {
		if (!!data.error_code) {
			console.error(data.error_message);
			return;
		}
		$("#login-modal").modal("hide");
		WP.login_status = true;
		WP.render_navbar(WP.current_song);
	});
}
WP.logout = function() {
	console.log('logout');
	$.get('/api/logout', function() {
		WP.render_navbar(WP.current_song);
		WP.login_status = false;
	});
}
WP.session_checker = function() {
	$.get('api/ping',function(data) {
		WP.login_status = data;
		WP.render_navbar(WP.current_song);
	})
}
$(function(){
	WP.render_navbar(WP.current_song);
	WP.render_playqueue(WP.playqueue);
	WP.bind_events();
	setInterval(WP.session_checker, 20000);
	$("#jplayer").jPlayer({
		volume: 0.5,
		ready: function(player) {
			var vol=$("#jplayer").data("jPlayer").options.volume;
			$("#volume-slider").slider("value", vol*100);
		},
		timeupdate: function(ev) {
			WP.player_timeupdate(ev.jPlayer);
		},
		ended: function(ev) {
			WP.player_next();
		}
	});
});
