<?php 
// subscribe title
function mgm_get_payment_subscribe_page_title(){
	return __('Select','mgm');
}

// subscribe html 
function mgm_get_payment_subscribe_page_html(){
	// attach scripts, returns
	do_action('mgm_attach_scripts');
	
	// content
	$html = '';
	// get user
	$user = (isset($_GET['username']) ? get_userdatabylogin($_GET['username']):false);	
	// member
	$mgm_member = mgm_get_member($user->ID);								
	// print
	if (isset($_GET['action']) && count(mgm_get_class('membership_types')->membership_types)) :
		// upgrade
		if($_GET['action'] == 'upgrade' || $_GET['action'] == 'complete_payment'):
			$html .= mgm_get_upgrade_buttons();
		// extend	
		elseif($_GET['action'] == 'extend'):
			$html .= mgm_get_extend_button();
		// extend	
		elseif($_GET['action'] == 'purchase_another'):
			$html .= mgm_get_purchase_another_subscription_button();//TODO	
		// bad action	
		else:
			$html .= __('<p>Error - Unknown action, Exiting...</p>','mgm');						
		endif;											
	elseif (in_array($mgm_member->status, array(MGM_STATUS_NULL, MGM_STATUS_EXPIRED, MGM_STATUS_TRIAL_EXPIRED))):
		$html .= mgm_get_subscription_buttons($user, true);		
	elseif ($mgm_member->status == MGM_STATUS_PENDING):
		$html .= __('<p>Error - Your subscription status is pending. Please contact an administrator for more information.</p>','mgm');
	else:
		$html .= __('<p>You are already subscribed or an error occurred. Please contact an administrator for more information.</p>','mgm');
	endif;
		
	// print with filter applied
	return apply_filters('mgm_payment_subscribe_page_html', $html);
}	

// processing title
function mgm_get_payment_processing_page_title(){
	// title
	$title = __('Processing Payment','');
	// get module
	if(isset($_GET['module'])){
		// module
		$module = mgm_get_module($_GET['module'],'payment');
		// type
		if($module->hosted_payment == 'N'){
			$title = __('Enter Credit Card Details');
		}
	}
	// return 
	return $title;
}
// processing html
function mgm_get_payment_processing_page_html(){
	global $mgm_html_outout;
			
	// attach scripts, returns
	do_action('mgm_attach_scripts');
	
	// content
	$html = $mgm_html_outout;
		
	// print with filter applied
	return apply_filters('mgm_payment_processing_page_html', $html);
}

// processed title
function mgm_get_payment_processed_page_title(){
	// current module
	$module = $_GET['module'];
	// check
	if (!mgm_is_valid_module($module) || empty($module)) {	
		// redirect		
		mgm_redirect($home_url);
	} 
	// system
	//$mgm_system = get_option('mgm_system');		
	$mgm_system = mgm_get_class('system');	
	// module object
	$module_object = mgm_get_module($module, 'payment');
	
	// get title
	if (!isset($_GET['status']) || $_GET['status'] == 'success') {	
		$title = ($module_object->setting['success_title'] ? $module_object->setting['success_title'] : $mgm_system->get_template('payment_success_title', array(), true));
	} else if (!isset($_GET['status']) || $_GET['status'] == 'cancel') {	
		$title = __('Transaction cancelled','mgm');
	} else {	
		$title = ($module_object->setting['failed_title'] ? $module_object->setting['failed_title'] : $mgm_system->get_template('payment_failed_title', array(), true));
	}
	
	// return
	return mgm_stripslashes_deep($title);
}

