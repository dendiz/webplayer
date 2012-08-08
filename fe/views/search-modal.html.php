<div class="modal hide" id="search-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3>Search</h3>
  </div>
  <div class="modal-body">
    <p>
	<input style="width: 100%; margin-right: 4px;" type="text" id="modal-search-query">
	</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="javascript:;" class="btn btn-primary" data-dismiss="modal" onclick="window.location.href='#!/search/'+$('#modal-search-query').val()+'/1'">Search</a>
  </div>
</div>
