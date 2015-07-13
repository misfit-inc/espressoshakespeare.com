<?php 
	if (is_user_logged_in()) { 
?>
	<div id="login-box" class="login-box">
	
		<a class="signout" href="<?php echo wp_logout_url( home_url() ); ?>"><span class="dn">SIGN OUT</span></a>
	
	</div>
<?php } else { ?>
	
	<div id="login-box" class="login-box">
		
		<p style="float: right;">Already have an account?</p>
		
		<a id="opener" class="signin" href=""><span class="dn">SIGN IN</span></a>
		
		<!-- <p style="float: right; margin-top: 10px;">OR</p> -->
		
		<a id="opener" class="signup" href="http://espressoshakespeare.com/sign-up"><span class="dn">SIGN UP</span></a>
			
	</div>
	
	<!-- <a style="top: 50px;" id="opener-1" class="register" href=""><span class="dn">REGISTER</span></a> -->
	
<?php } ?>


<div id="dialog" class="login-register" title="SIGN IN">

	<div style="border-top: 1px solid #ccc; border-bottom: 1px solid #fff; height: 0; margin-bottom: 20px;"></div>

	<?php 

		global $user_identity, $user_ID;	
		// If user is logged in or registered, show dashboard links in panel
		if (is_user_logged_in()) { 
	?>
	
	
	<?php } else { ?>
		
		<div class="normal-login-register">
			<!-- Login Form -->
			<form class="clearfix login" action="<?php bloginfo('wpurl') ; ?>/wp-login.php" method="post">
				
				<fieldset><label class="login_label label-1" for="log"><?php _e('Username') ?></label>
				<input class="field ui-corner-all" type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="23" />
				<label class="login_label label-1" for="pwd"><?php _e('Password') ?></label>
				<input class="field ui-corner-all" type="password" name="pwd" id="pwd" size="23" />
				<label style="display:none;"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /><?php _e('Remember Me') ?></label>
				<div class="clear"></div>
				<input type="submit" name="submit" value="Login" class="bt_login ui-corner-all" />
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<a class="lost-pwd" href="<?php bloginfo('wpurl') ; ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password?') ?></a>
				</fieldset>
			</form>
						
		</div>
	
	<?php } ?>
</div>