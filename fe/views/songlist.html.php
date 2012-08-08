<script type="text/html" id="songlist_tmpl">
<table class="table table-striped table-condensed">
	<thead>
	<tr>
		<th>N</th>
		<th>Ctrl</th>
		<th>Artists</th>
		<th>Song</th>
		<th>Album</th>
		<th>Length</th>
		<th>Info</th>
	</tr>	
	</thead>
	<tbody>
	<% for (var i=0,len=songlist.length;i<len;i++) { %>
	<% item = songlist[i]; %>
	<% 
		inpq = WP.in_playqueue(item, playqueue) ? "opacity: 0.2" : "";
	%>
	<tr style="<%= inpq %>">
		<!-- <%= item.id_mp3_items %> -->
		<td><%= item.track %></td>
		<td style="width: 90px">
			<div class="btn-toolbar" style="margin: 0">
				<div class="btn-group">
				  <button class="btn btn-mini add-pq-btn" song-id="<%= item.id_mp3_items %>">PQ</button>
				  <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				  <ul class="dropdown-menu">
					<li><a href="#">Play now</a></li>
					<li><a href="#">Add to PL</a></li>
					<li class="divider"></li>
					<li><a href="#">Share</a></li>
				  </ul>
				</div>
			  </div>
		</td>
		<td><span title="<%= item.artist %>"><%= item.artist.toString().ellipsis(15) %></span></td>
		<td><span title="<%= item.title %>"><%= item.title.toString().ellipsis(35) %></span></td>
		<td><span title="<%= item.album %>"><%= item.album.toString().ellipsis(15) %></span></td>
		<td><%= WP.human_duration(item.playtime_seconds) %></td>
		<td style="width: 200px">
			<% if (parseInt(item.listen_count) > 0) { %>
			<span class="badge"><%= item.listen_count %></span>
			<% } %>
			<span class="label label-info"><%= WP.human_filesize(item.filesize) %></span>
			<% if (!!current_song && current_song.id_mp3_items == item.id_mp3_items) { %>
			<span class="label label-info">Playing</span>
			<% } %>
			<% if (WP.in_playqueue(item, playqueue)) { %>
			<span class="label">In PQ</span>
			<% } %>
			<% if (WP.in_playlist(item, playlists)) { %>
			<span class="label" title="In playlist my list 1">In PL</span>
			<% } %>
			<img src="<%= WP.art_url(item) %>" height="24" width="24" style="float: right">
		</td>	
	</tr>
	<% } %>
	</tbody>
</table>
<div class="pagination .pagination-centered">
	<ul>
	  <% total_page = Math.ceil(total/WP.PER_PAGE); %>
	  <% prev_disabled = (page == 1) ? "disabled" : ""; %>
	  <% prev_page = (page == 1) ? 1 : parseInt(page)-1; %>
	  <% next_page = (page == total_page) ? parseInt(total_page) : parseInt(page)+1; %> 
	  <li class="<%= prev_disabled %>"><a href="#!/songs/<%= prev_page %>">«</a></li>
	  <% next_disabled = (page == total_page) ? "disabled" : ""; %>
	  <li class="<%= next_disabled %>"><a href="#!/songs/<%= next_page %>">»</a></li>
	  <% if (total_page < 10) { %>
		  <% for (var i=1;i<=total_page;i++) { %>
			  <% active = (i == page) ? "active" : ""; %>
			  <li class="<%= active %>"><a href="#!/songs/<%= i %>"><%= i %></a></li>
		  <% } %>
	  <% } else { %>
		  <li class=""><a href="#!/songs/1">1</a></li>
		  <li class="disabled"><a href="#">...</a></li>
		  <% step = Math.ceil(total_page / 10); %>
		  <% for (var i=2;i<=total_page;i+=step) { %>
			  <% active = (i == page) ? "active" : ""; %>
			  <li class="<%= active %>"><a href="#!/songs/<%= i %>"><%= i %></a></li>
		  <% } %>
		  <li class="disabled"><a href="#">...</a></li>
		  <li class=""><a href="#!/songs/<%= total_page %>"><%= total_page %></a></li>
	  <% } %>
	</ul>
</div>
</script>
