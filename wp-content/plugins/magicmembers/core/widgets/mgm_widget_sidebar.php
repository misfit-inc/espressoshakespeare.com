<?php
// sidebar
global $mgm_sidebar_widget;
$mgm_sidebar_widget = mgm_get_class('sidebar_widget');

// ---------------------------------------------------------------------------------------------------------------
// login widget : multiple instance
function mgm_sidebar_widget_login($args, $widget_args = 1){
	global $user_ID, $current_user, $mgm_sidebar_widget;

	// if hide on custom login page 
	$post_id = get_the_ID();
	// post custom register	
	if($post_id>0){
		// if match
		if( get_permalink($post_id) == mgm_get_custom_url('login') )
			return "";
	}
	
	// actual widget
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);	

	// get widget options
	$options = $mgm_sidebar_widget->login_widget;
	
	// validate
	if (!isset($options[$number])) {
		return;
	}	
	// home url
	$home_url                = home_url();

	// get options
	$title_logged_in         = (isset($options[$number]['title_logged_in']) ? $options[$number]['title_logged_in']:__('Magic Membership Details','mgm'));
	$title_logged_out        = (isset($options[$number]['title_logged_out']) ? $options[$number]['title_logged_out']:__('Login','mgm'));
	$profile_text 		     = (isset($options[$number]['profile_text']) ? $options[$number]['profile_text']:__('Profile','mgm'));
	$membership_details_text = (isset($options[$number]['membership_details_text']) ? $options[$number]['membership_details_text']:__('Membership Details','mgm'));
	$membership_contents_text = (isset($options[$number]['membership_contents_text']) ? $options[$number]['membership_contents_text']:__('Membership Contents','mgm'));
	$logout_text             = (isset($options[$number]['logout_text']) ? $options[$number]['logout_text']:__('Logout','mgm'));
	$register_text           = (isset($options[$number]['register_text']) ? $options[$number]['register_text']:__('Register','mgm'));
	$lostpassword_text       = (isset($options[$number]['lostpassword_text']) ? $options[$number]['lostpassword_text']:__('Lost your Password?','mgm'));
	$logged_out_intro        = (isset($options[$number]['logged_out_intro']) ? stripslashes($options[$number]['logged_out_intro']):'');
	
	// logged in user view
	if ($user_ID) {
		echo $before_widget;
		
		if (trim($title_logged_in)) {
			echo $before_title . $title_logged_in . $after_title;
		}
		
		//>=WP2.7 = DB9872
		if (get_option('db_version') >= 9872) {
			$logout_url = wp_logout_url($home_url);
		} else {
			//$logout_url = trailingslashit($home_url) . 'wp-login.php?action=logout';
			$logout_url = add_query_arg(array('action' => 'logout'), mgm_get_custom_field_array('login'));
		}
		
		$membership_details_link 	= mgm_get_custom_url('membership_details');
		$membership_contents_link 	= mgm_get_custom_url('membership_contents');
		$profile_link 				= mgm_get_custom_url('profile');
		// set tmpl
		$logged_in_template = (isset($options[$number]['logged_in_template']) ? $options[$number]['logged_in_template'] : $mgm_sidebar_widget->default_text['logged_in_template']);
		$logged_in_template = str_replace('[display_name]', $current_user->display_name, $logged_in_template);
		$logged_in_template = str_replace('[membership_details_url]', $membership_details_link, $logged_in_template);		
		$logged_in_template = str_replace('[membership_details_link]', sprintf('<a href="%s">%s</a>',$membership_details_link, $membership_details_text), $logged_in_template);		
		$logged_in_template = str_replace('[membership_contents_url]', $membership_contents_link, $logged_in_template);		
		$logged_in_template = str_replace('[membership_contents_link]', sprintf('<a href="%s">%s</a>',$membership_contents_link, $membership_contents_text), $logged_in_template);		
		$logged_in_template = str_replace('[profile_url]', $profile_link, $logged_in_template);		
		$logged_in_template = str_replace('[profile_link]', sprintf('<a href="%s">%s</a>',$profile_link, $profile_text), $logged_in_template);
		$logged_in_template = str_replace('[logout_url]', $logout_url, $logged_in_template);
		$logged_in_template = str_replace('[logout_link]', '<a href="' . $logout_url . '">' . $logout_text . '</a>', $logged_in_template);

		echo $logged_in_template;

		echo $after_widget;
	} else {
		echo $before_widget;
		
		if (trim($title_logged_out)) {
			echo $before_title . $title_logged_out . $after_title;
		}		

		echo $logged_out_intro;

		echo mgm_login_form($register_text, $lostpassword_text);

		echo $after_widget;
	}
}