// processed html
function mgm_get_payment_processed_page_html(){
	// refresh header
	if(isset($_GET['post_redirect'])) if(!headers_sent()) header("Refresh: 5;url=".$_GET['post_redirect']);
	// system
	//$mgm_system = get_option('mgm_system');	
	$mgm_system = mgm_get_class('system');	
	//autologin:
	$register_redirect = ((isset($_GET['register_redirect']) && !empty($_GET['register_redirect']) && isset($mgm_system->setting['enable_autologin']) && $mgm_system->setting['enable_autologin'] == 'Y' )) ? true : false ;
	if($register_redirect) {
		$register_redirect_time = 5;//in seconds
		$register_redirect_url = remove_query_arg('register_redirect', mgm_current_url());				
		$register_redirect_url = add_query_arg(array('redirect_transid' => $_GET['register_redirect']), $register_redirect_url);		
		if(!headers_sent()) header("Refresh: $register_redirect_time;url=".$register_redirect_url);
	}
	
	// home url
	$home_url = trailingslashit(get_option('siteurl'));	
	// current module
	$module = mgm_request_var('module');
	// check
	if (!mgm_is_valid_module($module) || empty($module)) {	
		// redirect		
		mgm_redirect($home_url);
	} 
	
	// module object
	$module_object = mgm_get_module($module, 'payment');
	// [domain]/subscribe/?method=payment_processed&module=mgm_paypal&status=success
	// [domain]/subscribe/?method=payment_processed&module=mgm_paypal&status=cancel
	// status and message
	$arr_shortcodes = array('transaction_amount' => '');
	if (!isset($_GET['status']) || $_GET['status'] == 'success') {			
		// mgm_replace_oldlinks_with_tag is a patch for replacing the old link
		$message = ($module_object->setting['success_message'] ? mgm_replace_oldlinks_with_tag($module_object->setting['success_message'], 'payment_success_message') : $mgm_system->get_template('payment_success_message', array(), true));		
		//get price
		if(isset($_GET['trans_ref'])) {
			$_GET['trans_ref'] = mgm_decode_id($_GET['trans_ref']);
			if($trans = mgm_get_transaction($_GET['trans_ref'])) {				
				$arr_shortcodes['transaction_amount'] = $trans['data']['cost'] .' '. $trans['data']['currency'];
			}
		}
	} else if (!isset($_GET['status']) || $_GET['status'] == 'cancel') {			
		$message = __('You have cancelled the transaction.','mgm');
	} else {	
		//mgm_replace_oldlinks_with_tag is a patch for replacing the old link
		$message = ($module_object->setting['failed_message'] ? mgm_replace_oldlinks_with_tag($module_object->setting['failed_message'], 'payment_failed_message') : $mgm_system->get_template('payment_failed_message', array(), true));
	}
	
	//parse short codes:
	//[transaction_amount] = amount paid
	foreach ($arr_shortcodes as $code => $value) {
		$message = str_replace( '['.$code.']', $value, $message );
	}
	
	// html
	$html = mgm_stripslashes_deep(mgm_get_message_template($message));
	// get error
	if (isset($_GET['errors'])) {
		// get errors
		$errors = explode('|', $_GET['errors']);
		// html
		$html .= '<h3>' . __('Messages', 'mgm') . '</h3>';
		$html .= '<div><ul>';
		// loop
		foreach ($errors as $error) {
			$html .= '<li>' . $error . '</li>';
		}
		$html .= '</ul>
		</div>';
	}	
		
	// redirect_to post
	if(isset($_GET['post_redirect'])){
		$html .= __('<b>You will be automatically redirected to the post you purchased within 5 seconds. Please <a href="'.$_GET['post_redirect'].'"> click here </a> to go to the page.</b>','mgm');
	}
	if($register_redirect) {
		$html .= __('<b>You will be automatically redirected to your Profile page within '.$register_redirect_time.' seconds. Please <a href="'.$register_redirect_url.'"> click here </a> to go to the page.</b>','mgm');
	}
		
	// print with filter applied
	return apply_filters('mgm_payment_processed_page_html', $html);
}

// post_purchase title
function mgm_get_post_purchase_page_title(){
	return __('Select Payment Option','mgm');
}

// post_purchase html
function mgm_get_post_purchase_page_html(){
	// html
	$html = mgm_get_post_purchase_buttons();	
	// print with filter applied
	return apply_filters('mgm_post_purchase_page_html', $html);
}

// registration title
function mgm_get_register_page_title(){
	return __('Register','mgm');
}

// registration html
function mgm_get_register_page_html(){
	// html
	$html = mgm_user_register_form();
	
	// print with filter applied
	return apply_filters('mgm_register_page_html', $html);
}

// profile title
function mgm_get_user_profile_page_title(){
	return __('Profile','mgm');
}

// profile html
function mgm_get_user_profile_page_html(){
	// html
	$html = mgm_user_profile_form();

	// print with filter applied
	return apply_filters('mgm_user_profile_page_html', $html);
}

// lost password title
function mgm_get_lost_password_page_title(){
	return __('Retrieve Password','mgm');
}

// lost password html
function mgm_get_lost_password_page_html(){
	// html
	$html = mgm_user_lostpassword_form(false);
		
	// print with filter applied
	return apply_filters('mgm_lost_password_page_html', $html);
}

// lost password title
function mgm_get_user_login_page_title(){
	return __('Login','mgm');
}

// lost password html
function mgm_get_user_login_page_html(){
	// html
	$html = mgm_user_login(false);
		
	// print with filter applied
	return apply_filters('mgm_user_login_page_html', $html);
}

// end file