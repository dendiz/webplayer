<script type="text/html" id="playlists_tmpl">
<table class="table table-striped table-condensed">
	<thead>
	<tr>
		<th>C</th>
		<th>Name</th>
		<th>Song count</th>
		<th>Duration</th>
		<th>Info</th>
	</tr>	
	</thead>
	<tbody>
	<% if (playlists.length == 1 && !playlists[0].name) { %>
		<tr>
			<td colspan="5">You dont have any saved playlists.</td>
		</tr>
		<% playlists = []; %>
	<% } %>
	<% for (var i=0,len=playlists.length;i<len;i++) { %>
	<% item = playlists[i]; %>
	<tr style="">
		<td style="width: 50px">
			<div class="btn-toolbar" style="margin: 0">
				<div class="btn-group">
				  <button class="btn btn-mini add-pq-btn" playlist-id="<%= item.id_playlists %>">PQ</button>
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
		<td style="width: 300px;"><%= item.name.toString().ellipsis(35) %></td>
		<td><span class="badge"><%= item.total %></span></td>
		
		<td><%= WP.human_duration(item.duration) %></td>
		<td style="width: 200px">
			<% var albums = WP.unique(item.hashes.split(",")); %>
			<% for (var j=0;j<albums.length;j++) { %>
				<img width="16" height="16" src="<%= WP.art_url({hash: albums[j]}) %>">
			<% }%>
		</td>	
	</tr>
	<% } %>
	</tbody>
</table>
<% total_page = Math.ceil(total/WP.PER_PAGE); %>
<% if (total_page > 1) { %>
<div class="pagination .pagination-centered">
	<ul>
	  <% prev_disabled = (page == 1) ? "disabled" : ""; %>
	  <% prev_page = (page == 1) ? 1 : parseInt(page)-1; %>
	  <% next_page = (page == total_page) ? parseInt(total_page) : parseInt(page)+1; %> 
	  <li class="<%= prev_disabled %>"><a href="#!/playlists/<%= prev_page %>">«</a></li>
	  <% next_disabled = (page == total_page) ? "disabled" : ""; %>
	  <li class="<%= next_disabled %>"><a href="#!/playlists/<%= next_page %>">»</a></li>
	  <% if (total_page < 10) { %>
		  <% for (var i=1;i<=total_page;i++) { %>
			  <% active = (i == page) ? "active" : ""; %>
			  <li class="<%= active %>"><a href="#!/playlists/<%= i %>"><%= i %></a></li>
		  <% } %>
	  <% } else { %>
		  <li class=""><a href="#!/playlists/1">1</a></li>
		  <li class="disabled"><a href="#">...</a></li>
		  <% step = Math.ceil(total_page / 10); %>
		  <% for (var i=2;i<=total_page;i+=step) { %>
			  <% active = (i == page) ? "active" : ""; %>
			  <li class="<%= active %>"><a href="#!/playlists/<%= i %>"><%= i %></a></li>
		  <% } %>
		  <li class="disabled"><a href="#">...</a></li>
		  <li class=""><a href="#!/playlists/<%= total_page %>"><%= total_page %></a></li>
	  <% } %>
	</ul>
</div>
<% } %> <!-- total page -->
</script>