// login admin widget
function mgm_sidebar_widget_login_admin($widget_args = 1 ) {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	static $updated = false;
	
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// options init
	$options = $mgm_sidebar_widget->login_widget;
	
	// default
	if (!is_array($options)) {
		$options = array();
	}
	
	// update
	if (!$updated && !empty($_POST['sidebar'])) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if (isset($sidebars_widgets[$sidebar])) {
			$this_sidebar =& $sidebars_widgets[$sidebar];
		} else {
			$this_sidebar = array();
		}

		foreach ($this_sidebar as $_widget_id) {
			if ('mgm_widget_login' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if (!in_array("login-$widget_number", $_POST['widget-id'])) {// the widget has been removed.
					unset($options[$widget_number]);
				}
			}
		}
		
		// update
		foreach ((array)$_POST['mgm_widget_login'] as $widget_number=>$mgm_widget_login) {
			if (!isset($mgm_widget_login['title_logged_in']) && isset($options[$widget_number])) {// user clicked cancel
				continue;
			}
			
			// set vars
			$title_logged_in         = stripslashes($mgm_widget_login['title_logged_in']);
			$title_logged_out        = stripslashes($mgm_widget_login['title_logged_out']);
			$profile_text 			 = stripslashes($mgm_widget_login['profile_text']);
			$membership_details_text = stripslashes($mgm_widget_login['membership_details_text']);
			$membership_contents_text= stripslashes($mgm_widget_login['membership_contents_text']);
			$logout_text             = stripslashes($mgm_widget_login['logout_text']);
			$register_text           = stripslashes($mgm_widget_login['register_text']);
			$lostpassword_text       = stripslashes($mgm_widget_login['lostpassword_text']);
			$logged_out_intro        = stripslashes($mgm_widget_login['logged_out_intro']); 			
			$logged_in_template      = stripslashes($mgm_widget_login['logged_in_template']);
			
			// set
			$options[$widget_number] = compact('title_logged_in', 'title_logged_out','profile_text','membership_contents_text', 'membership_details_text', 'logout_text',
			                                   'register_text','intro_logged_out','lostpassword_text',
											   'logged_out_intro','logged_in_template');
		}
		
		$mgm_sidebar_widget->login_widget = $options;
		// update_option('mgm_sidebar_widget', $mgm_sidebar_widget);
		$mgm_sidebar_widget->save();
		// updated
		$updated = true;
	}

	// get selected	
	if (-1 == $number) {
		$number                  = '%i%';
		$title_logged_in         = __('Magic Membership Details','mgm');
		$title_logged_out        = __('Login','mgm');		
		$profile_text 			 = __('Profile','mgm');
		$membership_contents_text= __('Membership Contents','mgm');	
		$membership_details_text = __('Membership Details','mgm');			
		$logout_text             = __('Logout','mgm');
		$register_text           = __('Register','mgm');
		$lostpassword_text       = __('Lost your Password?','mgm');
		$logged_out_intro        = '';
		$logged_in_template      = $mgm_sidebar_widget->default_text['logged_in_template'];
	} else {
		$title_logged_in         = stripslashes($options[$number]['title_logged_in']);
		$title_logged_out        = stripslashes($options[$number]['title_logged_out']);		
		$profile_text 			 = stripslashes($options[$number]['profile_text']);		
		$membership_contents_text= stripslashes($options[$number]['membership_contents_text']);		
		$membership_details_text = stripslashes($options[$number]['membership_details_text']);		
		$logout_text             = stripslashes($options[$number]['logout_text']);
		$register_text           = stripslashes($options[$number]['register_text']);
		$lostpassword_text       = stripslashes($options[$number]['lostpassword_text']);
		$logged_out_intro        = stripslashes($options[$number]['intro_logged_out']);
		$logged_in_template      = stripslashes($options[$number]['logged_in_template']);
	}	

	// print
	echo '<p>' . __('When logged out the user will see a login form. Removing the text from the "Register link text" or "Lost password link text" will subsequently remove the links they produce.', 'mgm') . '</p>
	<input type="hidden" name="mgm_widget_login['.$number.'][submit]" id="mgm-login-widget-submit-'.$number.'" value="1" />
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-widget-title"><strong>' . __('Widget Title (Logged in):','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $title_logged_in . '" id="mgm-login-widget-widget-title-logged-in-'.$number.'" name="mgm_widget_login['.$number.'][title_logged_in]" /></label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-widget-title-logged-out"><strong>' . __('Widget Title (Logged out):','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $title_logged_out . '" id="mgm-login-widget-widget-title-logged-out-'.$number.'" name="mgm_widget_login['.$number.'][title_logged_out]" /></label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-profile-text"><strong>' . __('Profile link text:','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $profile_text . '" id="mgm-login-widget-profile-text-'.$number.'" name="mgm_widget_login['.$number.'][profile_text]" /></label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-membership-details-text"><strong>' . __('Membership Details link text:','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $membership_details_text . '" id="mgm-login-widget-membership-details-text-'.$number.'" name="mgm_widget_login['.$number.'][membership_details_text]" /></label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-membership-contents-text"><strong>' . __('Membership Contents link text:','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $membership_contents_text . '" id="mgm-login-widget-membership-contents-text-'.$number.'" name="mgm_widget_login['.$number.'][membership_contents_text]" /></label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-logout-text"><strong>' . __('Logout text:','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $logout_text . '" id="mgm-login-widget-logout-text-'.$number.'" name="mgm_widget_login['.$number.'][logout_text]" />
		</label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-register-text"><strong>' . __('Register link text:','mgm') . '</strong></div>
		<input style="width: 300px;" value="' . $register_text . '" id="mgm-login-widget-register-text-'.$number.'" name="mgm_widget_login['.$number.'][register_text]" />
		</label>
	</div>
	<div style="margin-bottom: 5px;">
		<div><label for="mgm-login-widget-lostpassword-text"><strong>' . __('Lost password link text:','mgm') . '</strong></div>
		<input style="width: 300px;" value="' .$lostpassword_text . '" id="mgm-login-widget-lostpassword-text-'.$number.'"	name="mgm_widget_login['.$number.'][lostpassword_text]" /></label>
	</div>
	<div style="margin-bottom: 5px;">				
		<label for="mgm-login-widget-logged-out-intro">
			<div><strong>' . __('Logged Out Introduction','mgm') . '</strong></div>
			<textarea rows="2" cols="50" id="mgm-login-widget-logged-out-intro-'.$number.'" name="mgm_widget_login['.$number.'][logged_out_intro]">' . esc_html($logged_out_intro) . '</textarea>
		</label>
	</div>
	<div style="margin-bottom: 5px;">				
		<label for="mgm-login-widget-logged-in-template">
			<div><strong>' . __('Logged In Template','mgm') . '</strong> - Use the following hooks: [display_name], [profile_url], [profile_link], [membership_details_url], [membership_details_link],[membership_contents_url], [membership_contents_link], [logout_url], [logout_link]</div>
			<textarea rows="6" cols="50" id="mgm-login-widget-logged-in-template-'.$number.'" name="mgm_widget_login['.$number.'][logged_in_template]">' . $logged_in_template . '</textarea>
		</label>
	</div>
	';	
}
// hooks
// wp_register_sidebar_widget('mgm_sidebar_widget_login', __('Magic Members Login','mgm'), 'mgm_sidebar_widget_login');
// wp_register_widget_control('mgm_sidebar_widget_login', __('Magic Members Login','mgm'), 'mgm_sidebar_widget_login_admin', array('width'=>400));

