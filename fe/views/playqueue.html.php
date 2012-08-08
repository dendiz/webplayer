<script type="text/html" id="playqueue_tmpl">
<ul class="nav nav-list" style="overflow: auto;" id="playqueue-nav">
	<li class="nav-header"><span>Play Queue</span>
		<i style="float: right; cursor: pointer;" title="save as playlist" class="icon-file"></i>
		<i class="icon-trash empty-pq-btn" style="cursor:pointer; float: right" title="empty playqueue"></i>
	</li>
	<% if (playqueue.length == 0) { %>
		<li><p>The playqueue is empty</p></li>
	<% } %>
	<% for(var i=0,len = playqueue.length;i<len;i++) { %> 
	<% item = playqueue[i]; %>
	<% arturl = WP.STATIC_HOST + "/albumart/" + item.hash + ".jpg"; %>
	<% active = !!WP.current_song && WP.current_song.id_mp3_items == item.id_mp3_items ? '#f99810' : 'white'; %>
	<li class="pq-hoverable" style="padding: 2px; background-color: <%= active %>; margin: 0 0 2px;">
		<img style="float:left;margin: 0px 3px 0 0;border: 1px solid #ccc" src="<%= WP.art_url(item) %>" height="36" width="36">
		<div style="display: inline-block">
		<div title="<%= item.title %> - <%= item.artist %>"><strong><%= item.title.toString().ellipsis(25) %></strong></div>
		<div><%= item.artist.toString().ellipsis(25) %></div>	
		</div>
		<i class="icon-play play-pq-btn" song-id="<%= item.id_mp3_items %>" style="cursor: pointer;margin-top: 4px;float: right"></i>
		<i class="icon-trash remove-pq-btn" song-id="<%= item.id_mp3_items %>" style="cursor: pointer;margin-top: 4px;float: right"></i>
		<div style="clear:both"></div>
	</li>
	<% } %>
</ul>
</script>
