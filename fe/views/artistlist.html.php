<script type="text/html" id="artistlist_tmpl">
<table class="table table-striped table-condensed">
	<thead>
	<tr>
		<th>Artist</th>
		<th>Albums</th>
		<th>Duration</th>
		<th>Info</th>
	</tr>	
	</thead>
	<tbody>
	<% for (var i=0,len=artistlist.length;i<len;i++) { %>
	<% item = artistlist[i]; %>
	<tr style="">
		<td><a href="#!/search/<%= encodeURIComponent(item.artist) %>/1" title="<%= item.artist %>"><%= item.artist.toString().ellipsis(35) %></a></td>
		<td>
			<% var albums = WP.unique(item.hashes.split(",")); %>
			<% for (var j=0;j<albums.length;j++) { %>
				<img width="16" height="16" src="<%= WP.art_url({hash: albums[j]}) %>">
			<% }%>
		</td>
		
		<td><%= WP.human_duration(item.playtime) %></td>
		<td style="width: 200px">
			<span class="badge"><%= item.listens %></span>
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
	  <li class="<%= prev_disabled %>"><a href="#!/artists/<%= prev_page %>">«</a></li>
	  <% next_disabled = (page == total_page) ? "disabled" : ""; %>
	  <li class="<%= next_disabled %>"><a href="#!/artists/<%= next_page %>">»</a></li>
	  <% if (total_page < 10) { %>
		  <% for (var i=1;i<=total_page;i++) { %>
			  <% active = (i == page) ? "active" : ""; %>
			  <li class="<%= active %>"><a href="#!/artists/<%= i %>"><%= i %></a></li>
		  <% } %>
	  <% } else { %>
		  <li class=""><a href="#!/artists/1">1</a></li>
		  <li class="disabled"><a href="#">...</a></li>
		  <% step = Math.ceil(total_page / 10); %>
		  <% for (var i=2;i<=total_page;i+=step) { %>
			  <% active = (i == page) ? "active" : ""; %>
			  <li class="<%= active %>"><a href="#!/artists/<%= i %>"><%= i %></a></li>
		  <% } %>
		  <li class="disabled"><a href="#">...</a></li>
		  <li class=""><a href="#!/artists/<%= total_page %>"><%= total_page %></a></li>
	  <% } %>
	</ul>
</div>
</script>