// define login widget register
function mgm_sidebar_widget_login_register() {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	
	// $options[$number]['title']
	if ( !$options = $mgm_sidebar_widget->login_widget )
		$options = array();
	
	// defaults
	$widget_ops  = array();
	$control_ops = array('width' => 400, 'id_base' => 'mgm_sidebar_widget_login');
	
	// widget name
	$name = __('Magic Members Login','mgm');
	$registered = false;
	foreach ( array_keys($options) as $o ) {
	// Old widgets can have null values for some reason
		if (isset($options[$o]['title_logged_in']) ) {
			$registered = true;
			$id = "mgm_sidebar_widget_login-$o"; 
			// register			
			wp_register_sidebar_widget( $id, $name, 'mgm_sidebar_widget_login', $widget_ops, array( 'number' => $o ) );
			wp_register_widget_control( $id, $name, 'mgm_sidebar_widget_login_admin', $control_ops, array( 'number' => $o ) );
		}
	}
	// If there are none, we register the widget's existence with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'mgm_sidebar_widget_login-1', $name, 'mgm_sidebar_widget_login', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'mgm_sidebar_widget_login-1', $name, 'mgm_sidebar_widget_login_admin', $control_ops, array( 'number' => -1 ) );
	}
}
// register login widget
mgm_sidebar_widget_login_register();
// END login widget ----------------------------------------------------------------------------------------------

// ---------------------------------------------------------------------------------------------------------------
// registration widget : multiple instance
function mgm_sidebar_widget_registration($args, $widget_args = 1){
	global $wpdb, $user_ID, $current_user, $mgm_sidebar_widget, $wp_query;
	extract($args, EXTR_SKIP);
	
	if (is_numeric($widget_args))
		$widget_args = array('number'=>$widget_args);	
	$widget_args = wp_parse_args($widget_args, array('number'=>-1));
	extract($widget_args, EXTR_SKIP);	

	// options init
	$options = $mgm_sidebar_widget->register_widget;
	
	if (!isset($options[$number])) {
		return;
	}
	//skip widget if BUDDYPRESS is loaded
	if(defined('BP_VERSION'))
		return;
	//skip registation page:
	if(in_array(trailingslashit(mgm_current_url()),array(trailingslashit(mgm_get_custom_url('register'))), trailingslashit(mgm_get_custom_url('register', true))) )	
		return;
	//skip if on transactions page:
	foreach(array('payments','subscribe','purchase','transactions') as $query) {
		// set if
		if(isset( $wp_query->query_vars[$query] )){
			return;
		}
	}
	if((isset($_GET['method']) && preg_match('/payment_/', $_GET['method'] ))) {
		return;
	}	
		
	// set
	$title             = (isset($options[$number]['title']) ? $options[$number]['title'] : __('Magic Members - Register','mgm'));
	$intro             = (isset($options[$number]['intro']) ? $options[$number]['intro'] : '');
	$use_custom_fields = (isset($options[$number]['use_custom_fields']) ? $options[$number]['use_custom_fields']: true);
	
	// user looged in
	if (!$user_ID) {
		// if hide on custom register page 
		$post_id = get_the_ID();
		// post custom register	
		if($post_id>0){
			// if match
			if( get_permalink($post_id) == mgm_get_custom_url('register') )
				return "";
		}	
		
		// start actual widget
		echo $before_widget;
		
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		
		if ($intro) {			
			// echo $intro;
		}
		$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true)));
		// display form
		echo '<form name="registerform" id="registerform" action="' . mgm_get_custom_url('register') . '" method="post">';
		if(!mgm_is_customfield_active(array('username'), $cf_register_page) || !$use_custom_fields) {
			echo '<p>
					<label>' . __('Username') . '<br />
					<input type="text" name="user_login" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
				</p>';
		}
		if(!mgm_is_customfield_active(array('email'), $cf_register_page) || !$use_custom_fields) {
			echo '<p>
					<label>' . __('E-mail') . '<br />
					<input type="text" name="user_email" id="user_email" class="input" value="" size="20" tabindex="20" /></label>
				</p>';
		}
		// custom
		if ($use_custom_fields) {
			do_action('register_form');
		}
		
		echo '<p id="reg_passmail">' . __('A password will be e-mailed to you.') . '</p>
			  <p><input class="mgm-register-button" type="submit" name="wp-submit" id="wp-submit" value="' . __('Register &raquo;') . '" tabindex="100" /></p>
			  <input type="hidden" name="method" value="create_user">
			  </form>';
		
		echo $after_widget;
	}
	
}

