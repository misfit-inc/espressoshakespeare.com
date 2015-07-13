<?php
/*
Template Name: Test Register Page
*/
?>

<form action="" method="post" name="createuser" id="createuser" class="add:users: validate"<?php do_action('user_new_form_tag');?>>
<input name="action" type="hidden" value="createuser" />
<?php wp_nonce_field( 'create-user', '_wpnonce_create-user' ) ?>
<?php
// Load up the passed data, else set to a default.
foreach ( array( 'user_login' => 'login', 'first_name' => 'firstname', 'last_name' => 'lastname',
				'email' => 'email', 'url' => 'uri', 'role' => 'role', 'send_password' => 'send_password', 'noconfirmation' => 'ignore_pass' ) as $post_field => $var ) {
	$var = "new_user_$var";
	if( isset( $_POST['createuser'] ) ) {
		if ( ! isset($$var) )
			$$var = isset( $_POST[$post_field] ) ? stripslashes( $_POST[$post_field] ) : '';
	} else {
		$$var = false;
	}
}

?>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_login"><?php _e('Username'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><input name="user_login" type="text" id="user_login" value="<?php echo esc_attr($new_user_login); ?>" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="email"><?php _e('E-mail'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><input name="email" type="text" id="email" value="<?php echo esc_attr($new_user_email); ?>" /></td>
	</tr>
<?php if ( !is_multisite() ) { ?>
	<tr class="form-field">
		<th scope="row"><label for="first_name"><?php _e('First Name') ?> </label></th>
		<td><input name="first_name" type="text" id="first_name" value="<?php echo esc_attr($new_user_firstname); ?>" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="last_name"><?php _e('Last Name') ?> </label></th>
		<td><input name="last_name" type="text" id="last_name" value="<?php echo esc_attr($new_user_lastname); ?>" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="url"><?php _e('Website') ?></label></th>
		<td><input name="url" type="text" id="url" class="code" value="<?php echo esc_attr($new_user_uri); ?>" /></td>
	</tr>
<?php if ( apply_filters('show_password_fields', true) ) : ?>
	<tr class="form-field form-required">
		<th scope="row"><label for="pass1"><?php _e('Password'); ?> <span class="description"><?php /* translators: password input field */_e('(twice, required)'); ?></span></label></th>
		<td><input name="pass1" type="password" id="pass1" autocomplete="off" />
		<br />
		<input name="pass2" type="password" id="pass2" autocomplete="off" />
		<br />
		<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
		<p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="send_password"><?php _e('Send Password?') ?></label></th>
		<td><label for="send_password"><input type="checkbox" name="send_password" id="send_password" <?php checked( $new_user_send_password ); ?> /> <?php _e('Send this password to the new user by email.'); ?></label></td>
	</tr>
<?php endif; ?>
<?php } // !is_multisite ?>
	<tr class="form-field">
		<th scope="row"><label for="role"><?php _e('Role'); ?></label></th>
		<td><select name="role" id="role">
			<?php
			if ( !$new_user_role )
				$new_user_role = !empty($current_role) ? $current_role : get_option('default_role');
			wp_dropdown_roles($new_user_role);
			?>
			</select>
		</td>
	</tr>
	<?php if ( is_multisite() && is_super_admin() ) { ?>
	<tr>
		<th scope="row"><label for="noconfirmation"><?php _e('Skip Confirmation Email') ?></label></th>
		<td><label for="noconfirmation"><input type="checkbox" name="noconfirmation" id="noconfirmation" value="1"  <?php checked( $new_user_ignore_pass ); ?> /> <?php _e( 'Add the user without sending them a confirmation email.' ); ?></label></td>
	</tr>
	<?php } ?>
</table>

<?php submit_button( __( 'Add New User '), 'primary', 'createuser', true, array( 'id' => 'createusersub' ) ); ?>

</form>