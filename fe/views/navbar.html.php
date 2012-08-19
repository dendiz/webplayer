<script type="text/html" id="navbar_tmpl">
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="">
          <a class="brand" href="#">Webplayer</a>
		  <img id="loader" src="<?php echo STATIC_HOST?>/img/loader.gif" style="float:left;margin-top:10px;visibility: hidden">
          <div class="nav-collapse">
            <ul class="nav">
			  <li class="divider-vertical"></li>
              <li class=""><a href="javascript:;" id="prev-btn"><img src="<?php echo STATIC_HOST?>/img/glyphicons_170_step_backward.png"></a></li>
              <li class=""><a href="javascript:;" id="play-btn"><img src="<?php echo STATIC_HOST?>/img/glyphicons_173_play.png"></a></li>
              <li class=""><a href="javascript:;" id="next-btn"><img src="<?php echo STATIC_HOST?>/img/glyphicons_178_step_forward.png"></a></li>
              <li class=""><a href="javascript:;" id="volume-btn"><img src="<?php echo STATIC_HOST?>/img/glyphicons_184_volume_up.png"></a></li>
			  <li><img src="<%= WP.art_url(item)%>" height="24" width="24" style="border: 1px solid #CCC; margin: 7px 7px 0 10px;"></li>
			  <li>
				  <a href="#" style="padding-top: 6px;">
					<div style="line-height: 14px; padding: 7px 0 0 0; display: inline;">
						<div style="color: white"><strong>
						<% if (item) { %>
						<%= item.title.toString().ellipsis(20) %>
						<% } else { %>
						load a song...
						<% } %>
						</strong></div>
						<div style="font-size: 90%;">
						<% if (item) { %>
						<%= item.artist.toString().ellipsis(20) %>
						<% } %>	
						</div>
					</div>
				  </a>
			  </li>
			  <li>
				<a href="#">
				<div id="progress-slider" style="width: 300px; margin-top: 5px; margin-left: 50px;"></div>
				</a>
			  </li>
			  <li>
			 	<p class="navbar-text"><span id="timeupdate">00:00</span> / 
				<% if(item) { %>
				<%= WP.human_duration(item.playtime_seconds) %>
				<% } else {%>	
					00:00
				<% } %>
				</p> 
			  </li>
            </ul>
			<form class="navbar-search pull-left" onsubmit="return false;">
				<input style="width: 110px" type="text" id="search" class="search-query" placeholder="">
			</form>
			<a href="javascript:;" onclick="WP.search_submit()" class="btn btn-primary" href="#">Search</a>
			<% if (WP.login_status) { %>
				<a href="javascript:;" onclick="WP.logout()">Logout</a>
			<% } else { %>
				<a href="#login-modal" data-toggle="modal">Login</a>
			<% } %>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
</script>