// registration admin widget
function mgm_sidebar_widget_registration_admin($widget_args=1) {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	static $updated = false;
	
	if (is_numeric($widget_args))
		$widget_args = array('number'=>$widget_args);		
	$widget_args = wp_parse_args($widget_args, array('number'=>-1));
	extract($widget_args, EXTR_SKIP);	
	
	// options init
	$options = $mgm_sidebar_widget->register_widget;
	
	// default
	if (!is_array($options)) {
		$options = array();
	}
	
	// update
	if (!$updated && !empty($_POST['sidebar'])) {
		$sidebar = (string)$_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if (isset($sidebars_widgets[$sidebar])) {
			$this_sidebar =& $sidebars_widgets[$sidebar];
		} else {
			$this_sidebar = array();
		}

		foreach ($this_sidebar as $_widget_id) {
			if ('mgm_widget_registration' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if (!in_array("registration-$widget_number", $_POST['widget-id'])) {// the widget has been removed.
					unset($options[$widget_number]);
				}
			}
		}
		
		// update
		foreach ((array)$_POST['mgm_widget_registration'] as $widget_number=>$mgm_widget_registration) {
			if (!isset($mgm_widget_registration['title']) && isset($options[$widget_number])) {// user clicked cancel
				continue;
			}
			
			// set vars
			$title             = stripslashes($mgm_widget_registration['title']);
			$intro             = stripslashes($mgm_widget_registration['intro']);
			$use_custom_fields = isset($mgm_widget_registration['use_custom_fields']) ? $mgm_widget_registration['use_custom_fields'] : false;			
			
			// set
			$options[$widget_number] = compact('title', 'intro', 'use_custom_fields');
		}
		
		// update
		$mgm_sidebar_widget->register_widget = $options;
		// update_option('mgm_sidebar_widget', $mgm_sidebar_widget);
		$mgm_sidebar_widget->save();
		// updated
		$updated = true;
	}
	
	// get selected	
	if (-1 == $number) {
		$number            = '%i%';
		$title             = '';
		$intro             = trim($mgm_sidebar_widget->default_text['active_intro']);
		$use_custom_fields = false;			
	} else {
		$title             = stripslashes($options[$number]['title']);
		$intro             = stripslashes($options[$number]['intro']);		
		$use_custom_fields = $options[$number]['use_custom_fields'];			
	}	
	
	echo '	<input type="hidden" name="mgm_widget_registration['.$number.'][submit]" id="mgm_widget_registration_submit_'.$number.'" value="1" />
			<p>
				<div style="margin-bottom: 5px;">
					<label for="mgm_register_sidebar_widget_title">
						<div><strong>' . __('Widget Title','mgm') . '</strong></div>
						<input style="width: 300px;" type="text" value="' . $title . '" id="mgm_widget_registration_title_'.$number.'" name="mgm_widget_registration['.$number.'][title]" />
					</label>
				</div>
				<div style="margin-bottom: 5px;">
					<label for="mgm_register_sidebar_widget_use_custom_fields">
						<strong>' . __('Use Custom Fields in form?','mgm') . '</strong>
						<input style="width: 30px;" type="checkbox" ' . ($use_custom_fields? 'checked="checked"':'') . ' value="1" id="mgm_widget_registration_use_custom_fields_'.$number.'" name="mgm_widget_registration['.$number.'][use_custom_fields]" />
					</label>
				</div>
				<div style="margin-bottom: 5px;">
					<label for="mgm_register_sidebar_widget_active_intro">
						<div><strong>' . __('Introduction','mgm') . '</strong></div>
						<textarea rows="6" cols="50" id="mgm_widget_registration_intro_'.$number.'" name="mgm_widget_registration['.$number.'][intro]">' . $intro . '</textarea>
					</label>
				</div>
			</p>';
}
// hooks
// wp_register_sidebar_widget('mgm_sidebar_widget_registration', __('Magic Members Register','mgm'), 'mgm_sidebar_widget_registration');
// wp_register_widget_control('mgm_sidebar_widget_registration', __('Magic Members Register','mgm'), 'mgm_sidebar_widget_registration_admin', array('width'=>400));

