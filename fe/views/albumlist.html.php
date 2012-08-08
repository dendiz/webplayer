<script type="text/html" id="albumlist_tmpl">
<table class="table table-striped table-condensed">
	<thead>
	<tr>
		<th style="width:55px;"># Tracks</th>
		<th>Ctrl</th>
		<th>Artist</th>
		<th>Album</th>
		<th>Length</th>
		<th>Info</th>
	</tr>	
	</thead>
	<tbody>
	<% for (var i=0,len=albumlist.length;i<len;i++) { %>
	<% item = albumlist[i]; %>
	<tr style="">
		<td><%= item.total%></td>
		<td style="width: 90px">
			<div class="btn-toolbar" style="margin: 0">
				<div class="btn-group">
				  <button class="btn btn-mini add-pq-btn" album-hash="<%= item.hash %>">PQ</button>
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
		<td><span title="<%= item.album %>"><%= item.album.toString().ellipsis(35) %></span></td>
		<td><%= WP.human_duration(item.duration) %></td>
		<td style="width: 200px">
			<span class="label label-info"><%= WP.human_filesize(item.size) %></span>
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
	  <% prev_page = (page == 1) ? 1 : page-1; %>
	  <% next_page = (page == total_page) ? total_page : parseInt(page)+1; %> 
	  <li class="<%= prev_disabled %>"><a href="#!/albums/<%= prev_page %>">«</a></li>
	  <% next_disabled = (page == total_page) ? "disabled" : ""; %>
	  <li class="<%= next_disabled %>"><a href="#!/albums/<%= next_page %>">»</a></li>
	  <% step = Math.ceil(total_page / 10); %>
	  <% for (var i=1,len=total_page;i<=len;i+=step) { %>
		  <% active = (i == page) ? "active" : ""; %>
		  <li class="<%= active %>"><a href="#!/albums/<%= i %>"><%= i %></a></li>
	  <% } %>
	</ul>
  </div>
</script>
