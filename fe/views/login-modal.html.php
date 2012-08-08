<div class="modal hide" id="login-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3>Login</h3>
  </div>
  <div class="modal-body">
    <p>
	<strong>E-mail</strong>
	<br/>
	<input style="width: 150px; margin-right: 4px;" type="text" id="modal-login-email" name="email">
	<br/>
	<strong>Password</strong>
	<br/>
	<input style="width: 150px; margin-right: 4px;" type="password" id="modal-login-password" name="password">
	<br />
    <a href="javascript:;" class="" onclick="WP.register()" >register</a>
	</p>
	<div id="modal-login-error" style="color:red"></div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="javascript:;" class="btn btn-primary"  onclick="WP.login()">Login</a>
  </div>
</div>
