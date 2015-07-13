<?php 
// user hooks
// sanitize_user
// add_filter('sanitize_user'     , 'mgm_sanitize_user', 20);
// validate_username
// add_filter('validate_username' , 'mgm_validate_username');
// register form custom fields
add_action('register_form'        , 'mgm_register_form_extend'); 
// buddypress fix
if(defined('BP_VERSION')){
	// add_action('bp_after_account_details_fields', 'mgm_register_form_extend'); // form 
	// issue #: 413	
	if(mgm_check_theme_register()) {
		// issue #: 248		
		add_action('bp_after_signup_profile_fields', 'mgm_register_form_extend'); // register form custom fields resplace
	}else {
		//remove buddypress redirection if register template is not found
		add_action( 'wp', 'mgm_disable_bp_redirection',100 );
		remove_action( 'wp', 'bp_core_catch_no_access' );
		add_action( 'template_redirect', 'redirect_canonical',10 );
	}
}
// not for users added via admin
//if(!is_admin()){
	add_action('register_post'    , 'mgm_register_post', 10, 3);// after post
	add_action('user_register'    , 'mgm_register', 10, 1); // after register
//}
add_filter('wp_authenticate_user' , 'mgm_authenticate', 20);	
add_action('mgm_attach_scripts'   , 'mgm_attach_scripts');// custom where ever neede
add_action('login_head'           , 'mgm_attach_scripts');// on wp-login.php only
add_action('profile_update'       , 'mgm_save_custom_fields');// user profile custom fields save callback
add_action('show_user_profile'    , 'mgm_show_custom_fields');// user profile custom fields display
add_action('edit_user_profile'    , 'mgm_edit_custom_fields');// user profile custom fields display/edit
// login redirect
// add_filter('login_redirect'    , 'mgm_login_form_redirect', 1);
add_action('wp_login'             , 'mgm_login_redirect', 20);
add_filter('user_contactmethods'  , 'mgm_updte_contactmethods');//profile:contactmethods
// autoresponder always on payment return
add_action('mgm_return_subscription_payment'            , 'mgm_autoresponder_subscribe');
// check and reset member canceled flag for renewal
add_action('mgm_subscription_purchase_payment_success'  , 'mgm_reset_cancelled_member');
add_filter('mgm_validate_reset_password'				, 'mgm_validate_reset_password', 10, 2);//reset password validation
add_action('mgm_reset_password'							, 'mgm_reset_password', 10, 2);//reset password
add_action('mgm_logout'									, 'mgm_logout');//custom logout hook
add_filter('logout_url'									, 'mgm_logout_url',10,2);//custom logout hook
add_filter('login_url'									, 'mgm_login_url',10,2);//custom login hook
add_filter('get_avatar'									, 'mgm_get_avatar',10,2);
add_action('delete_user'								, 'mgm_delete_user');//just before delete
add_filter('mgm_reassign_member_subscription'			, 'mgm_reassign_member_subscription',10,4);//reset membership
add_filter('cron_schedules'								, 'mgm_custom_schedules');
// ////////////////////////////////////////////////////////////////////////////////////////////////
// mgm_register_form_extend
function mgm_register_form_extend($return=false,$form_fields=NULL){				
	// get mgm_system
	$mgm_system = mgm_get_class('system');
	// no custom fields
	// if($mgm_system->setting['hide_custom_fields'] == 'Y') return;
	$hide_custom_fields = ($mgm_system->setting['hide_custom_fields'] == 'Y') ? true : false;
	
	// do not repeat if already called, iss #383 related
	if(!$form_fields){
		// get custom fields on register page
		$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true)));	
	
		// wordpress register
		$wordpres_form = mgm_check_wordpress_login();
		
		// 	get row row template
		$form_row_template = $mgm_system->get_template('register_form_row_template');
		
		// get template row filter, mgm_register_form_row_template for custom, mgm_register_form_row_template_wordpress for wordpress
		$form_row_template = apply_filters('mgm_register_form_row_template'.($wordpres_form ? '_wordpress': ''), $form_row_template);
		
		// get mgm_form_fields generator
		$form_fields = & new mgm_form_fields(array('wordpres_form'=>$wordpres_form,'form_row_template'=>$form_row_template));
	}else{
		// wordpress register
		$wordpres_form = $form_fields->get_config('wordpres_form');
		// 	get row row template
		$form_row_template = $form_fields->get_config('form_row_template');
		// cf_register_page		
		$cf_register_page = $form_fields->get_config('cf_register_page');
	}	
	
	// add password_conf dynamic field
	// $cf_register_page = array_push($cf_register_page, $form_fields->_get_password_conf_field());	
	// form_template
	$form_template = '';
	
	// loop to create form template
	foreach($cf_register_page as $field){
		// skip custom fields by settings call
		if($hide_custom_fields && $field['name'] != 'subscription_options') continue;
			
		// field wrapper
		$wrapper_ph = sprintf('[user_field_wrapper_%s]',$field['name']);	
		// field label 
		$label_ph = sprintf('[user_field_label_%s]',$field['name']);		
		// field/html element
		$element_ph = sprintf('[user_field_element_%s]',$field['name']);
		// template for autoresponder// TODO check template for each individual field in theme folder
		if($field['name'] == 'autoresponder'){
			// template
			$form_row_template_ar = $mgm_system->get_template('register_form_row_autoresponder_template');
			// set element place holders
			$form_template .= str_replace(array('[user_field_wrapper]','[user_field_label]','[user_field_element]'),array($wrapper_ph,$label_ph,$element_ph),$form_row_template_ar);
		}else{
			// set element place holders
			$form_template .= str_replace(array('[user_field_wrapper]','[user_field_label]','[user_field_element]'),array($wrapper_ph,$label_ph,$element_ph),$form_row_template);
		}	
		
	}
	
	// get template filter, mgm_register_form_template for custom, mgm_register_form_template_wordpress for wordpress
	$form_template = apply_filters('mgm_register_form_template'.($wordpres_form ? '_wordpress': ''), $form_template);

	// now replace and create the fields
	// form_html
	$form_html = $form_template;	

	$arr_images = array();
	
	// mgm_pr($cf_register_page);
	
	// loop to create form template
	foreach($cf_register_page as $field){
		// skip custom fields by settings call
		//if($hide_custom_fields && $field['name'] != 'subscription_options') continue;
		if($hide_custom_fields && !in_array($field['name'], array('subscription_options','payment_gateways'))) continue;
		//image field:
		if($field['type'] == 'image')
			if(!in_array($field['name'], $arr_images ))	
				$arr_images[] = $field['name'];	
				
		// field wrapper
		$wrapper_ph = sprintf('[user_field_wrapper_%s]',$field['name']);
		// field label
		$label_ph = sprintf('[user_field_label_%s]',$field['name']);
		// field/html element
		$element_ph = sprintf('[user_field_element_%s]',$field['name']);
		// replace wrapper
		$form_html = str_replace($wrapper_ph, $field['name'].'_box', $form_html);
		// replace label
		$form_html = str_replace($label_ph, ($field['attributes']['hide_label']?'': mgm_stripslashes_deep($field['label'])), $form_html);
		// replace element
		$form_html = str_replace($element_ph, $form_fields->get_field_element($field, 'mgm_register_field'), $form_html);
	}
	
	// log	
	$yearRange = mgm_get_calendar_year_range();
	
	// append script
	$form_html .= '<script language="javascript">jQuery(document).ready(function(){try{mgm_date_picker(".mgm_date",false,{yearRange:"'.$yearRange.'"});}catch(x){}});</script>';	
		
	$form_action = mgm_get_custom_url('register');
	
	$membership = mgm_request_var('membership');	
	if(!empty($membership)) {
		$form_action = add_query_arg(array('membership' => $membership), $form_action);
	}
	
	$package = mgm_request_var('package');	
	if(!empty($package)) {
		$form_action = add_query_arg(array('package' => $package), $form_action);
	}
	if(!empty($package) || !empty($membership)) {
		//issue#: 482		
		$form_html .= '<script language="javascript">jQuery(document).ready(function(){jQuery(\'#registerform\').attr(\'action\',\''.$form_action.'\')});</script>';
	}
	
	//include scripts for image upload:
	if(!empty($arr_images)) {
		$form_html .= mgm_attach_scripts(true, array());
		$form_html .= mgm_upload_script_js('registerform', $arr_images);
	}
	// print 
	if($return) return $form_html;
	
	// print
	print $form_html; 	
}
// mgm_register_post, validate required custom fields
function mgm_register_post($sanitized_user_login = '', $user_email = '',$errors = null) {
	// no custom fields
	// if(mgm_get_class('system')->setting['hide_custom_fields'] == 'Y') return;	
	// get mgm_system
	$mgm_system = mgm_get_class('system');
	$hide_custom_fields = ($mgm_system->setting['hide_custom_fields'] == 'Y') ? true : false;
	if(is_null($errors)) $errors = new WP_Error();
	// errors
	$error_codes = $errors->get_error_codes();
	// user_login
	if(array_key_exists('user_login', $_POST) ) {			
		$sanitized_user_login = sanitize_user($_POST['user_login'] );			
		if ( $sanitized_user_login == '' ) {
			if(!in_array('empty_username', $error_codes))
				$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
		} elseif ( ! validate_username( $_POST['user_login'] ) ) {
			if(!in_array('invalid_username', $error_codes))
				$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
			$sanitized_user_login = '';
		} elseif ( ! mgm_validate_username( $sanitized_user_login ) ) {
			if(!in_array('invalid_username', $error_codes))
				$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters, spaces are not allowed. Please enter a valid username.' ) );
			$sanitized_user_login = '';		
		} elseif ( username_exists( $sanitized_user_login ) ) {
			if(!in_array('username_exists', $error_codes))
				$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.' ) );
		}			
	}
	
	// user_email
	if(array_key_exists('user_email', $_POST) ) {
		$user_email = apply_filters( 'user_registration_email', $_POST['user_email'] );
		// Check the e-mail address
		if ( $user_email == '' ) {
			if(!in_array('empty_email', $error_codes))
				$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
		} elseif ( ! is_email( $user_email ) ) {
			if(!in_array('invalid_email', $error_codes))
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			if(!in_array('email_exists', $error_codes))
				$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
		}
	}	
	// get custom fields	
	$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true)));	
	// loop
	foreach($cf_register_page as $field){		
		// skip custom fields by settings call
		//if($hide_custom_fields && $field['name'] != 'subscription_options') continue;
		if($hide_custom_fields && !in_array($field['name'], array('subscription_options','payment_gateways'))) continue;
		
		// skip default fields, validated already
		if(($field['name'] == 'username') || ($field['name'] == 'email' )) continue;
			
		// skip no type
		if ($field['name'] == 'terms_conditions') {
			if (!isset($_POST['mgm_tos'])) {
				$errors->add('mgm_tos',  __('<strong>ERROR</strong>: You must accept the Terms and Conditions.','mgm'));
			}
		}else if($field['name'] == 'subscription_options'){
			if ( (!isset($_POST['mgm_subscription'])) || (empty($_POST['mgm_subscription'])) ) {
				$errors->add('mgm_subscription', __('<strong>ERROR</strong>: You must select a Subscription Type.','mgm'));
			}
		}
		//validate payment gateways:
		else if($field['name'] == 'payment_gateways'){
			if ( isset($_POST['mgm_subscription'])) { 				
				$sub_pack = mgm_decode_package($_POST['mgm_subscription']);				
				if(isset($sub_pack['pack_id'])) {
					$pack = mgm_get_class('subscription_packs')->get_pack($sub_pack['pack_id']);
					$arr_modules = array_diff($pack['modules'], array('mgm_free', 'mgm_trial'));					
					if(!empty($arr_modules) && (!isset($_POST['mgm_payment_gateways']) || (isset($_POST['mgm_payment_gateways']) && empty($_POST['mgm_payment_gateways'])))) {
						$errors->add('mgm_subscription', __('<strong>ERROR</strong>: You must select a Payment Gateway.','mgm'));	
					}
				}				
			}
		}else if($field['type'] == 'captcha'){
			if ( (!isset($_POST['recaptcha_response_field'])) || (empty($_POST['recaptcha_response_field'])) ) {
				$errors->add('mgm_captcha', __('<strong>ERROR</strong>: You must enter the Captcha String.','mgm'));
			}else {
				
				$captcha = mgm_get_class('recaptcha')->recaptcha_check_answer($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'] );				
				if(!isset($captcha->is_valid) || !$captcha->is_valid ) {					
					$errors->add('mgm_captcha', __('<strong>ERROR</strong>: '.(!empty($captcha->error) ? $captcha->error : 'The Captcha String isn\'t correct.') ,'mgm'));	
				}
			}
		}else{								
			// check register and required		
			if((bool)$field['attributes']['required'] === true){		
				// error
				$error_codes = $errors->get_error_codes();
				// validate other				
				// confirm password
				if($field['name'] == 'password' || $field['name'] == 'password_conf' ){					
					if ( ($field['name'] == 'password' && (!isset($_POST['user_password']) || empty($_POST['user_password']))) || 
							( $field['name'] == 'password_conf' && (!isset($_POST['user_password_conf']) || empty($_POST['user_password_conf'])))
						){						
						$errors->add($field['name'], __('<strong>ERROR</strong>: You must provide a '.mgm_stripslashes_deep($field['label']).'.','mgm'));
						
					}								
					elseif($field['name'] == 'password' && !empty($_POST['user_password']) && !empty($_POST['user_password_conf']) && $_POST['user_password'] != $_POST['user_password_conf'] ){
						$errors->add($field['name'], __('<strong>ERROR</strong>: Passwords does not match.','mgm'));
					}	
				}else{
					if ( (!isset($_POST['mgm_register_field'][$field['name']])) || (empty($_POST['mgm_register_field'][$field['name']])) ) {
						$errors->add($field['name'], __('<strong>ERROR</strong>: You must provide a '.mgm_stripslashes_deep($field['label']).'.','mgm'));
					}
				}				
			}
		}
	}		
	// return	
	return $errors;
}
// register after process
function mgm_register($user_id){		
	// get mgm_system
	$mgm_system = mgm_get_class('system');
	$hide_custom_fields = ($mgm_system->setting['hide_custom_fields'] == 'Y') ? true : false;
	// custom fields 
	// if($mgm_system->setting['hide_custom_fields'] == 'Y') return $user_id;
	
	// globals
	global $wpdb, $post;
	get_currentuserinfo();	
	
	// members object
	$mgm_member = mgm_get_member($user_id);	
	// set status
	$mgm_member->set_field('status', MGM_STATUS_NULL);
	// get custom fields	
	$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true)));
	// mgm_subscription
	$mgm_subscription = @$_POST['mgm_subscription'];
	// get subs 				
	$subs_pack = mgm_decode_package($mgm_subscription);
	extract($subs_pack);
	//payment_gateways if set:
	//Eg: $_POST['mgm_payment_gateways'] = mgm_paypal
	$cf_payment_gateways = (isset($_POST['mgm_payment_gateways']) && !empty($_POST['mgm_payment_gateways'])) ? $_POST['mgm_payment_gateways'] : null;
	// init
	$member_custom_fields = array();
	// loop
	foreach($cf_register_page as $field){
		// skip custom fields by settings call
		if($hide_custom_fields && $field['name'] != 'subscription_options') continue;
		//skip if payment_gateways custom field
		if($field['name'] == 'payment_gateways') continue;
		//
		// do not save html
		if($field['type']=='html' || $field['type']=='label') continue;				
		// save
		switch($field['name']){			
			case 'username':				
				$member_custom_fields[$field['name']] = @$_POST['user_login'];
			break;	
			case 'email':				
				$member_custom_fields[$field['name']] = @$_POST['user_email'];
			break;
			case 'password':			
				$member_custom_fields[$field['name']] = $user_password = $_POST['user_password'];
			break;	
			case 'autoresponder':										
				// checked
				if(in_array($_POST['mgm_register_field'][$field['name']], array('Y','y','Yes','YES','yes'))){			
					// set to member, to be used on payment
					$mgm_member->subscribed    = 'Y';
					$mgm_member->autoresponder = $mgm_system->active_modules['autoresponder'];
				}						
			break;
			case 'coupon':
				// validate
				$coupon = mgm_validate_coupon(@$_POST['mgm_register_field'][$field['name']], $cost);
				// valid
				if($coupon!==false){
					// set
					$mgm_member->coupon = $coupon;			
					// update coupon usage							
					$wpdb->query(sprintf("UPDATE `%s` SET `used_count` = IF(used_count IS NULL, 1, used_count+1) WHERE id='%d'", TBL_MGM_COUPON, $coupon['id']));							
				}
			break;										
			default:
				$member_custom_fields[$field['name']] = @$_POST['mgm_register_field'][$field['name']];					
			break;
		}
	}// end fields save	
	
	// mgm_log($member_custom_fields);
	// mgm_log($_POST);
	
	// user password not provided
	if (!isset( $user_password )){
		$user_password = (isset($_POST['pass1']) && !empty($_POST['pass1'])) ? trim($_POST['pass1']) : substr(md5(uniqid(microtime())), 0, 7);
	}	
	// set to member
	$mgm_member->user_password = $user_password;
	// md5			
	$user_password_md5 = md5($user_password);	
	// db update
	$wpdb->query("UPDATE `{$wpdb->users}` SET `user_pass` = '{$user_password_md5}' WHERE ID = '{$user_id}'");	
	// set custom
	$mgm_member->set_custom_fields($member_custom_fields);
	// set pack
	$mgm_member->pack_id = $pack_id; // from mgm_subscription
	// update option	
	// update_user_option($user_id,'mgm_member',$mgm_member,true);
	$mgm_member->save();
	// update user firstname/last name
	mgm_update_default_userdata($user_id);	
	$is_admin = is_admin();
	$send_touser = true;
	// send notification
	if(!isset($_POST['send_password']) && $is_admin)
		$send_touser = false;
	if($send_touser)	
		mgm_new_user_notification($user_id,$user_password, ($is_admin?false:true));	
	// action for after register
	do_action('mgm_user_register',$user_id);	
	
	// process payment only when registered from site, not when user added by admin
	if($is_admin){
		unset($_POST['send_password']);//prevent sending user email again
		return $user_id;
	}
	
	// if on wordpress page or custompage	
	$post_id = get_the_ID();
	// post custom register		
	if($post_id > 0 && $post->post_type == 'post'){
	//if($post_id > 0){
		$redirect =	get_permalink($post_id);
	}else{
		$redirect = mgm_get_custom_url('transactions');
	}
	
	// userdata
	$userdata = get_userdata($user_id);			
	// note this fix VERY IMPORTANT, needed for PAYPAL PRO CC POST
	$redirect = add_query_arg(array('username'=>urlencode($userdata->user_login)),$redirect);	
	// add redirect
	if ($redirector = mgm_request_var('mgm_redirector', mgm_request_var('redirect_to'))){ 
		$redirect = add_query_arg(array('redirector'=>$redirector), $redirect);
	}	
	// with subscription	            
	if ($mgm_subscription){ 
		$redirect = add_query_arg(array('subs'=>$mgm_subscription,'method'=>'payment_subscribe'), $redirect);                       		
	}
	
	
	//bypass step2 if payment gateway is submitted: issue #: 469
	if(!is_null($cf_payment_gateways)) {				
		$packs_obj = mgm_get_class('subscription_packs');
		// validate			
		$pack = $packs_obj->validate_pack($cost, $duration, $duration_type, $membership_type, $pack_id);		
		// error
		if($pack != false) {
			$mgm_member->coupon = (array) $mgm_member->coupon;
			if(isset($mgm_member->coupon['id'])){				
				// main 		
				if($pack && $mgm_member->coupon['cost']){
					// original
					$pack['original_cost'] = $pack['cost'];
					// payable
					$pack['cost'] = $mgm_member->coupon['cost'];
				}	
				
				if($pack && $mgm_member->coupon['duration'])
					$pack['duration'] = $mgm_member->coupon['duration'];
				if($pack && $mgm_member->coupon['duration_type'])
					$pack['duration_type'] = $mgm_member->coupon['duration_type'];
				if($pack && $mgm_member->coupon['membership_type'])
					$pack['membership_type'] = $mgm_member->coupon['membership_type'];
				//issue#: 478/ add billing cycles.	
				if($pack && isset($mgm_member->coupon['num_cycles']))
					$pack['num_cycles'] = $mgm_member->coupon['num_cycles'];	
				
				// trial	
				if($pack && $mgm_member->coupon['trial_on'])
					$pack['trial_on'] = $mgm_member->coupon['trial_on'];
				if($pack && $mgm_member->coupon['trial_cost'])
					$pack['trial_cost'] = $mgm_member->coupon['trial_cost'];
				if($pack && $mgm_member->coupon['trial_duration_type'])
					$pack['trial_duration_type'] = $mgm_member->coupon['trial_duration_type'];
				if($pack && $mgm_member->coupon['trial_duration'])
					$pack['trial_duration'] = $mgm_member->coupon['trial_duration'];	
				if($pack && $mgm_member->coupon['trial_num_cycles'])
					$pack['trial_num_cycles'] = $mgm_member->coupon['trial_num_cycles'];	
					
				// mark pack as coupon applied
				$pack['coupon_id'] = $mgm_member->coupon['id'];				
			}
			
			if ((float)$pack['cost'] > 0) {
				//get an object of the payment gateway:
				$mod_obj = mgm_get_module($cf_payment_gateways,'payment');
				// create transaction				
				$tran_id = $mod_obj->_create_transaction($pack, array('is_registration'=>true, 'user_id' => $user_id));
				//bypass directly to process return if manual payment:				
				if($cf_payment_gateways == 'mgm_manualpay') {
					$_POST['custom'] = $tran_id;
					//direct call to module return function:
					$mod_obj->process_return();					
					exit;
				}
				//encode id:
				$tran_id = mgm_encode_id($tran_id);
				$redirect = $mod_obj->_get_endpoint('html_redirect', true);	
				$redirect = add_query_arg(array( 'tran_id' => $tran_id ), $redirect); 							
			}
		}
	}
	//ends custom payment gateway bypassing 	
	
	// is secure
	$redirect = mgm_ssl_url($redirect);	
			
	// redirect	
	mgm_redirect($redirect);// this goes to subscribe, mgm_functions.php/mgm_get_subscription_buttons
	// exit						
	exit;
}
// authenticate
function mgm_authenticate($user, $return=false){
	//remove:	
	// user name
	$user_name = $user->user_login;	
	
	// user is administrator, no check applied
	if (is_super_admin($user->ID)) {
		return ($return ? true : $user);
	}

	// other user
	$mgm_member = mgm_get_member($user->ID);
	//mgm_array_dump($mgm_member);
	// no status
	if ($mgm_member->status === false) {
		return ($return ? true : $user); 
	}
	
	// active
	if ($mgm_member->status == MGM_STATUS_ACTIVE) {		
		// no expire
		if (empty($mgm_member->expire_date)) {
			return ($return ? true : $user);
		}
		// check expire
		//if (date('Y-m-d') > $mgm_member->expire_date) {
		if (strtotime(date('Y-m-d')) > strtotime($mgm_member->expire_date)) {
			// set expired
			$mgm_member->status = MGM_STATUS_EXPIRED;
			// update
			// update_user_option($user->ID,'mgm_member',$mgm_member, true);			
			$mgm_member->save();
			// trial
			if (strtolower($mgm_member->membership_type) == 'trial') {
				$mgm_member->status = MGM_STATUS_TRIAL_EXPIRED;
			} else {
				$mgm_member->status = MGM_STATUS_EXPIRED;
			}
		} else {
			return ($return ? true : $user); // account is current. Let the user login.
		}
	}else {
		
		//multiple membership (issue#: 400) modification
		$others_active = 0;		
		//check any other membership exists with active status
		if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types) ) {
			foreach ($mgm_member->other_membership_types as $key => $mem_obj) {
				$mem_obj = mgm_convert_array_to_memberobj($mem_obj, $user->ID);
				if(is_numeric($mem_obj->pack_id) && $mem_obj->status == MGM_STATUS_ACTIVE){
					//check for expiry
					if ( !empty($mem_obj->expire_date) && strtotime(date('Y-m-d')) > strtotime($mem_obj->expire_date)) {
						// set expired
						$mem_obj->status = MGM_STATUS_EXPIRED;
						// update member object							
						mgm_save_another_membership_fields($mem_obj, $user->ID);								
					}else 
						$others_active++;								
				}					
			}
			// one of the other memberships is active. Let the user login.
			if($others_active > 0) {
				return ($return ? true : $user); 
			}
		}	
			
	}

	// process error
	$mgm_system = mgm_get_class('system');	
	
	// error
	$error_messages = array(MGM_STATUS_NULL          => mgm_stripslashes_deep($mgm_system->get_template('login_errmsg_null', array(), true)),
							MGM_STATUS_TRIAL_EXPIRED => mgm_stripslashes_deep($mgm_system->get_template('login_errmsg_trial_expired', array(), true)),
							MGM_STATUS_EXPIRED       => mgm_stripslashes_deep($mgm_system->get_template('login_errmsg_expired', array(), true)),
							MGM_STATUS_PENDING       => mgm_stripslashes_deep($mgm_system->get_template('login_errmsg_pending', array(), true)),
							MGM_STATUS_CANCELLED     => mgm_stripslashes_deep($mgm_system->get_template('login_errmsg_cancelled', array(), true)),
							'ANY'                    => mgm_stripslashes_deep($mgm_system->get_template('login_errmsg_default', array(), true)));

	// error
	$error = (isset($error_messages[$mgm_member->status]) ? $error_messages[$mgm_member->status] : $error_messages['ANY']);
	$error = str_replace('[[USERNAME]]', $user_name, $error);
	
	// set edit cookie
	if ( wp_check_password($_POST['pwd'], $user->user_pass, $user->ID) ){
		setcookie('wp-tempuser-edit' , $user->ID, (time() + (60*60)), SITECOOKIEPATH);// 1 hr
	}
	
	// check subscription status
	$err = new WP_Error();
	// add
	$err->add('mgm_login_error', $error);
	// return
	return ($return ? false : $err);
}