// define registration widget register
function mgm_sidebar_widget_registration_register() {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	
	// $options[$number]['title']
	if ( !$options = $mgm_sidebar_widget->register_widget )
		$options = array();
	
	// defaults
	$widget_ops  = array();
	$control_ops = array('width' => 400, 'id_base' => 'mgm_sidebar_widget_registration');
	
	// widget name
	$name = __('Magic Members Register','mgm');
	$registered = false;
	foreach ( array_keys($options) as $o ) {
	// Old widgets can have null values for some reason
		if (isset($options[$o]['title']) ) {
			$registered = true;
			$id = "mgm_sidebar_widget_registration-$o"; 
			// register			
			wp_register_sidebar_widget( $id, $name, 'mgm_sidebar_widget_registration', $widget_ops, array( 'number' => $o ) );
			wp_register_widget_control( $id, $name, 'mgm_sidebar_widget_registration_admin', $control_ops, array( 'number' => $o ) );
		}
	}
	// If there are none, we register the widget's existence with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'mgm_sidebar_widget_registration-1', $name, 'mgm_sidebar_widget_registration', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'mgm_sidebar_widget_registration-1', $name, 'mgm_sidebar_widget_registration_admin', $control_ops, array( 'number' => -1 ) );
	}
}
// register registration widget
mgm_sidebar_widget_registration_register();
// END registration widget ----------------------------------------------------------------------------------------------

// ---------------------------------------------------------------------------------------------------------------
// status widget : multiple instance
function mgm_sidebar_widget_status($args, $widget_args = 1){
	global $wpdb, $user_ID, $current_user, $mgm_sidebar_widget;
	extract($args, EXTR_SKIP);
	
	if (is_numeric($widget_args))
		$widget_args = array('number'=>$widget_args);	
	$widget_args = wp_parse_args($widget_args, array('number'=>-1));
	extract($widget_args, EXTR_SKIP);	

	$options = $mgm_sidebar_widget->status_widget;
	
	if (!isset($options[$number])) {
		return;
	}
	
	$title            = (isset($options[$number]['title']) ? $options[$number]['title']:__('Magic Members','mgm'));
	$logged_out_intro = (isset($options[$number]['logged_out_intro']) ? stripslashes($options[$number]['logged_out_intro']):$mgm_sidebar_widget->default_text['logged_out_intro']);
	$hide_logged_out  = (isset($options[$number]['hide_logged_out']) ? stripslashes($options[$number]['hide_logged_out']):false);

	if ($user_ID) {		
		echo $before_widget;

		if (trim($title)) {
			echo $before_title . $title . $after_title;
		}
	
		//issue#: 539		
		$mgm_member = mgm_get_member($user_ID);
		$uat = $mgm_member->membership_type;
		if (!$uat) {
			$uat = 'free';
		}
		
		$user_status = $mgm_member->status;
		
		if ($user_status != MGM_STATUS_ACTIVE || strtolower($uat) == 'free') {

			$inactive_intro = (isset($options[$number]['inactive_intro']) ? $options[$number]['inactive_intro']:$mgm_sidebar_widget->default_text['inactive_intro']);
			echo $inactive_intro;
			mgm_sidebar_register_links();

		} else {
			if ($expiry = $mgm_member->expire_date) {
				$date = explode('-', $expiry);
				$expiry = date(get_option('date_format'), mktime(0,0,0,$date[1], $date[2], $date[0]));
			} else {
				$expiry = __('None', 'mgm');
			}

			$active_intro = $mgm_sidebar_widget->default_text['active_intro'];
			if (isset($options[$number]['active_intro'])) {
				$active_intro = $options[$number]['active_intro'];
			}
				
			$active_intro = str_replace('[membership_type]', mgm_get_class('membership_types')->get_type_name($uat), $active_intro);
			$active_intro = str_replace('[expiry_date]', $expiry, $active_intro);

			echo $active_intro;
			mgm_render_my_purchased_posts($user_ID);
		}
		
		echo $after_widget;
	} else {
		if (!$hide_logged_out) {
			echo $before_widget;
			
			if (trim($title)) {
				echo $before_title . $title . $after_title;
			}
		
			echo $logged_out_intro;
			echo mgm_get_login_register_links(get_option('siteurl'));
			echo $after_widget;
		}
	}
}