// new user email
function mgm_new_user_notification($user_id, $user_pass='',$sendto_admin=true){
	// mgm_system	
	$mgm_system = mgm_get_class('system');		
	// template
	$template_subject = mgm_stripslashes_deep($mgm_system->get_template('registration_email_template_subject', array(), true));
	$template_body    = mgm_stripslashes_deep($mgm_system->get_template('registration_email_template_body', array(), true));	
	// default
	if(empty($template_body)){
		 wp_new_user_notification($user_id, $user_pass);	
	}else{
		// get user
		$user = new WP_User($user_id);	
		// mgm member
		$mgm_member = mgm_get_member($user_id);
		// user data
		$user_login = stripslashes($user->user_login);
	    $user_email = stripslashes($user->user_email);		
		if($sendto_admin) {
			// send to admin 
			$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "<br/><br/>";
			$message .= sprintf(__('Username: %s'), $user_login) . "<br/><br/>";
			$message .= sprintf(__('E-mail: %s'), $user_email) . "<br/><br/>";
			
			// mail	
			@mgm_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
		}
		
		// no pass
		if ( empty($user_pass) ) return;

		// first name
		if($user->first_name){
			$display_name = $user->first_name;
		}elseif($mgm_member->custom_fields->first_name){
			$display_name = $mgm_member->custom_fields->first_name;
		}else{					
		 	$display_name = $user->display_name;
		}
		// format
		$display_name = stripslashes($display_name);	
			
		// subject
		if($template_subject!='')
		 	$subject = $template_subject;
		else		
		    $subject = sprintf(__('[%s] Your username and password'), get_option('blogname'));
		 
		// body
		$message = $template_body;	
		$message = str_replace('[name]',$display_name,$message);
		$message = str_replace('[username]',$user_login,$message);
		$message = str_replace('[password]',$user_pass,$message);
		// $message = str_replace('[expire_date]',$expire_date,$message);
		$message = str_replace('[login_url]',sprintf('<a href="%s">Profile</a>',wp_login_url()),$message);		 
		// send
		mgm_mail($user_email, $subject, $message);
	}	
}