// status admin widget
function mgm_sidebar_widget_status_admin($widget_args=1) {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	static $updated = false;

	if (is_numeric($widget_args))
		$widget_args = array('number'=>$widget_args);
		
	$widget_args = wp_parse_args($widget_args, array('number'=>-1));
	extract($widget_args, EXTR_SKIP);	

	$options = $mgm_sidebar_widget->status_widget;
	
	if (!is_array($options)) {
		$options = array();
	}

	// update
	if (!$updated && !empty($_POST['sidebar'])) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if (isset($sidebars_widgets[$sidebar])) {
			$this_sidebar =& $sidebars_widgets[$sidebar];
		} else {
			$this_sidebar = array();
		}

		foreach ($this_sidebar as $_widget_id) {
			if ('mgm_widget_status' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if (!in_array("status-$widget_number", $_POST['widget-id'])) {// the widget has been removed.
					unset($options[$widget_number]);
				}
			}
		}

		foreach ((array)$_POST['mgm_widget_status'] as $widget_number=>$mgm_widget_status) {
			if (!isset($mgm_widget_status['title']) && isset($options[$widget_number])) {// user clicked cancel
				continue;
			}
			
			// set vars
			$title         	  = stripslashes($mgm_widget_status['title']);
			$active_intro     = stripslashes($mgm_widget_status['active_intro']);
			$inactive_intro   = stripslashes($mgm_widget_status['inactive_intro']);
			$logged_out_intro = stripslashes($mgm_widget_status['logged_out_intro']);
			$hide_logged_out  = (int)$mgm_widget_status['hide_logged_out'];			

			$options[$widget_number] = compact('title', 'active_intro', 'inactive_intro', 'logged_out_intro', 'hide_logged_out');
		}
		
		$mgm_sidebar_widget->status_widget = $options;
		// update_option('mgm_sidebar_widget', $mgm_sidebar_widget);
		$mgm_sidebar_widget->save();
		// updated
		$updated = true;
	}

	// get selected	
	if (-1 == $number) {
		$number            = '%i%';
		$title             = '';
		$active_intro      = trim($mgm_sidebar_widget->default_text['active_intro']);
		$inactive_intro    = trim($mgm_sidebar_widget->default_text['inactive_intro']);	
		$logged_out_intro  = trim($mgm_sidebar_widget->default_text['logged_out_intro']);	
		$hide_logged_out   = 0;			
	} else {
		$title             = stripslashes($options[$number]['title']);
		$active_intro      = stripslashes($options[$number]['active_intro']);		
		$inactive_intro    = stripslashes($options[$number]['inactive_intro']);
		$logged_out_intro  = stripslashes($options[$number]['logged_out_intro']);
		$hide_logged_out   = (int)$options[$number]['hide_logged_out'];			
	}	
	
	echo '	<input type="hidden" name="mgm_sidebar_widget_submit" id="mgm_sidebar_widget_submit" value="1" />
			<p>
				<div style="margin-bottom: 5px;">
				<label for="mgm_sidebar_widget_title">
					<div><strong>' . __('Widget Title','mgm') . '</strong></div>
					<input style="width: 300px;" type="text" value="' . $title . '" id="mgm_widget_status_title_'.$number.'" name="mgm_widget_status['.$number.'][title]" />
				</label>
				</div>
				<div style="margin-bottom: 5px;">
				<label for="mgm_sidebar_widget_active_intro">
					<div><strong>' . __('User Active Introduction','mgm') . '</strong> - Use [membership_type] and [expiry_date]</div>
					<textarea rows="6" cols="50" id="mgm_widget_status_active_intro_'.$number.'" name="mgm_widget_status['.$number.'][active_intro]">' . $active_intro . '</textarea>
				</label>
				</div>
				<div style="margin-bottom: 5px;">				
				<label for="mgm_sidebar_widget_inactive_intro">
					<div><strong>' . __('User Inactive Introduction','mgm') . '</strong></div>
					<textarea rows="6" cols="50" id="mgm_widget_status_inactive_intro_'.$number.'" name="mgm_widget_status['.$number.'][inactive_intro]">' . $inactive_intro . '</textarea>
				</label>
				</div>
				<div style="margin-bottom: 5px;">				
				<label for="mgm_sidebar_widget_logged_out_intro">
					<div><strong>' . __('User Logged Out Introduction','mgm') . '</strong></div>
					<textarea rows="6" cols="50" id="mgm_widget_status_logged_out_intro_'.$number.'" name="mgm_widget_status['.$number.'][logged_out_intro]">' . $logged_out_intro . '</textarea>
				</label>
				</div>
				<div style="margin-bottom: 5px;">				
				<label for="mgm_sidebar_widget_hide_logged_out">
					<div><strong>' . __('Hide widget when user logged out?','mgm') . '</strong>
					<input type="checkbox" id="mgm_widget_status_hide_logged_out_'.$number.'" name="mgm_widget_status['.$number.'][hide_logged_out]" value="1" ' . ($hide_logged_out ? 'checked="checked"':'') . ' />
				</div>
				</label>
				</div>				
			</p>';
}
// hooks
// wp_register_sidebar_widget('mgm_sidebar_widget_status', __('Magic Members Status','mgm'), 'mgm_sidebar_widget_status');
// wp_register_widget_control('mgm_sidebar_widget_status', __('Magic Members Status','mgm'), 'mgm_sidebar_widget_status_admin', array('width'=>400));

// define status widget register
function mgm_sidebar_widget_status_register() {
	global $wp_registered_widgets, $mgm_sidebar_widget, $wp_query;
	
	//skip if on transactions page:
	foreach(array('payments','subscribe','purchase','transactions') as $query) {
		// set if
		if(isset( $wp_query->query_vars[$query] )){
			return;
		}
	}
	if((isset($_GET['method']) && preg_match('/payment_/', $_GET['method'] ))) {
		return;
	}
	
	// $options[$number]['title']
	if ( !$options = $mgm_sidebar_widget->status_widget )
		$options = array();
	
	// defaults
	$widget_ops  = array();
	$control_ops = array('width' => 400, 'id_base' => 'mgm_sidebar_widget_status');
	
	// widget name
	$name = __('Magic Members Status','mgm');
	$registered = false;
	foreach ( array_keys($options) as $o ) {
	// Old widgets can have null values for some reason
		if (isset($options[$o]['title']) ) {
			$registered = true;
			$id = "mgm_sidebar_widget_status-$o"; 
			// register			
			wp_register_sidebar_widget( $id, $name, 'mgm_sidebar_widget_status', $widget_ops, array( 'number' => $o ) );
			wp_register_widget_control( $id, $name, 'mgm_sidebar_widget_status_admin', $control_ops, array( 'number' => $o ) );
		}
	}
	// If there are none, we register the widget's existence with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'mgm_sidebar_widget_status-1', $name, 'mgm_sidebar_widget_status', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'mgm_sidebar_widget_status-1', $name, 'mgm_sidebar_widget_status_admin', $control_ops, array( 'number' => -1 ) );
	}
}
// register status widget
mgm_sidebar_widget_status_register();
// END status widget ----------------------------------------------------------------------------------------------

// ---------------------------------------------------------------------------------------------------------------
// text widget : multiple instance
function mgm_sidebar_widget_text($args, $widget_args = 1){
	global $mgm_sidebar_widget;
	extract($args, EXTR_SKIP);
	
	if (is_numeric($widget_args))
		$widget_args = array('number'=>$widget_args);	
	$widget_args = wp_parse_args($widget_args, array('number'=>-1));
	extract($widget_args, EXTR_SKIP);	

	$options = $mgm_sidebar_widget->text_widget;
	
	if (!isset($options[$number])) {
		return;
	}	
	
	$available_to    = explode('|', $options[$number]['access_membership_types']);
	$membership_type = strtolower(mgm_get_user_membership_type(false, 'code'));

	$access = false;
	foreach ($available_to as $available) {
		if ($membership_type == strtolower($available)) {
			$access = true;
			break;
		}
	}

	// has access
	if ($access) {
		$title = apply_filters('mgm_sidebar_widget_text_title', $options[$number]['title']);
		$text  = apply_filters('mgm_sidebar_widget_text_text', $options[$number]['text']);
		?>
		<?php echo $before_widget; ?>
		<?php if (!empty($title)) { echo $before_title . $title . $after_title; } ?>
		<div class="textwidget"><?php echo $text; ?></div>
		<?php echo $after_widget;
	}
}