// login head 
function mgm_attach_scripts($return = false, $exclude = array('jquery.ajaxfileupload.js')){ 	
	global $mgm_scripts;		
	// wp login form 
	$wordpres_login_form = mgm_check_wordpress_login();
	// int css array
	$css_files = array();
	// subscribe page css, loaded from wp-admin
	/*
	if( get_query_var('subscribe') || get_query_var('payment_return')):
		$css_files[] = admin_url('/css/login.css');
		$css_files[] = admin_url('/css/colors-fresh.css'); 
	endif; 
	*/
	// other, loaded from mgm custom
	$css_files[] = MGM_ASSETS_URL . 'css/mgm_form_fields.css'; 
	$css_files[] = MGM_ASSETS_URL . 'css/mgm_site.css';
	$css_files[] = MGM_ASSETS_URL . 'css/mgm_cc_fields.css';
	$css_files[] = MGM_ASSETS_URL . 'css/mgm/jquery-ui.css';  
	// disable
	$disable_jquery = false;
	//this is for blocking loading jquery externally, to disable jquery add_filter and modify disable_jquery to return true 	
	$disable_jquery = apply_filters('disable_jqueryon_page', $disable_jquery);	
	// init js array
	$js_files = array();	
	$arr_default_pages = array('wp-login.php', 'user-edit.php', 'profile.php');
	$default_page = (in_array(basename($_SERVER['SCRIPT_FILENAME']), $arr_default_pages )) ? true : false ;
	
	// jquery from wp distribution	
	if(($default_page && !in_array('jquery.js', (array)$mgm_scripts )) || (!wp_script_is('jquery') && !$disable_jquery)) {		
		if(($default_page && !in_array('jquery.js', (array)$mgm_scripts )) || !mgm_is_script_already_included('jquery.js')) {			
			$js_files[] = includes_url( '/js/jquery/jquery.js');				
			$mgm_scripts[] = 'jquery.js';
		}		
	}
	// custom
	//if(!wp_script_is('mgm-jquery-validate'))
	//	if(!mgm_is_script_already_included('jquery.validate.pack.js')) {
		if(($default_page && !in_array('jquery.validate.pack.js', (array)$mgm_scripts )) || (!wp_script_is('mgm-jquery-validate') && !mgm_is_script_already_included(MGM_ASSETS_URL . 'js/jquery/jquery.validate.pack.js', true))) {
			$js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.validate.pack.js';
			$mgm_scripts[] = 'jquery.validate.pack.js';
		}
	//if(!wp_script_is('mgm-jquery-metadata'))	
	//	if(!mgm_is_script_already_included('jquery.metadata.js')) {
		if(($default_page && !in_array('jquery.metadata.js', (array)$mgm_scripts )) || (!wp_script_is('mgm-jquery-metadata') && !mgm_is_script_already_included(MGM_ASSETS_URL . 'js/jquery/jquery.metadata.js', true))) {
			$js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.metadata.js';
			$mgm_scripts[] = 'jquery.metadata.js';
		}
	//if(!wp_script_is('mgm-helpers'))	
	//	if(!mgm_is_script_already_included('helpers.js', true)) {
		if( ($default_page && !in_array('helpers.js', (array)$mgm_scripts )) || (!wp_script_is('mgm-helpers') && !mgm_is_script_already_included(MGM_ASSETS_URL . 'js/helpers.js', true)) ) {
			$js_files[] = MGM_ASSETS_URL . 'js/helpers.js';
			$mgm_scripts[] = 'helpers.js';
		}
	// ui on wp version			
	$jqueryui_version = mgm_get_jqueryui_version();	
	// add to array	
	//if(!wp_script_is('mgm-jquery-ui')) {		
		//if(!mgm_is_script_already_included('jquery-ui-'.$jqueryui_version.'.min.js')) {
		if( ($default_page && !in_array('jquery-ui-'.$jqueryui_version.'.min.js', (array)$mgm_scripts )) || ( !wp_script_is('mgm-jquery-ui') && !mgm_is_script_already_included('jquery-ui-'.$jqueryui_version.'.min.js'))) {
			$js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.ui/jquery-ui-'.$jqueryui_version.'.min.js';
			$mgm_scripts[] = 'jquery-ui-'.$jqueryui_version.'.min.js';
		}			
	//}	
	//if(!wp_script_is('mgm-jquery-ajaxupload')) {		
	//	if(!mgm_is_script_already_included('jquery.ajaxfileupload.js')) {					
		if(($default_page && !in_array('jquery.ajaxfileupload.js', (array)$mgm_scripts )) || (!mgm_is_script_already_included(MGM_ASSETS_URL . 'js/jquery/jquery.ajaxfileupload.js', true) && !wp_script_is('mgm-jquery-ajaxupload') )) {			
			$js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.ajaxfileupload.js';
			$mgm_scripts[] = 'jquery.ajaxfileupload.js';
		}	
	//}
	// if(!wp_script_is('mgm-jquery-watermarkinput'))
		// $js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.watermarkinput.js';
	// init
	$scripts = '';	
	// css format
	$css_link_format = '<link rel="stylesheet" href="%s" type="text/css" media="all" />';
	// add
	foreach($css_files as $css_file){
		$scripts .= sprintf($css_link_format, $css_file);
	}	
	// js format
	$js_script_format = '<script type="text/javascript" src="%s"></script>';
	
	// add
	if($js_files)
		foreach($js_files as $js_file){
			$scripts .= sprintf($js_script_format, $js_file);
		}
	// return	
	if($return) 
		return $scripts;
	else		
		echo $scripts;	
}
// after login redirect
function mgm_login_redirect($user_login){
	// get user data	
	$userdata = get_user_by('login', $user_login);	
	// if not super admin	
	if(!is_super_admin($userdata->ID)) {		
		// get object
		//$mgm_membership_types = mgm_get_class('membership_types');		
		// membership_type
		//$membership_type = $userdata->mgm_member->membership_type;		
		// get membership level redirect
		//$login_redirect_url = $mgm_membership_types->get_login_redirect($membership_type);
		// get setting	
		$mgm_system = mgm_get_class('system');
			
		//allow redirecting back to post url:
		//issue #: 503			
		if(isset($_POST['redirect_to']) && !empty($_POST['redirect_to']) && isset($mgm_system->setting['enable_post_url_redirection']) && $mgm_system->setting['enable_post_url_redirection'] == 'Y' ) {
			$redirect_now = true;
			$custom_pages_url = $mgm_system->get_custom_pages_url(); 			
			foreach($custom_pages_url as $key=>$page_url){				
				if(!empty($page_url) && trailingslashit($_POST['redirect_to']) == trailingslashit($page_url)){					
					$redirect_now = false; 				
					break;
				}			
			}
			//OK
			if($redirect_now){				
				mgm_redirect($_POST['redirect_to']);
			}
		}		
		//issue# 464		
		$login_redirect_url = mgm_get_user_package_redirect_url($userdata->ID);
		// id set, redirecr
		if (!empty($login_redirect_url)) {			
			// redirect								
			mgm_redirect($login_redirect_url); exit();						
		}		
		// get redirect
		$redirect = trim($mgm_system->setting['login_redirect_url']);
		// set if
		if (!empty($redirect)) {			
			// redirect								
			mgm_redirect($redirect);	exit();						
		}
		// get redirect
		$redirect = trim($mgm_system->setting['profile_url']);
		// set if
		if (!empty($redirect)) {			
			// redirect							
			mgm_redirect($redirect);	exit();						
		}
	}else{		
		mgm_redirect(admin_url());		
	}
}