// text admin widget
function mgm_sidebar_widget_text_admin($widget_args=1) {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	static $updated = false;

	if (is_numeric($widget_args))
		$widget_args = array('number'=>$widget_args);
		
	$widget_args = wp_parse_args($widget_args, array('number'=>-1));
	extract($widget_args, EXTR_SKIP);	

	$options = $mgm_sidebar_widget->text_widget;
	if (!is_array($options)) {
		$options = array();
	}

	if (!$updated && !empty($_POST['sidebar'])) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if (isset($sidebars_widgets[$sidebar])) {
			$this_sidebar =& $sidebars_widgets[$sidebar];
		} else {
			$this_sidebar = array();
		}

		foreach ($this_sidebar as $_widget_id) {
			if ('mgm_widget_text' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if (!in_array("text-$widget_number", $_POST['widget-id'])) {// the widget has been removed.
					unset($options[$widget_number]);
				}
			}
		}

		foreach ((array)$_POST['mgm_widget_text'] as $widget_number=>$mgm_widget_text) {
			if (!isset($mgm_widget_text['text']) && isset($options[$widget_number])) {// user clicked cancel
				continue;
			}
			
			$title = strip_tags(stripslashes($mgm_widget_text['title']));

			if (current_user_can('unfiltered_html')) {
				$text = stripslashes($mgm_widget_text['text']);
			} else {
				$text = stripslashes(wp_filter_post_kses($mgm_widget_text['text']));
			}

			$access_membership_types = implode('|', $mgm_widget_text['access_membership_types']);

			$options[$widget_number] = compact('title', 'text', 'access_membership_types');
		}
		// set
		$mgm_sidebar_widget->text_widget = $options;
		// update_option('mgm_sidebar_widget', $mgm_sidebar_widget);
		$mgm_sidebar_widget->save();
		// updated
		$updated = true;
	}
	
	// get available membership types
	$membership_types = mgm_get_class('membership_types')->membership_types;	
	// selected
    $selected_membership_types = array();
	// get selected	
	if (-1 == $number) {
		$number = '%i%';
		$title  = '';
		$text   = '';		
		$selected_membership_types = implode(';', $membership_types);
	} else {
		$title = attribute_escape($options[$number]['title']);
		$text  = format_to_edit($options[$number]['text']);
		if(isset($options[$number]['access_membership_types'])){
			$selected_membership_types = explode('|',$options[$number]['access_membership_types']);
		}
	}

	echo '<p>'.__('Available to','mgm').':<br />';
	
	foreach ((array)$membership_types as $type_code=>$type_name) {
		if(is_array($selected_membership_types)){
			$c = (in_array($type_code, $selected_membership_types) ? 'checked="checked"':'');
		}else{
			$c ='';
		}

		echo '<input type="checkbox" id="mgm_widget_text_' . $number . '" class="checkbox" name="mgm_widget_text[' . $number . '][access_membership_types][]" value="' . $type_code . '" ' . $c . ' />
			  &nbsp;&nbsp;<label style="font-style:italic;" for="' . __($type_code) . '">' . __($type_name) . '</label>&nbsp;&nbsp;';
	}

	echo '</p>';
	?>
	<p>
		<label><?php _e('Title','mgm')?>:</label> 		
		<input class="widefat" id="mgm_widget_text_<?php echo $number; ?>" name="mgm_widget_text[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label><?php _e('Text','mgm')?>: </label>
		<textarea class="widefat" rows="16" cols="20" id="mgm_widget_text_<?php echo $number; ?>" name="mgm_widget_text[<?php echo $number; ?>][text]"><?php echo $text; ?></textarea>
		<input type="hidden" name="mgm_widget_text[<?php echo $number; ?>][submit]" value="1" />
	</p>
	<?php
}
// hooks
// wp_register_sidebar_widget('mgm_sidebar_widget_text', __('Magic Members Text','mgm'), 'mgm_sidebar_widget_text');
// wp_register_widget_control('mgm_sidebar_widget_text', __('Magic Members Text','mgm'), 'mgm_sidebar_widget_text_admin',  array('width'=>400));

// register multiple instance
function mgm_sidebar_widget_text_register() {
	global $wp_registered_widgets, $mgm_sidebar_widget;
	
	// $options[$number]['title']
	if ( !$options = $mgm_sidebar_widget->text_widget )
		$options = array();
	
	// defaults
	$widget_ops  = array();
	$control_ops = array('width' => 400, 'id_base' => 'mgm_sidebar_widget_text');
	
	// widget name
	$name = __('Magic Members Text','mgm');
	$registered = false;
	foreach ( array_keys($options) as $o ) {
	// Old widgets can have null values for some reason
		if (isset($options[$o]['title']) ) {
			$registered = true;
			$id = "mgm_sidebar_widget_text-$o"; 
			// register			
			wp_register_sidebar_widget( $id, $name, 'mgm_sidebar_widget_text', $widget_ops, array( 'number' => $o ) );
			wp_register_widget_control( $id, $name, 'mgm_sidebar_widget_text_admin', $control_ops, array( 'number' => $o ) );
		}
	}
	// If there are none, we register the widget's existence with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'mgm_sidebar_widget_text-1', $name, 'mgm_sidebar_widget_text', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'mgm_sidebar_widget_text-1', $name, 'mgm_sidebar_widget_text_admin', $control_ops, array( 'number' => -1 ) );
	}
}
// add
mgm_sidebar_widget_text_register();

// END text widget -----------------------------------------------------------------------------------------------


// ---------------------------------------------------------------------------------------------------------------
// other internal functions



// purchased posts
function mgm_render_my_purchased_posts($user_id, $sidebar=true, $return=false) {
	global $wpdb;

	$html = '';
	
	$prefix = $wpdb->prefix;
	$sql = 'SELECT pp.post_id, p.post_title AS title
			FROM `' . TBL_MGM_POSTS_PURCHASED . '` pp 
			JOIN ' . $prefix . 'posts p ON (p.id = pp.post_id)
			WHERE pp.user_id = ' . $user_id;
	//echo $sql;		
	$results = $wpdb->get_results($sql,'ARRAY_A');

	if (!$sidebar) {
		if (count($results[0])) {
			$html .= '<table><tr><td>Post Title</td></tr>';

			foreach ($results as $result) {
				$link = get_permalink($result['post_id']);
				$title = $result['title'];
				if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
					$title = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
				}

				$html .= '<tr><td><a href="' . $link . '">' . $title . '</a></td></tr>';
			}

			$html .= '</table>';

		}
	} else {
		if (count($results[0])) {
			$html .= '<div style="border-bottom: 1px solid #EFEFEF; font-weight:bold; width: 100%;">Purchased Posts</div>';
			
			foreach ($results as $result) {
				$link = get_permalink($result['post_id']);

				$title = $result['title'];
				if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
					$title = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
				}

				$html .= '<div><a href="' . $link . '">' . $title . '</a></div>';
			}
		}
	}
	
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}
?>