// set field value
function mgm_set_field_value($name, $key, $default=''){	
	// isset						
	if(isset($_POST[$name][$key])){
		return mgm_stripslashes_deep($_POST[$name][$key]);
	}							
	// return
	return mgm_stripslashes_deep($default);
}

// send autoresponder
function mgm_autoresponder_subscribe($args){	
	// check
	if(isset($args['user_id'])){
		// subscribe
		return mgm_autoresponder_send($args['user_id']);
	}
}
// update
function mgm_updte_contactmethods($methods) {	
	// issue#: 255(Disable contact methods)
	return array();
}
// reset cancelled member
function mgm_reset_cancelled_member($args){
	// check
	if(isset($args['user_id'])){
		// user_id
		$user_id = $args['user_id'];
		// get member
		//another_subscription modification
		if(isset($args['another_membership'])) 
			$mgm_member = mgm_get_member_another_purchase($user_id, $args['another_membership']);
		else 
			$mgm_member = mgm_get_member($user_id);
			
		// check
		if(isset($mgm_member->status_reset_as) && $mgm_member->status_reset_as == MGM_STATUS_CANCELLED){
			// unset
			unset($mgm_member->status_reset_as,$mgm_member->status_reset_on);
			// update user
			//another_subscription modification
			if(isset($args['another_membership'])) 
				mgm_save_another_membership_fields($mgm_member, $user_id);
			else 			
				// update_user_option($user_id, 'mgm_member', $mgm_member, true);	
				$mgm_member->save();			
		}		
	}	
}
//reset password validation hook
function mgm_validate_reset_password($key, $login) {
	global $wpdb;

	$key = preg_replace('/[^a-z0-9]/i', '', $key);

	if ( empty( $key ) || !is_string( $key ) )
		return new WP_Error('invalid_key', __('Invalid key','mgm'));

	if ( empty($login) || !is_string($login) )
		return new WP_Error('invalid_key', __('Invalid key','mgm'));

	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));
	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key','mgm'));
	
	return $user;
}
//reset password action hook
function mgm_reset_password($key, $user) {
	global $current_site;
	if(isset($user->ID) && $user->ID > 0) {
		$new_pass = wp_generate_password();
		do_action('password_reset', $user, $new_pass);
		wp_set_password($new_pass, $user->ID);
		update_user_option($user->ID, 'default_password_nag', true, true); //Set up the Password change nag.
		//get custom title/messages
		$title = apply_filters('password_reset_title', '');
		$message = apply_filters('password_reset_message', '', $new_pass);
		if ( $message && !mgm_mail($user->user_email, $title, $message) )
	  		wp_die( __('The e-mail could not be sent.','mgm') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.','mgm') );
	
		wp_password_change_notification($user);
		wp_safe_redirect(mgm_get_custom_url('login', false, array('checkemail' => 'newpass')));
		exit();
	}
}
//logout
function mgm_logout() {
	check_admin_referer('log-out');
	wp_logout();
	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : mgm_get_custom_url('login', false, array('loggedout' => 'true'));
	wp_safe_redirect( $redirect_to );
	exit();
}
//logout url hook
function mgm_logout_url($logout_url, $redirect) {
	if(is_super_admin()) return $logout_url;
	$login = mgm_get_custom_url('login');	
	$login = str_replace('?action=login','', $login);//if default login is again loaded
	if(preg_match('/wp-login.php/', $logout_url )) {
		$arr_queries = explode("?",$logout_url,2);	
		//$login = trailingslashit($login);
		$logout_url = $login . "?". $arr_queries[1];		
	}elseif($logout_url == '') {
		$logout_url = $login;
	}
	//the below line is not really required, just to ensure that it contains action = logout
	$logout_url = add_query_arg(array('action' => 'logout'), $logout_url);
	if($custom_redirect = mgm_logout_redirect_url()) {		
		$logout_url = html_entity_decode($logout_url);				
		$logout_url = remove_query_arg(array('redirect_to'), $logout_url);						
		$logout_url = add_query_arg(array('redirect_to' => $custom_redirect), $logout_url);		
		$logout_url = htmlentities($logout_url);			
	}
	
	return $logout_url;
}
//custom login url
function mgm_login_url($login_url, $redirect = '') {
	$login = mgm_get_custom_url('login');	
	$login = str_replace('?action=login','', $login);
	if(!preg_match('/wp-login.php/', $login)) {
		$arr_queries = explode("?", $login_url, 2);	
		if(!empty($arr_queries[1]))
			$login = $login . "?". $arr_queries[1];
		if(!preg_match('/redirect_to=/', $login) && !empty($redirect))
			$login = add_query_arg(array('redirect_to' => $redirect), $login);		
		$login_url = $login;
	}	
	return 	$login_url;
}
// mgm_sanitize_user
function mgm_sanitize_user($username){
	// Consolidate contiguous whitespace, lowercase
	return strtolower(preg_replace( '|\s+|', '_', $username ));
}
// mgm_validate_username
function mgm_validate_username($username){	
	// check if space
	return (preg_match('/\s/',$username)) ? false : true;
}
//remove the buddypress hook: bp_core_do_catch_uri from global filter array 
function mgm_disable_bp_redirection() {	
	//This doesn't work
	remove_action( 'template_redirect', 'bp_core_do_catch_uri' );
	global $wp_filter;	
	if(!empty($wp_filter['template_redirect'])) {				
		foreach ($wp_filter['template_redirect'] as $key => $val ) {
			if(isset($val['bp_core_do_catch_uri'])) {				
				unset($wp_filter['template_redirect'][$key]);
			}			
		}
	}
}
//check register.php exists in the selected theme
function mgm_check_theme_register() {
	$theme_dir = get_template();
	return ((file_exists(get_theme_root(). '/' . $theme_dir . '/registration/register.php') || $theme_dir == 'bp-default') ? true : false);
	
}
//replace avatar with custom photo if any
function mgm_get_avatar($avatar, $id_or_email) {	
	$user_id = 0;
	$email = '';
	if ( is_numeric($id_or_email) ) {
		$user_id = (int) $id_or_email;	
	} elseif ( is_object($id_or_email) ) {
	 	if ( isset($id_or_email->user_id) && !empty($id_or_email->user_id) ) {
	 		$user_id = $id_or_email->user_id;
	 	}elseif (!empty($id_or_email->comment_author_email)) {
			$email = $id_or_email->comment_author_email;
	 	}
	}else {
		$email = $id_or_email;
	}
	if(!empty($email)) {
		//find user id from email:
		$user = get_user_by('email', $email);
		if(isset($user->ID))
			$user_id = $user->ID;
	}
	
	if($user_id > 0) {
		$mgm_member = mgm_get_member($user_id);
		if(isset($mgm_member->custom_fields) && !empty($mgm_member->custom_fields)) {			
			foreach ($mgm_member->custom_fields as $field => $value) {
				if($field == 'photo' && !empty($value)) {								
					//use thumb image:
					$value = preg_replace("/_medium/", "_thumb", $value);					
					$arr_size = @getimagesize(MGM_FILES_UPLOADED_IMAGE_DIR . basename($value));
					$avatar = preg_replace("/src='(.*?)'/i", "src='".$value."'", $avatar);
					//select width:
					if($arr_size[0] >= $arr_size[1]) {
						//format: height='32' width='32'
						$avatar = preg_replace("/width='(.*?)'/i", "width='".$arr_size[0]."'", $avatar);		
						$avatar = preg_replace("/height='(.*?)'/i", "", $avatar);		
					}else {//select height
						$avatar = preg_replace("/width='(.*?)'/i", "", $avatar);		
						$avatar = preg_replace("/height='(.*?)'/i", "width='".$arr_size[1]."'", $avatar);	
					}					
					
					break;
				}				
			}
		}
	}
		
	return $avatar;
}
//delete user uploaded photo
function mgm_delete_user($user_id) {
	if($user_id > 0) {
		$mgm_member = mgm_get_member($user_id);
		if(isset($mgm_member->custom_fields) && !empty($mgm_member->custom_fields)) {			
			foreach ($mgm_member->custom_fields as $field => $value) {
				if($field == 'photo' && !empty($value)) {
					$medium 	= MGM_FILES_UPLOADED_IMAGE_DIR . basename($value); 
					$thumbnail 	= MGM_FILES_UPLOADED_IMAGE_DIR . basename( str_replace('_medium', '_thumb', $value) ); 
					if(is_file($medium)) unlink($medium);
					if(is_file($thumbnail)) unlink($thumbnail);
					break;
				}
			}
		}
	}
}
/**
 * Reassign member's pack to a specified pack[as per pack setting: move_members_pack value](issue#: 535)
 * This will be invoked in Expiry/User initiated Cancellation
 *
 * @param int $user_id
 * @param obj $mgm_member
 * @param string $type
 * @param boolean $return
 * @return obj depending on $return value
 */
function mgm_reassign_member_subscription($user_id, $mgm_member, $type, $return  = true) {
	if(isset($mgm_member->pack_id) && is_numeric($mgm_member->pack_id)) {
		$obj_pack = mgm_get_class('subscription_packs');
		$prev_pack = $obj_pack->get_pack($mgm_member->pack_id);
		//if move_members_pack (id) is set:
		if(isset($prev_pack['move_members_pack']) && is_numeric($prev_pack['move_members_pack'])) {
			$system = mgm_get_class('system');	
			$current_time = time();
			$subs_pack = $obj_pack->get_pack($prev_pack['move_members_pack']);
			//assign new pack:	
			// if trial on			
			$mgm_member->trial_on            = ($subs_pack['trial_on']) ? $subs_pack['trial_on'] 			: 0 ;
			$mgm_member->trial_cost          = ($subs_pack['trial_on']) ? $subs_pack['trial_cost'] 			: 0;
			$mgm_member->trial_duration      = ($subs_pack['trial_on']) ? $subs_pack['trial_duration'] 		: 0;
			$mgm_member->trial_duration_type = ($subs_pack['trial_on']) ? $subs_pack['trial_duration_type'] : 'd';
			$mgm_member->trial_num_cycles    = ($subs_pack['trial_on']) ? $subs_pack['trial_num_cycles'] 	: 0;
			
			// duration
			$mgm_member->duration        = $subs_pack['duration'];
			$mgm_member->duration_type   = strtolower($subs_pack['duration_type']);
			$mgm_member->amount          = $subs_pack['cost'];
			if(!isset($mgm_member->currency))
				$mgm_member->currency    = $system->setting['currency'];
			$mgm_member->membership_type = $subs_pack['membership_type'];		
			//set new pack id
			$mgm_member->pack_id         = $subs_pack['id'];						
			$mgm_member->active_num_cycles = $subs_pack['num_cycles']; 
			$mgm_member->payment_type    = ((int)$subs_pack['num_cycles'] == 1) ? 'one-time' : 'subscription';
			//reset joining date as current time
			$mgm_member->join_date = $current_time;
			if($mgm_member->duration_type == 'l')
				$mgm_member->expire_date = '';
			else {
				$duration_types = array('d'=>'DAY','m'=>'MONTH','y'=>'YEAR');	
				$time = strtotime("+{$mgm_member->duration} {$duration_types[$mgm_member->duration_type]}", $current_time);										
				// formatted
				$time_str = date('Y-m-d', $time);
				$mgm_member->expire_date = $time_str;
			}
			$mgm_member->last_pay_date = date('Y-m-d', $current_time);
			//reset as active
			$mgm_member->status 	= MGM_STATUS_ACTIVE;
			$mgm_member->status_str = ($mgm_member->status_str . ' ('.__('Reassigned new pack on ['.$type.']', 'mgm').')');
					
			//unset vars:
			if(isset($mgm_member->rebilled)) unset($mgm_member->rebilled);
			// payment info for unsubscribe		
			if(isset($mgm_member->payment_info)) unset($mgm_member->payment_info);
			//unset transaction id - let's keep the old for ref.
			//if(isset($mgm_member->transaction_id)) unset($mgm_member->transaction_id);
						
			//add new role/remove old role										
			if($prev_pack['role'] != $subs_pack['role']) {				
				$obj_role = mgm_get_class('roles'); 
				$obj_role->add_user_role($user_id, $subs_pack['role'], true, false );
				//remove user role:
				$obj_role->remove_userrole($user_id, $prev_pack['role']);			
			}
			//done
		}		
	}
	
	if($return)
		return $mgm_member;
	else 
		$mgm_member->save();	
		
}
/**
 * Custom schedule intervals
 *
 * @param array $schedules : WP schedules
 * @return array
 */
function mgm_custom_schedules($schedules) {
	
	$schedules['quarterhourly'] = array( 'interval' => (15 * 60), 'display' => __('Every 15 minutes', 'mgm') );
	
	return $schedules;
}
// end file