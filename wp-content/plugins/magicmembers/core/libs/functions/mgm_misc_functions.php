<?php
// Common Functions
// dump
function mgm_array_dump($array, $return=false){
	// dump
	$dump = '<pre>' . print_r($array, true) . '</pre>';
	// return
	if($return){
		return $dump;
	} 
	// print
	echo $dump;	
}
// pr
function mgm_pr($array){
	mgm_array_dump($array);
}
// log
function mgm_log($data,$filename=NULL){
	// line count
	static $line=1;	
	// define
	if(!defined('LOG_REQUEST_ID')) define('LOG_REQUEST_ID', date('mdYHis'));
	// file name 
	$filename = (!$filename) ? LOG_REQUEST_ID : ($filename . '-' . LOG_REQUEST_ID);		
	// data, array to string
	if(is_array($data) || is_object($data)) $data = mgm_array_dump($data, true);
	// log	
	$crlf       = "\n";
	$end_crlf   = "\n\r";
	// open
	if($fp = fopen(MGM_FILES_LOG_DIR . $filename . '.log', "a+")){
		// write
		fwrite($fp, $crlf . ($line++) . '['.date('d-m-Y H:i:s').']:' . $crlf . str_repeat('-',100) . $crlf . $data . $end_crlf);
		// close
		fclose($fp);
		// return success
		return true;
	}
	// error
	return false;
}

// remote request
function mgm_remote_request($url, $error_string=true) {
    $string = '';
        
	if (ini_get('allow_url_fopen')) {
		if (!$string = @file_get_contents($url)) {
            if ($error_string) {
				$string = 'Could not connect to the server to make the request.';
            }
		}
	} else if (extension_loaded('curl')) {		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$string = curl_exec($ch);
		curl_close($ch);
	} else if ($error_string) {
	    $string = 'This feature will not function until either CURL or fopen to urls is turned on.';
	}
	
	// return
	return $string;
}

// remote post
function mgm_remote_post($url, $post_fields=NULL, $auth='', $http_header=array()) {
			
	// set headers	
	$headers   = array();	
	$headers[] = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11";
	$headers[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html,application/json;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$headers[] = "Accept-Language: en-us,en;q=0.5";
	$headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$headers[] = "Keep-Alive: 300";
	$headers[] = "Connection: keep-alive";
	$headers[] = "Content-Type: application/x-www-form-urlencoded";
	$headers[] = "Content-Length: " . strlen($fields);
	
	// init
    $ch = curl_init();	
	// set other params
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT,'Magic Members Membership Software');//$_SERVER['HTTP_USER_AGENT']
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);		
	// post
	if($post_fields){
		curl_setopt($ch, CURLOPT_POST, true);			
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
	}
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
	curl_setopt($ch, CURLOPT_REFERER, get_option('siteurl'));	
	// auth
	if($auth){
		curl_setopt($ch, CURLOPT_USERPWD, $auth);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	}
	// get result
	$response = curl_exec($ch);					
	curl_close($ch);
	// return
	return $response;
}

// request post/get
function mgm_request_var($key, $default='', $strip_tags=false) {
	// gpc	
	return mgm_gpc($_REQUEST, $key, $default, $strip_tags);
}

// request post
function mgm_post_var($key, $default='', $strip_tags=false) {
	// gpc	
	return mgm_gpc($_POST, $key, $default, $strip_tags);
}

// request get
function mgm_get_var($key, $default='', $strip_tags=false) {
	// gpc	
	return mgm_gpc($_GET, $key, $default, $strip_tags);
}

// request cookie
function mgm_cookie_var($key, $default='', $strip_tags=false) {
	// gpc	
	return mgm_gpc($_COOKIE, $key, $default, $strip_tags);
}

// get/post/cookie
function mgm_gpc($var, $key, $default='', $strip_tags=false){
	// data
	$data = '';
	// check
	if (isset($var[$key])) {
		$data = $var[$key];		
	}else{
		$data = $default;
	}
	// strip
	if ($strip_tags) {
		$data = strip_tags($data);
	}
	// data
	return $data;
}

// create mask url for making post to self 
function mgm_home_url($query=''){	
	// using cutom 
	/* ----------------------------------------------------------------------------
	$custom_permalink = (get_option('permalink_structure')!='') ? true : false;
	// get base
	$base_url = site_url() . ($custom_permalink ? '/' : '?') ;
	// appaned
	if($custom_permalink){
		$base_url .= $query.'/';// #301 related, not required but using to clean url
	}else{
		$base_url .= $query.'=1';
	}
	// return 
	return $base_url;
	--------------------------------------------------------------------------------*/
	// permalink structure
	if(!get_option('permalink_structure')){// empty is default query string
	// set
		$path = '?' . $path . '=1';
	}else{	
	// set
		$path = '/' . $path . '/';// #301 related, not required but using to clean url	
	}
	// return 
	return $home_url = home_url($path);
}

// make secure if ssl is on or taged
function mgm_ssl_url($url){	
	// ssl
	if((mgm_get_class('system')->setting['use_ssl_paymentpage'] == 'Y' || is_ssl())){
		$url = preg_replace('/^http:\/\//', 'https://', $url);
	}
	// return
	return $url;
}

// generate subscription buttons for first time payment
function mgm_get_subscription_buttons($user=false){
	global $wpdb;
	// user 
	if($user === false)
		$user = (isset($_GET['username']) ? get_userdatabylogin($_GET['username']) : false);	
	
	// packs 	
	$packs_obj = mgm_get_class('subscription_packs');
	// mgm member
	$mgm_member = mgm_get_member($user->ID);			

	// check subscription
	if (isset($_GET['subs'])) {
		// get		
		$subs_pack = mgm_decode_package($_GET['subs']);
		extract($subs_pack);		
		// validate			
		$pack = $packs_obj->validate_pack($cost, $duration, $duration_type, $membership_type, $pack_id);		
		// error
		if($pack === false){
			// no more process
			return  sprintf(__('Invalid Data Passed. <a href="%1$s">Try again.</a>','mgm'), add_query_arg(array('username'=>$user->user_login), mgm_get_custom_url('transactions')));
		}		
		
		// is using a coupon ? reset prices
		//if($mgm_member->coupon){
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
		
		// get active modules
		$a_payment_modules = mgm_get_class('system')->get_active_modules('payment');					
		// free | trial with zero cost | other membership with free module active
		if ((float)$pack['cost'] == 0.00 && (in_array($membership_type, array('free','trial')) || in_array('mgm_free',(array)$pack['modules']))) {						
			// payments url
			$payments_url == mgm_get_custom_url('transactions');
			// module 
			$modules = array('mgm_'.$membership_type);
			// other
			$modules[] = ($membership_type=='free') ? 'mgm_trial' : 'mgm_free';
			// init
			$module = '';
			// check if mod available
			foreach($modules as $mod){
				// check
				if(in_array($mod, $a_payment_modules)){	
					$module = $mod;			
					break;
				}
			}
			
			// exit
			if(!$module){
				// return
				return __('No Free module active, please activate Trial or Free module.','mgm');
				exit;
			}
			
			// redirect
			$redirector = $_GET['redirector'];
			$mod_obj = mgm_get_module($module,'payment');
			$transid = $mod_obj->_create_transaction($pack, array('is_registration'=>true, 'user_id' => $user->ID));			
			// attempt to redirect to the processor.			
			$redirect = add_query_arg(array('method'=>'payment_return', 'module'=>$module, 'custom' => ($user->ID . '_' . $duration . '_'  . $duration_type . '_' . $pack_id), 'redirector'=>$redirector, 'transid' => mgm_encode_id($transid)), $payments_url);							
			// redirect
			if (!headers_sent()) {									
				@header('location: ' . $redirect);
			}
			// js redirect
			$html .= '<script type="text/javascript">window.location = "'. $redirect .'";</script><div>' . $packs_obj->get_pack_desc($pack) . '</div>';
		
		} else {	
		// paid subscription 		
			// init 
			$payment_modules = array();			
			// when active
			if($a_payment_modules){
				// loop
				foreach($a_payment_modules as $payment_module){
					// not trial
					if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;
					// modules
					if(isset($pack['modules']) && !in_array($payment_module, (array)$pack['modules'])) continue;
					// store
					$payment_modules[] = $payment_module;					
				}
			}
			// check				
			if(count($payment_modules)==0){
				$html .= '<div>' . __('No active payment module', 'mgm') . '</div>';
			}else{				
				$html .= '<div style="height:30px; font-weight:bold" align="center">' . $packs_obj->get_pack_desc($pack) . '</div>';
				// coupon				
				if(isset($mgm_member->coupon['id'])){
					$html .= '<div style="height:30px; font-weight:bold" align="center">' . sprintf(__('Using Coupon "%s" - %s','mgm'),$mgm_member->coupon['name'], $mgm_member->coupon['description']) . '</div>';
				}
				// html
				$html .= '<div style="height:30px; font-weight:bold" align="center">' . __('Select Payment Gateway','mgm') . '</div>';
				// tran id
				$tran_id = 0;
				// generate modules
				foreach($payment_modules as $payment_module){
					// get obj
					$mod_obj = mgm_get_module($payment_module,'payment');
					// create transaction
					if($tran_id==0){
						$tran_id = $mod_obj->_create_transaction($pack, array('is_registration'=>true, 'user_id' => $user->ID));
					}
					// html
					$html .='<div>' . $mod_obj->get_button_subscribe(array('pack'=>$pack,'tran_id'=>$tran_id)) . '</div>';
				}
			}
		}
		// return	
		return $html;	
	}
	// error
	return '';
}
// get partial fields
function mgm_get_partial_fields($display=NULL, $name='mgm_upgrade_field'){
	// display
	if(!$display) $display = array('on_upgrade'=>true);
	// get system
	$system = mgm_get_class('system');
	
	// 	get row row template
	$form_row_template = $system->get_template('register_form_row_template');	
	// get template row filter, mgm_register_form_row_template for custom
	$form_row_template = apply_filters('mgm_register_form_row_template', $form_row_template);	
	// get mgm_form_fields generator
	$form_fields = & new mgm_form_fields(array('wordpres_form'=>$wordpres_form,'form_row_template'=>$form_row_template));
	// user fields on specific page, coupon specially
	$cf_partial = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>$display));
	// found some
	if($cf_partial){
		// loop
		foreach($cf_partial as $field){
			// init
			$form_html = $form_row_template;
			// replace wrapper
			$form_html = str_replace('[user_field_wrapper]', $field['name'].'_box', $form_html);
			// replace label
			$form_html = str_replace('[user_field_label]', ($field['attributes']['hide_label']?'':mgm_stripslashes_deep($field['label'])), $form_html);
			// replace element
			$form_html = str_replace('[user_field_element]', $form_fields->get_field_element($field, $name), $form_html);
			
			// append
			$html .= $form_html;		
		}
	}
	// return
	return $html;
}
// save
function mgm_save_partial_fields($display=NULL, $name='mgm_upgrade_field', $cost, $is_single=true){
	global $wpdb;
	// set data    
	$user = wp_get_current_user();
	//issue#: 416
	if($user->ID == 0 || !is_numeric($user->ID)) {
		$user = (isset($_GET['username']) ? get_userdatabylogin($_GET['username']) : false);				
	}
	// display
	if(!$display) $display = array('on_upgrade'=>true);
	// get system
	$system = mgm_get_class('system');
	// member
	$mgm_member = mgm_get_member($user->ID);
	// user fields on specific page
	$cf_partial = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>$display));
	
	// found some
	if($cf_partial){
		// loop
		foreach($cf_partial as $field){			
			// name switch		
			switch($field['name']){
				case 'coupon':						
					// validate
					$coupon = mgm_validate_coupon($_POST[$name][$field['name']], $cost);		
					
					if($field['attributes']['required'] && empty($_POST[$name][$field['name']])) {						
						if(!empty($_POST['form_action'])) {
							//redirect back to the form							
							$redirect = add_query_arg(array('error_field' => $field['label'], 'error_type' => 'empty'), $_POST['form_action']);														
							mgm_redirect($redirect);
							exit;
						}
					}
					// valid
					if($coupon!==false){	
						// update_usage
						$update_usage = false;
						// field name in object for ref
						$field_name = str_replace(array('mgm_','_field'),'',$name); // mgm_upgrade_field = > upgrade	
						// single coupon, upgrade/ extend
						if($is_single){
							if(isset($mgm_member->{$field_name}['coupon']))
								$mgm_member->{$field_name}['coupon'] = (array) $mgm_member->{$field_name}['coupon'];									
							// update coupon usage, if not used already
							if(!isset($mgm_member->{$field_name}['coupon']) || (isset($mgm_member->{$field_name}['coupon']) && $mgm_member->{$field_name}['coupon']['id'] != $coupon['id'])){														
								// set
								$mgm_member->{$field_name}['coupon'] = $coupon;	
								// usage
								$update_usage = true;
							}														
						}else{
							if(!isset($mgm_member->{$field_name}['coupons'])){
								// never added
								$mgm_member->{$field_name}['coupons'] = array($coupon['id'] => $coupon);
								// usage
								$update_usage = true;
							}else{
								// not added
								if(!in_array($coupon['id'],array_keys($mgm_member->{$field_name}['coupons']))){
									// never added
									$mgm_member->{$field_name}['coupons'] = array_merge($mgm_member->{$field_name}['coupons'],array($coupon['id']=>$coupon));
									// usage
									$update_usage = true;
								}
							}
						}	
						// update database
						if($update_usage){
							$wpdb->query(sprintf("UPDATE `%s` SET `used_count` = IF(`used_count` IS NULL, 1, `used_count`+1) WHERE `id`='%d'", TBL_MGM_COUPON, $coupon['id']));							
						}	
					}
				break;
			}
		}
		
		// update option
		// update_user_option($user->ID,'mgm_member',$mgm_member, true);
		$mgm_member->save();
	}
	
	// return
	if(!$is_single)	
		return isset($coupon) ? $coupon : false;
	// default		
	return $mgm_member;
}

function mgm_save_partial_fields_purchase_more($user_id, $membership_type, $cost, $is_single=true) {
	global $wpdb;	
	$name = 'mgm_upgrade_field';
	// member
	$mgm_member = mgm_get_member($user_id);	
	$key_found = false;
	if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
		foreach ($mgm_member->other_membership_types as $key => $memtypes) {
			$memtypes = mgm_convert_array_to_memberobj($memtypes, $user_id);
			if($memtypes->membership_type == $membership_type ) {
				//reset if already saved
				$key_found = true;
				//return $memtypes;
				break;
			}
		}
	}	
	if(!$key_found)
		return $mgm_member;
		
	// user fields on specific page
	$cf_partial = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=> array('on_upgrade'=>true)));
	// found some
	if($cf_partial){
		// loop
		foreach($cf_partial as $field){		
			// name switch		
			switch($field['name']){
				case 'coupon':						
					// validate					
					$coupon = mgm_validate_coupon($_POST[$name][$field['name']], $cost);
					if($field['attributes']['required'] && empty($_POST[$name][$field['name']])) {						
						if(!empty($_POST['form_action'])) {
							//redirect back to the form							
							$redirect = add_query_arg(array('error_field' => $field['label'], 'error_type' => 'empty'), $_POST['form_action']);														
							mgm_redirect($redirect);
							exit;
						}
					}
					// valid
					if($coupon!==false){	
						// update_usage
						$update_usage = false;
						// field name in object for ref
						$field_name = str_replace(array('mgm_','_field'),'',$name); // mgm_upgrade_field = > upgrade
						
						if(isset($mgm_member->other_membership_types[$key]))
							$mgm_member->other_membership_types[$key] = mgm_convert_array_to_memberobj($mgm_member->other_membership_types[$key], $user_id);	
							
						if(!is_array($mgm_member->other_membership_types[$key]->$field_name))
							$mgm_member->other_membership_types[$key]->$field_name = array();
						// single coupon, upgrade/ extend
						if($is_single){											
							// update coupon usage, if not used already
							if(!isset($mgm_member->other_membership_types[$key]->$field_name['coupon']) || (isset($mgm_member->other_membership_types[$key]->$field_name['coupon']) && $mgm_member->other_membership_types[$key]->$field_name['coupon']['id'] != $coupon['id'])){														
								// set
								$mgm_member->other_membership_types[$key]->$field_name['coupon'] = $coupon;	
								// usage
								$update_usage = true;
							}														
						}else{
							if(!isset($mgm_member->other_membership_types[$key]->$field_name['coupons'])){
								// never added
								$mgm_member->other_membership_types[$key]->$field_name['coupons'] = array($coupon['id']=>$coupon);
								// usage
								$update_usage = true;
							}else{
								// not added
								if(!in_array($coupon['id'],array_keys($mgm_member->other_membership_types[$key]->$field_name['coupons']))){
									// never added
									$mgm_member->other_membership_types[$key]->$field_name['coupons'] = array_merge($mgm_member->other_membership_types[$key]->$field_name['coupons'],array($coupon['id']=>$coupon));
									// usage
									$update_usage = true;
								}
							}
						}	
						// update database
						if($update_usage){
							$wpdb->query(sprintf("UPDATE `%s` SET `used_count` = IF(`used_count` IS NULL, 1, `used_count`+1) WHERE `id`='%d'", TBL_MGM_COUPON, $coupon['id']));							
						}	
					}
				break;
			}
		}		
		// update option
		// update_user_option($user_id,'mgm_member',$mgm_member, true);
		//make sure other_membership_types is array:
		if(isset($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types))
			$mgm_member->other_membership_types = mgm_convert_memberobj_to_array($mgm_member->other_membership_types);
			
		$mgm_member->save();
	}
	
	// return
	if(!$is_single)	
		return isset($coupon) ? $coupon : false;
	//make sure returns an array to work the previous code	
	$mgm_member->other_membership_types[$key] = mgm_convert_array_to_memberobj($mgm_member->other_membership_types[$key], $user_id)	;
	// default		
	return $mgm_member->other_membership_types[$key];
}
// show buttons of modules available for upgrade/downgrade
function mgm_get_upgrade_buttons($args=array()) { 
	global $wpdb;
	// set data    
	$user = wp_get_current_user();	
	// check
	if($user->ID == 0 || !is_numeric($user->ID)) {
		$user = (isset($_GET['username']) ? get_userdatabylogin($_GET['username']) : false);				
	}	
	// userdata
	$username        = $user->user_login;
	$mgm_home        = get_option('siteurl');
	// member
	$mgm_member      = mgm_get_member($user->ID);
	//this is a fix for issue#: 589, see the notes for details:
	//This is to read saved coupons as array in order to fix the fatal error on some servers.	
	//This will change the object on each users profile view.
	//Also this will avoid using patch for batch update,	
	$arr_coupon = array('upgrade', 'extend');
	$oldcoupon_found = 0;
	foreach ($arr_coupon as $cpn_type) {
		if(isset($mgm_member->{$cpn_type}['coupon']) && is_object($mgm_member->{$cpn_type}['coupon'])) {
			$mgm_member->{$cpn_type}['coupon'] = (array) $mgm_member->{$cpn_type}['coupon'];
			$oldcoupon_found++ ;
		}
	}
	if($oldcoupon_found) {		
		$mgm_member->save();
	}
	// other objects
	$system      	 = mgm_get_class('system');
	$membership_type = mgm_get_user_membership_type($user->ID, 'code');
	$packs_obj       = mgm_get_class('subscription_packs');
	// get active packs on upgrade page	
	$active_packs 	 = $packs_obj->get_packs('upgrade');
	// duration	
	$duration_str    = $packs_obj->duration_str;
	$trial_taken     = $mgm_member->trial_taken;	
	// pack_id
	$pack_id         = (int)$mgm_member->pack_id;		
	// action 
	$action = (isset( $_GET['action'])) ? $_GET['action'] : 'upgrade'; // upgrade or complete_payment
	// query_arg
	$form_action = mgm_get_custom_url('transactions', false, array('action'=>$action, 'username'=> $username));
	// cancel 
	$cancel_url = ($action == 'upgrade') ? mgm_get_custom_url('profile') : mgm_get_custom_url('login');
	// active modules
	$a_payment_modules = $system->get_active_modules('payment');		
	
	// post form-----------------------------------------------------------------------
	if($_POST && isset($_POST['submit']) && isset($_POST['subs_opt']) ){
		// check and validate passed data		
		if ($_POST['ref'] != md5($mgm_member->amount .'_'. $mgm_member->duration .'_'. $mgm_member->duration_type .'_'. $mgm_member->membership_type)) {
			// die
			wp_die(__('Package data tampered. Cheatin!','mgm'));
		}
		// get selected pack 			
		$selected_pack = mgm_decode_package($_POST['subs_opt']);
		
		// check selected pack is a valid pack		     
		$valid = false;
		// loop packs
		foreach($active_packs as $pack) {
			// check
			if ($pack['cost'] == $selected_pack['cost'] 
			    && $pack['duration'] == $selected_pack['duration'] 
				&& $pack['duration_type'] == $selected_pack['duration_type'] 
				&& $pack['membership_type'] == $selected_pack['membership_type']
				&& $pack['id'] == $selected_pack['pack_id'] 
				) {
				// valid
				$valid = true; break;
			}
		}
		// error
		if (!$valid) {  wp_die(__('Invalid package data. Cheatin!','mgm'));	}

		if(!isset($selected_pack['num_cycles'])) {
			//Note the above break in for loop:
			$selected_pack['num_cycles'] = $pack['num_cycles'];
		}
		
		// save member data including coupon etc, MUST save after all validation passed, we dont want any 
		// unwanted value in member object unless its a valid upgrdae
		$mgm_member = mgm_save_partial_fields(array('on_upgrade'=>true),'mgm_upgrade_field', $selected_pack['cost']);	
		
		// is using a coupon ? reset prices
		if($selected_pack!==false && isset($mgm_member->upgrade['coupon']['id'])){			
			// cost			
			if( isset($mgm_member->upgrade['coupon']['cost']) ){
				// original
				$selected_pack['original_cost'] = $selected_pack['cost'];
				// payable
				$selected_pack['cost'] = $mgm_member->upgrade['coupon']['cost'];
			}	
			// duration
			if( isset($mgm_member->upgrade['coupon']['duration']) )
				$selected_pack['duration'] = $mgm_member->upgrade['coupon']['duration'];
			// duration type	
			if( isset($mgm_member->upgrade['coupon']['duration_type']) )
				$selected_pack['duration_type'] = $mgm_member->upgrade['coupon']['duration_type'];
			// membership type	
			if( isset($mgm_member->upgrade['coupon']['membership_type']) )
				$selected_pack['membership_type'] = $mgm_member->upgrade['coupon']['membership_type'];	
			// billing cycles. issue#478	
			if( isset($mgm_member->upgrade['coupon']['num_cycles']) )
				$selected_pack['num_cycles'] = $mgm_member->upgrade['coupon']['num_cycles'];	
				
			// trial on	
			if( isset($mgm_member->upgrade['coupon']['trial_on']) )
				$selected_pack['trial_on'] = $mgm_member->upgrade['coupon']['trial_on'];
			// trial cost	
			if( isset($mgm_member->upgrade['coupon']['trial_cost']) )
				$selected_pack['trial_cost'] = $mgm_member->upgrade['coupon']['trial_cost'];
			// trial duration type	
			if( isset($mgm_member->upgrade['coupon']['trial_duration_type']) )
				$selected_pack['trial_duration_type'] = $mgm_member->upgrade['coupon']['trial_duration_type'];
			// trial duration	
			if( isset($mgm_member->upgrade['coupon']['trial_duration']) )
				$selected_pack['trial_duration'] = $mgm_member->upgrade['coupon']['trial_duration'];
			// trial billing cycles		
			if( isset($mgm_member->upgrade['coupon']['trial_num_cycles']) )
				$selected_pack['trial_num_cycles'] = $mgm_member->upgrade['coupon']['trial_num_cycles'];		
				
			// mark pack as coupon applied				
			$selected_pack['coupon_id'] = $mgm_member->upgrade['coupon']['id'];		
		}
		
		// start html
		$html = '<table width="100%" cellpadding="3" cellspacing="0" border="0" align="center" class="form-table">';		
		// free package
		if (($selected_pack['cost'] == 0 || $selected_pack['membership_type'] == 'free') && in_array('mgm_free', $a_payment_modules) && mgm_get_module('mgm_free')->enabled=='Y') {	
			// html		
			$html .= '<tr><th>' . __('Create a free account: ','mgm') . ucwords($selected_pack['membership_type']) . '</th></tr>';			
			// module
			$module = 'mgm_free';
			// payments url
			$payments_url = mgm_get_custom_url('transactions');			
			// if tril module selected and cost is 0 and free moduleis not active
			if($selected_pack['membership_type'] == 'trial'){
				// check
				if(in_array('mgm_trial', $a_payment_modules)){
					// module
					$module = 'mgm_trial';
				}
			}
			// query_args
			$query_args = array('method' => 'payment_return', 'module'=>$module, 
			                    'custom' => implode('_', array($user->ID, $selected_pack['duration'], $selected_pack['duration_type'], $pack_id)));
			// redirector
			if(isset($_REQUEST['redirector'])){
				// set
				$query_args['redirector'] = $_REQUEST['redirector'];
			}
			// redirect to module to mark the payment as complete
			$redirect = add_query_arg($query_args, $payments_url);			
			// redirect
			if (!headers_sent()) {							
				@header('location: ' . $redirect);
			}else{
			// js redirect
				$html .= '<script type="text/javascript">window.location = "'. $redirect .'";</script><div>' . $packs_obj->get_pack_desc($pack) . '</div>';
			}			
		} else {		
		// paid package, generate buy buttons
			// set html	
			$html .= '<tr><th style="height:30px">' . $packs_obj->get_pack_desc($selected_pack) . '</th></tr>';
			// coupon			
			if(isset($mgm_member->upgrade['coupon']['id'])){	
				// set html 
				$html .= '<tr><th style="height:30px">' . sprintf(__('Using Coupon "%s" - %s','mgm'),$mgm_member->upgrade['coupon']['name'], $mgm_member->upgrade['coupon']['description']) . '</th></tr>';
			}
			// set html
			$html .= '<tr><th style="height:30px">' . __('Select Payment Gateway','mgm') . '</th></tr>';
		}
		
		// init 
		$payment_modules = array();			
		// active
		if(count($a_payment_modules)>0){
			// loop
			foreach($a_payment_modules as $payment_module){
				// not trial
				if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;	
				// consider only the modules assigned to pack
				if(isset($selected_pack['modules']) && !in_array($payment_module, (array)$selected_pack['modules'])) continue;			
				// store
				$payment_modules[] = $payment_module;					
			}
		}
		
		// loop payment module if not free		
		if (count($payment_modules) && $selected_pack['cost']) {
			// transaction
			$tran_id = false;
			// loop
			foreach($payment_modules as $module) {
				// module
				$mod_obj = mgm_get_module($module,'payment');	
				// create transaction
				if(!$tran_id) $tran_id = $mod_obj->_create_transaction($selected_pack, array('user_id' => $user->ID));
				// set html				
				$html .="<tr><td>";			
				$html .= $mod_obj->get_button_subscribe(array('pack'=>$selected_pack,'tran_id'=>$tran_id)) ;
				$html .="</td></tr>";	
			}
		} else {
		// no module error
			if($selected_pack['cost']){		
				// set html	
				$html .= "<tr><td>".__('Error, no payment gateways active on upgrade page, notify administrator.','mgm')."</td></tr>";
			}
		}
		// html
		$html .= '</table>';
	// end post form 	
	}else{
	// generate upgrade form ----------------------------------------------------------	
		// selected subscription, from args (shortcode) or get url	
		$selected_pack = mgm_get_selected_subscription($args);	
		
		// html
		$html  = '<p class="message register">'. __('Choose a Membership Package','mgm') .'</p>';		
 		// upgrade_packages
		$upgrade_packages = '';
		
		// loop		
		foreach($active_packs as $pack) {			
			// default			
			$checked = '';
			// on action
			switch($action){
				case 'complete_payment':
				// for complete payment only show purchased pack
					// pack 
					if(isset($pack_id)){
						// leave other pack
						if($pack['id'] != $pack_id) continue 2;									   
						// select 
						if($pack['id'] == $pack_id) $checked='checked="checked"';
					}
				break;
				case 'upgrade':
				default:
				// upgrade
					// pack
					if(isset($pack_id)){
						// leave current pack, it will goto extend
						if($pack['id'] == $pack_id) continue 2;	
				    }
					// skip trial or free packs
					if($pack['membership_type'] == 'trial' || $pack['membership_type'] == 'free') continue 2;
					
					// skip if not allowed
					if(!mgm_pack_upgrade_allowed($pack)) continue 2;
					
					// reset
					$checked = mgm_select_subscription($pack,$selected_pack);
											
					// skip other when a package sent as selected
					if($selected_pack !== false && empty($checked)) continue 2; 					
				break;
			}			
			
			// checked
			if(!$checked) $checked = ((int)$pack['default'] == 1 ? ' checked="checked"':''); 
			
			// duration                      
			if ($pack['duration'] == 1) {
				$dur_str = rtrim($duration_str[$pack['duration_type']], 's');
			} else {
				$dur_str = $duration_str[$pack['duration_type']];
			}
			
			// encode pack
			$subs_opt_enc = mgm_encode_package($pack);
			//expand this if needed
			$css_link_format = '<link rel="stylesheet" href="%s" type="text/css" media="all" />';				
			$css_file = MGM_ASSETS_URL . 'css/mgm_form_fields.css';
			$upgrade_packages .= sprintf($css_link_format, $css_file);
			
			// free
			if (($pack['cost'] == 0 || strtolower($pack['membership_type']) == 'free') && in_array('mgm_free', $a_payment_modules) && mgm_get_module('mgm_free')->enabled=='Y') {
				// html
				$upgrade_packages .= '  
					<div style="margin: 10px 0px; overflow: auto;">
						<div style="width: 25px; float: left;">
							<input type="radio" ' . $checked . ' class="checkbox" name="subs_opt" value="'. $subs_opt_enc .'" />
						</div>
						<div style="width: 245px; float: left;">
							' . ucwords($pack['membership_type']) . '
						</div>
						<div class="clearfix"></div>
						<div class="mgm_subs_desc">
							' . mgm_stripslashes_deep($pack['description']) . '
						</div>
					</div>';
			} else {
				// html
				$upgrade_packages .= '  
					<div style="margin: 10px 0px; overflow: auto;">
						<div style="width: 25px; float: left;">
							<input type="radio" ' . $checked . ' class="checkbox" name="subs_opt" value="'. $subs_opt_enc .'" />
						</div>
						<div style="width: 245px; float: left;">
							' . $packs_obj->get_pack_desc($pack) . '
						</div>
						<div class="clearfix"></div>
						<div class="mgm_subs_desc">
							' . mgm_stripslashes_deep($pack['description']) . '
						</div>
					</div>';				
			}			
		}
		
		// show error
		if(!$upgrade_packages){
			// html
			$html .= '<div style="margin: 10px 0px; overflow: auto;">						
						<div style="width: 245px; float: left;">
							' . __('Sorry, no upgrades available.','mgm') . '
						</div>
					  </div>
					  <p>						
					  	  <input type="button" name="cancel" onclick="window.location=\''.$cancel_url.'\'" value="&laquo; '.__('Cancel','mgm').'" class="button-primary" />&nbsp;					
					  </p>';
		}else{						
			// check errors if any:
			$html .= mgm_subscription_purchase_errors();			
			
			// form
			$html .= '<form action="'.$form_action .'" method="post" class="mgm_form"><div style="clear: both; overflow: hidden; padding-bottom: 5px;">';
			$html .= $upgrade_packages;
			// get coupon field
			$html .= mgm_get_partial_fields(array('on_upgrade'=>true),'mgm_upgrade_field');
			// html
			$html .= '<input type="hidden" name="ref" value="'. md5($mgm_member->amount .'_'. $mgm_member->duration .'_'. $mgm_member->duration_type .'_'. $mgm_member->membership_type) .'" />';					
			$html .= '<input type="hidden" name="form_action" value="'. $form_action .'" />';					
			
			// edit link
			$edit_link  = '';
			// edit on complete payment only
			if($action == 'complete_payment' && isset($_COOKIE['wp-tempuser-edit']) && $_COOKIE['wp-tempuser-edit'] == $user->ID){
				// issue#: 416
				$upgrade_url  = add_query_arg(array('action' => 'upgrade'), mgm_get_url());				
				$edit_link   .= '<input type="button" value="&laquo; '.__('Show other packages','mgm').'" class="button-primary" onclick="window.location=\''.$upgrade_url.'\'">';
			}			
			// set
			$html .= '<p>
						'.$edit_link.'	
						<input type="submit" name="submit" value="'.__('Next','mgm').' &raquo;" class="button-primary" />&nbsp;&nbsp;
						<input type="button" name="cancel" onclick="window.location=\''.$cancel_url.'\'" value="&laquo; '.__('Cancel','mgm').'" class="button-primary" />&nbsp;					
					  </p>';
			// html
			$html .= '</div></form>';
		}
		// end generate form 		
	}// end	
    
	// return    	
	return $html;	
}

/**
 * Magic Members get extend buttons/packs
 * display the current pack for extend if the pack is set as renewable/ active on extend page
 * show all packs active on extend page otherwise
 *
 * @package MagicMembers
 * @since 2.0
 * @param none
 * @return formatted html
 */ 
function mgm_get_extend_button() {
	global $wpdb;    
	// get data
	$user = wp_get_current_user();
	if($user->ID == 0 || !is_numeric($user->ID))
		$user = (isset($_GET['username']) ? get_userdatabylogin($_GET['username']) : false);
	$username = $user->user_login;	
	// get member
	$mgm_member = mgm_get_member($user->ID);
	//this is a fix for issue#: 589, see the notes for details:
	//This is to read saved coupons as array in order to fix the fatal error on some servers.	
	//This will change the object on each users profile view.
	//Also this will avoid using patch for batch update,	
	$arr_coupon = array('upgrade', 'extend');
	$oldcoupon_found = 0;
	foreach ($arr_coupon as $cpn_type) {
		if(isset($mgm_member->{$cpn_type}['coupon']) && is_object($mgm_member->{$cpn_type}['coupon'])) {
			$mgm_member->{$cpn_type}['coupon'] = (array) $mgm_member->{$cpn_type}['coupon'];
			$oldcoupon_found++ ;
		}
	}
	if($oldcoupon_found) {		
		$mgm_member->save();
	}
	// other objects
	$system     = mgm_get_class('system');	
	$packs_obj  = mgm_get_class('subscription_packs');		
	// selected pack
	$pack_id = (int)$_GET['pack_id'];
	// action 
	$action = (isset( $_GET['action'])) ? $_GET['action'] : 'extend'; // extend
	// query_arg
	$form_action = mgm_get_custom_url('transactions', false, array('action'=>$action, 'pack_id'=>$pack_id, 'username'=> $username));
	// cancel 
	$cancel_url = mgm_get_custom_url('membership_details');
	// active modules
	$a_payment_modules = $system->get_active_modules('payment');	
	// active packs
	$active_packs = array();
	// init html
	$html = $error = '';
	// pack id passed in get, coming from profile membership details
	if($pack_id = (int)$_GET['pack_id']){
		// get selected pack
		$selected_pack = $packs_obj->get_pack($pack_id); 
		// validate
		if($selected_pack !== false){
			// check if extend allowed on the current pack of subscriber/user
			if(!mgm_pack_extend_allowed($selected_pack)){
				// error
				$error = __('Renewal of the current subscription is not allowed.','mgm'); 				
				// get packs
				$active_packs = $packs_obj->get_packs('extend');
				// check
				if(count($active_packs) > 0){
					// error
					$error = ''; // reset error
					// html head
					$html  = '<p class="message register">'. __('Choose a subscription package','mgm') .'</p>';						 
				}				
			}else{
				// html
				$html  = '<p class="message register">'. __('Extend current subscription','mgm') .'</p>';	
				// active packs
				$active_packs[] = $selected_pack;
			}			
		}
	}	
	
	// post form---------------------------------------------------------------------
	if($_POST && isset($_POST['submit']) && isset($_POST['subs_opt']) ){
	// process post	
		// get pack data			
		$selected_pack = mgm_decode_package($_POST['subs_opt']);
		// check selected pack		     
		$valid = false;
		// loop packs
		foreach ($active_packs as $pack) {
			// check
			//check pack id as well: issue#: 580
			if ($pack['cost'] == $selected_pack['cost'] 
			    && $pack['duration'] == $selected_pack['duration'] 
				&& $pack['duration_type'] == $selected_pack['duration_type'] 
				&& $pack['membership_type'] == $selected_pack['membership_type']
				&& $pack['id'] == $selected_pack['pack_id']  
				) {
				// valid
				$valid = true; break;
			}
		}
		// error
		if (!$valid) { wp_die(__('Invalid data. Cheatin!','mgm'));}	
				
		//update num_cycles if not set
		if(!isset($selected_pack['num_cycles'])) {
			//Note the above break in for loop:
			$selected_pack['num_cycles'] = $pack['num_cycles'];
		}
		// save member data including coupon etc, MUST save after all validation passed, we dont want any 
		// unwanted value in member object unless its a valid upgrdae
		$mgm_member = mgm_save_partial_fields(array('on_extend'=>true),'mgm_extend_field', $selected_pack['cost']);	
		
		// is using a coupon ? reset prices
		if($selected_pack!==false && isset($mgm_member->extend['coupon']['id'])){		
			// cost				
			if( isset($mgm_member->extend['coupon']['cost']) ){
				// original
				$selected_pack['original_cost'] = $selected_pack['cost'];
				// payable
				$selected_pack['cost'] = $mgm_member->extend['coupon']['cost'];
			}
			// duration	
			if( isset($mgm_member->extend['coupon']['duration']) )
				$selected_pack['duration'] = $mgm_member->extend['coupon']['duration'];
			// duration type	
			if( isset($mgm_member->extend['coupon']['duration_type']) )
				$selected_pack['duration_type'] = $mgm_member->extend['coupon']['duration_type'];
			// membership type	
			if( isset($mgm_member->extend['coupon']['membership_type']) )
				$selected_pack['membership_type'] = $mgm_member->extend['coupon']['membership_type'];
			// billing cycles, issue#478	
			if( isset($mgm_member->extend['coupon']['num_cycles']) )
				$selected_pack['num_cycles'] = $mgm_member->extend['coupon']['num_cycles'];		
				
			// trial on	
			if( isset($mgm_member->extend['coupon']['trial_on']) )
				$selected_pack['trial_on'] = $mgm_member->extend['coupon']['trial_on'];
 			// trial cost	
			if( isset($mgm_member->extend['coupon']['trial_cost']) )
				$selected_pack['trial_cost'] = $mgm_member->extend['coupon']['trial_cost'];
			// trial duration type	
			if( isset($mgm_member->extend['coupon']['trial_duration_type']) )
				$selected_pack['trial_duration_type'] = $mgm_member->extend['coupon']['trial_duration_type'];
			// trial duration	
			if( isset($mgm_member->extend['coupon']['trial_duration']) )
				$selected_pack['trial_duration'] = $mgm_member->extend['coupon']['trial_duration'];	
			// trial billing cycles	
			if( isset($mgm_member->extend['coupon']['trial_num_cycles']) )
				$selected_pack['trial_num_cycles'] = $mgm_member->extend['coupon']['trial_num_cycles'];		
				
			// mark pack as coupon applied
			$selected_pack['coupon_id'] = $mgm_member->extend['coupon']['id'];				
		}// end coupon
				
		// start html
		$html = '<table width="100%" cellpadding="3" cellspacing="0" border="0" align="center" class="form-table">';		
		// free package
		if (($selected_pack['cost'] == 0 || $selected_pack['membership_type'] == 'free') && in_array('mgm_free', $a_payment_modules) && mgm_get_module('mgm_free')->enabled=='Y') {	
			// html		
			$html .= '<tr><th>' . __('Create a free account: ','mgm') . ucwords($selected_pack['membership_type']) . '</th></tr>';			
			// module
			$module = 'mgm_free';
			// payments url
			$payments_url = mgm_get_custom_url('transactions');			
			// if tril module selected and cost is 0 and free moduleis not active
			if($selected_pack['membership_type'] == 'trial'){
				// check
				if(in_array('mgm_trial', $a_payment_modules)){
					// module
					$module = 'mgm_trial';
				}
			}
			// query_args
			$query_args = array('method' => 'payment_return', 'module'=>$module, 
			                    'custom' => implode('_', array($user->ID, $selected_pack['duration'], $selected_pack['duration_type'], $pack_id)));
			// redirector
			if(isset($_REQUEST['redirector'])){
				// set
				$query_args['redirector'] = $_REQUEST['redirector'];
			}
			// redirect to module to mark the payment as complete
			$redirect = add_query_arg($query_args, $payments_url);			
			// redirect
			if (!headers_sent()) {							
				@header('location: ' . $redirect);
			}else{
			// js redirect
				$html .= '<script type="text/javascript">window.location = "'. $redirect .'";</script><div>' . $packs_obj->get_pack_desc($pack) . '</div>';
			}			
		} else {		
		// paid package, generate payment buttons
			// set html	
			$html .= '<tr><th style="height:30px">' . $packs_obj->get_pack_desc($selected_pack) . '</th></tr>';
			// coupon			
			if(isset($mgm_member->extend['coupon']['id'])){	
				// set html 
				$html .= '<tr><th style="height:30px">' . sprintf(__('Using Coupon "%s" - %s','mgm'),$mgm_member->extend['coupon']['name'], $mgm_member->extend['coupon']['description']) . '</th></tr>';
			}
			// set html
			$html .= '<tr><th style="height:30px">' . __('Select Payment Gateway','mgm') . '</th></tr>';
		}
		
		
		// init 
		$payment_modules = array();			
		// active
		if(count($a_payment_modules)>0){
			// loop
			foreach($a_payment_modules as $payment_module){
				// not trial
				if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;	
				// consider only the modules assigned to pack
				if(isset($selected_pack['modules']) && !in_array($payment_module, (array)$selected_pack['modules'])) continue;			
				// store
				$payment_modules[] = $payment_module;					
			}
		}		
		
		// loop payment module if not free		
		if (count($payment_modules) && $selected_pack['cost']) {
			// transaction
			$tran_id = false;
			// loop
			foreach($payment_modules as $module) {
				// module
				$mod_obj = mgm_get_module($module,'payment');	
				// create transaction
				if(!$tran_id) $tran_id = $mod_obj->_create_transaction($selected_pack, array('user_id' => $user->ID));
				// set html				
				$html .="<tr><td>";			
				$html .= $mod_obj->get_button_subscribe(array('pack'=>$selected_pack,'tran_id'=>$tran_id)) ;
				$html .="</td></tr>";	
			}
		} else {
		// no module error
			if($selected_pack['cost']){		
				// set html	
				$html .= "<tr><td>".__('Error, no payment gateways active on extend page, notify administrator.','mgm')."</td></tr>";
			}
		}
		// html
		$html .= '</table>';
	// end post form	
	}else{
	// generate form ----------------------------------------------------------------
		// check error
		if($error){
			// html
			$html .= $error;
		}else{
			// generate 				
			// extend packages
			$extend_packages = '';			
			// loop		
			foreach ($active_packs as $pack) {				
				// default			
				$checked = '';	
				
				// checked			
				if($pack['id'] == $pack_id) {
					$checked = ' checked="checked"'; 
				}elseif((int)$pack['default'] == 1) {
					$checked = ' checked="checked"'; 
				}						
				
				// duration                      
				if ($pack['duration'] == 1) {
					$dur_str = rtrim($duration_str[$pack['duration_type']], 's');
				} else {
					$dur_str = $duration_str[$pack['duration_type']];
				}
				
				// encode pack
				$subs_opt_enc = mgm_encode_package($pack);
				
				//expand this if needed
				$css_link_format = '<link rel="stylesheet" href="%s" type="text/css" media="all" />';				
				$css_file = MGM_ASSETS_URL . 'css/mgm_form_fields.css';
				$extend_packages .= sprintf($css_link_format, $css_file);
				
				// free
				if (($pack['cost'] == 0 || strtolower($pack['membership_type']) == 'free') && in_array('mgm_free', $a_payment_modules) && mgm_get_module('mgm_free')->enabled=='Y') {
					// html
					$extend_packages .= '  
						<div style="margin: 10px 0px; overflow: auto;">
							<div style="width: 25px; float: left;">
								<input type="radio" ' . $checked . ' class="checkbox" name="subs_opt" value="'. $subs_opt_enc .'" />
							</div>
							<div style="width: 245px; float: left;">
								' . ucwords($pack['membership_type']) . '
							</div>
							<div class="clearfix"></div>
							<div class="mgm_subs_desc">
								' . mgm_stripslashes_deep($pack['description']) . '
							</div>
						</div>';
				} else {
					// html
					$extend_packages .= '  
						<div style="margin: 10px 0px; overflow: auto;">
							<div style="width: 25px; float: left;">
								<input type="radio" ' . $checked . ' class="checkbox" name="subs_opt" value="'. $subs_opt_enc .'" />
							</div>
							<div style="width: 245px; float: left;">
								' . $packs_obj->get_pack_desc($pack) . '
							</div>
							<div class="clearfix"></div>
							<div class="mgm_subs_desc">
								' . mgm_stripslashes_deep($pack['description']) . '
							</div>
						</div>';
				}
			}
			
			// show error
			if(!$extend_packages){
				// html
				$html .= '<div style="margin: 10px 0px; overflow: auto;">						
							<div style="width: 245px; float: left;">
								' . __('Sorry, no extend available.','mgm') . '
							</div>
						</div>';
				$html .= '<p>						
							<input type="button" name="cancel" onclick="window.location=\''.$cancel_url.'\'" value="&laquo; '.__('Cancel','mgm').'" class="button-primary" />&nbsp;					
						  </p>';
			}else{						
				// check errors if any:
				$html .= mgm_subscription_purchase_errors();			
				
				// form
				$html .= '<form action="'.$form_action .'" method="post" class="mgm_form"><div style="clear: both; overflow: hidden; padding-bottom: 5px;">';
				$html .= $extend_packages;
				// get coupon field
				$html .= mgm_get_partial_fields(array('on_extend'=>true),'mgm_extend_field');
				// html
				// $html .= '<input type="hidden" name="ref" value="'. md5($mgm_member->amount .'_'. $mgm_member->duration .'_'. $mgm_member->duration_type .'_'. $mgm_member->membership_type) .'" />';					
				$html .= '<input type="hidden" name="form_action" value="'. $form_action .'" />';					
						
				// set
				$html .= '<p>							
							<input type="submit" name="submit" value="'.__('Next','mgm').' &raquo;" class="button-primary" />&nbsp;&nbsp;
							<input type="button" name="cancel" onclick="window.location=\''.$cancel_url.'\'" value="&laquo; '.__('Cancel','mgm').'" class="button-primary" />&nbsp;					
						  </p>';
				// html
				$html .= '</div></form>';
			}	
			// end generate
		}
	}	
	
	// return
	return $html;
}	
//create purchase another button
function mgm_get_purchase_another_subscription_button($args = array()) {
	global $wpdb;	
	//ceck settings
	$settings = mgm_get_class('system')->setting;	
	// check
	if( !isset($settings['enable_multiple_level_purchase']) || (isset($settings['enable_multiple_level_purchase']) && $settings['enable_multiple_level_purchase'] != 'Y')) {
		return;
	}
	// ge  user
	$user = wp_get_current_user();		
	// check
	if(!$user->ID) $user = (isset($_GET['username']) ? get_userdatabylogin($_GET['username']) : false);		
	// validate
	if(!$user->ID) return;
	
	// userdata
	$username        = $user->user_login;
	$mgm_home        = get_option('siteurl');
	$mgm_member      = mgm_get_member($user->ID);
	$system          = mgm_get_class('system');
	$membership_type = mgm_get_user_membership_type($user->ID, 'code');
	$packs_obj       = mgm_get_class('subscription_packs');
	$packs           = $packs_obj->get_packs('upgrade');
	$duration_str    = $packs_obj->duration_str;
	$trial_taken     = $mgm_member->trial_taken;	
	// pack_ids	
	$pack_ids 		 = mgm_get_members_packids($mgm_member);
	$pack_membership_types = mgm_get_subscribed_membershiptypes($user->ID, $mgm_member);
	
	// query_arg
	$form_action = mgm_get_custom_url('transactions', false, array('action'=>'purchase_another', 'username'=> $username));
	// cancel 
	$cancel_url = mgm_get_custom_url('membership_details');
	// active modules
	$a_payment_modules = $system->get_active_modules('payment');
	
	// 	selected_subscription	
	$selected_subs = mgm_get_selected_subscription($args);	
	// first step show upgrade options
	if (!isset($_POST['submit']) || !isset($_POST['subs_opt'])) {
		// html
		$html  = '<p class="message register">'. __('Choose a Membership Package','mgm') .'</p>';		
 		// upgrade_packages
		$upgrade_packages = '';
		// loop		
		foreach ($packs as $pack) {					
			// default
			$checked = '';			
			// skip already purchased packs
		    if(in_array($pack['id'], $pack_ids)) continue;	
		    //skip same membership level subscriptions		   
		    if(in_array($pack['membership_type'], $pack_membership_types)) continue;			   
		    // do not show trial or free as upgradre
		    if($pack['membership_type'] == 'trial' || $pack['membership_type'] == 'free') continue;			
			// reset
			$checked = mgm_select_subscription($pack,$selected_subs);						
			// skip other when a package sent as selected
			if($selected_subs !== false){
				if(empty($checked)) continue;
			}			
			// checked
			if(!$checked){
            	$checked = ((int)$pack['default'] == 1 ? ' checked="checked"':''); 
			}
			// duration                      
			if ($pack['duration'] == 1) {
				$dur_str = rtrim($duration_str[$pack['duration_type']], 's');
			} else {
				$dur_str = $duration_str[$pack['duration_type']];
			}			
			// encode pack
			$subs_opt_enc = mgm_encode_package($pack);
			//expand this if needed
			$css_link_format = '<link rel="stylesheet" href="%s" type="text/css" media="all" />';				
			$css_file = MGM_ASSETS_URL . 'css/mgm_form_fields.css';
			$upgrade_packages .= sprintf($css_link_format, $css_file);
			// free
			if (($pack['cost'] == 0 || strtolower($pack['membership_type']) == 'free') && in_array('mgm_free', $a_payment_modules) && mgm_get_module('mgm_free')->enabled=='Y') {
				$upgrade_packages .= '  
							<div style="margin: 10px 0px; overflow: auto;">
								<div style="width: 25px; float: left;">
									<input type="radio" ' . $checked . ' class="checkbox" name="subs_opt" value="'. $subs_opt_enc .'" />
								</div>
								<div style="width: 245px; float: left;">
									' . ucwords($pack['membership_type']) . '
								</div>
								 <div class="clearfix"></div>
								 <div class="mgm_subs_desc">
									' . mgm_stripslashes_deep($pack['description']) . '
								 </div>
							</div>';
			} else {
				$upgrade_packages .= '  
							<div style="margin: 10px 0px; overflow: auto;">
								<div style="width: 25px; float: left;">
									<input type="radio" ' . $checked . ' class="checkbox" name="subs_opt" value="'. $subs_opt_enc .'" />
								</div>
								<div style="width: 245px; float: left;">
									' . $packs_obj->get_pack_desc($pack) . '
								</div>
								 <div class="clearfix"></div>
								 <div class="mgm_subs_desc">
									' . mgm_stripslashes_deep($pack['description']) . '
								 </div>
							</div>';
			}
		}
		
		// show error
		if(!$upgrade_packages){
			// html
			$html .= '<div style="margin: 10px 0px; overflow: auto;">						
						<div style="width: 245px; float: left;">
							' . __('Sorry, no packages available.','mgm') . '
						</div>
					</div>';
			$html .= '<p>						
						<input type="button" name="cancel" onclick="window.location=\''.$cancel_url.'\'" value="&laquo; '.__('Cancel','mgm').'" class="button-primary" />&nbsp;					
					  </p>';
		}else {
			//check erros if any:
			$error_field = mgm_request_var('error_field'); 
			if(!empty($error_field)) {
				$errors = new WP_Error();
				switch (mgm_request_var('error_type')) {
					case 'empty':
						$error_string = 'You must provide a '.$error_field;
						break;
					case 'invalid':
						$error_string = 'Invalid '.$error_field;
						break;	
				}				
				$errors->add( $error_field, __( '<strong>ERROR</strong>: '.$error_string, 'mgm' ));	
				$html .= mgm_set_errors($errors, true);					
			}
			// form
			$html .= '<form action="'.$form_action .'" method="post" class="mgm_form"><div style="clear: both; overflow: hidden; padding-bottom: 5px;">';
			$html .= '<input type="hidden" name="form_action" value="'. $form_action .'" />';
			$html .= $upgrade_packages;
			// get coupon field
			$html .= mgm_get_partial_fields(array('on_upgrade'=>true),'mgm_upgrade_field');
			// html
			$html .= '<input type="hidden" name="ref" value="'. md5($mgm_member->amount .'_'. $mgm_member->duration .'_'. $mgm_member->duration_type .'_'. $mgm_member->membership_type) .'" />';													
			// set
			$html .= '<p>						
						<input type="submit" name="submit" value="'.__('Next','mgm').' &raquo;" class="button-primary" />&nbsp;&nbsp;
						<input type="button" name="cancel" onclick="window.location=\''.$cancel_url.'\'" value="&laquo; '.__('Cancel','mgm').'" class="button-primary" />&nbsp;					
					  </p>';
			// html
			$html .= '</div></form>';
		}	
	}else {
		// second step, after post		
		// get subs data			
		$subs_opt_pack = mgm_decode_package($_POST['subs_opt']);
		extract($subs_opt_pack);
					
		// check		     
		$valid = false;
		// loop packs
		foreach ($packs as $pack) {
			// check
			//check pack id as well: issue#: 580
			if ($pack['cost'] == $cost && $pack['duration'] == $duration && $pack['duration_type'] == $duration_type && $membership_type == $pack['membership_type'] && $pack_id == $pack['id']) {
				$valid = true;				
				break;
			}
		}
		// error
		if (!$valid) {
		    wp_die(__('Invalid data passed','mgm'));
		}	
		
		// get object
		$mgm_member                         =  & new mgm_member($user->ID);
		$temp_membership                    = $mgm_member->_default_fields();
		$temp_membership['membership_type'] = $membership_type;
		$temp_membership['pack_id'] 		= $pack_id;
		
		//inserted an incomplete entry for the selected subscription type
		mgm_save_another_membership_fields($temp_membership, $user->ID);			
		
		//save coupon fields and update member object		
		$mgm_member = mgm_save_partial_fields_purchase_more($user->ID, $membership_type, $cost);
			
		// is using a coupon ? reset prices
		if(isset($mgm_member->upgrade['coupon']['id'])){			
			// main			
			if($pack && $mgm_member->upgrade['coupon']['cost']){
				// original
				$pack['original_cost'] = $pack['cost'];
				// payable
				$pack['cost'] = $mgm_member->upgrade['coupon']['cost'];
			}	
			if($pack && $mgm_member->upgrade['coupon']['duration'])
				$pack['duration'] = $mgm_member->upgrade['coupon']['duration'];
			if($pack && $mgm_member->upgrade['coupon']['duration_type'])
				$pack['duration_type'] = $mgm_member->upgrade['coupon']['duration_type'];
			if($pack && $mgm_member->upgrade['coupon']['membership_type'])
				$pack['membership_type'] = $mgm_member->upgrade['coupon']['membership_type'];
			//issue#: 478/ add billing cycles.	
			if($pack && isset($mgm_member->upgrade['coupon']['num_cycles']))
				$pack['num_cycles'] = $mgm_member->upgrade['coupon']['num_cycles'];		
				
			// trial	
			if($pack && $mgm_member->upgrade['coupon']['trial_on'])
				$pack['trial_on'] = $mgm_member->upgrade['coupon']['trial_on'];
			if($pack && $mgm_member->upgrade['coupon']['trial_cost'])
				$pack['trial_cost'] = $mgm_member->upgrade['coupon']['trial_cost'];
			if($pack && $mgm_member->upgrade['coupon']['trial_duration_type'])
				$pack['trial_duration_type'] = $mgm_member->upgrade['coupon']['trial_duration_type'];
			if($pack && $mgm_member->upgrade['coupon']['trial_duration'])
				$pack['trial_duration'] = $mgm_member->upgrade['coupon']['trial_duration'];	
			if($pack && $mgm_member->upgrade['coupon']['trial_num_cycles'])
				$pack['trial_num_cycles'] = $mgm_member->upgrade['coupon']['trial_num_cycles'];		
				
			// mark pack as coupon applied
			$pack['coupon_id'] = $mgm_member->upgrade['coupon']['id'];		
		}
			
		// crete hml
		$html = '<table width="100%" cellpadding="3" cellspacing="0" border="0" align="center" class="form-table">';
		
		// free
		if (($cost == 0 || $membership_type == 'free') && in_array('mgm_free', $a_payment_modules) && mgm_get_module('mgm_free')->enabled=='Y') {			
			$html .= '<tr><th>' . __('Create a free account: ','mgm') . ucwords($membership_type) . '</th></tr>';			
			$module = 'mgm_free';
			// payments url
			$payments_url = mgm_get_custom_url('transactions');			
			// if tril module selected and cost is 0 and free moduleis not active
			if($membership_type == 'trial'){
				if(in_array('mgm_trial', $a_payment_modules)){
					$module = 'mgm_trial';
				}
			}
			$redirect = add_query_arg(array('method'=>'payment_return', 'module'=>$module, 'custom' => ($user->ID . '_' . $duration . '_'  . $duration_type . '_' . $pack_id), 'redirector'=>$redirector), $payments_url);			
			if (!headers_sent()) {							
				@header('location: ' . $redirect);
			}
			// js redirect
			$html .= '<script type="text/javascript">window.location = "'. $redirect .'";</script><div>' . $packs_obj->get_pack_desc($pack) . '</div>';
			
		} else {			
			$html .= '<tr><th style="height:30px">' . $packs_obj->get_pack_desc($pack) . '</th></tr>';
			// coupon
			if(isset($mgm_member->upgrade['coupon']['id'])){
				$html .= '<tr><th style="height:30px">' . sprintf(__('Using Coupon "%s" - %s','mgm'),$mgm_member->upgrade['coupon']['name'], $mgm_member->upgrade['coupon']['description']) . '</th></tr>';
			}
			$html .= '<tr><th style="height:30px">' . __('Select Payment Gateway','mgm') . '</th></tr>';
		}
				// init 
		$payment_modules = array();			
		// when active
		if($a_payment_modules){
			// loop
			foreach($a_payment_modules as $payment_module){
				// not trial
				if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;	
				//consider only the modules assigned to pack
				if(isset($pack['modules']) && !in_array($payment_module, (array)$pack['modules'])) continue;			
				// store
				$payment_modules[] = $payment_module;					
			}
		}
		
		// loop payment mods if not free		
		if (count($payment_modules) && $cost) {
			// transaction
			$tran_id = 0;
			// loop
			foreach ($payment_modules as $module) {
				// module
				$mod_obj = mgm_get_module($module,'payment');	
				// create transaction
				if($tran_id==0){
					$tran_id = $mod_obj->_create_transaction($pack, array('is_another_membership_purchase' => true, 'user_id' => $user->ID));
				}				
				$html .="<tr><td>";			
				$html .= $mod_obj->get_button_subscribe(array('pack'=>$pack,'tran_id'=>$tran_id)) ;
				$html .="</td></tr>";	
			}
		} else {
			if($cost){			
				$html .= "<tr><td>".__('There are no payment gateways available at this time.','mgm')."</td></tr>";
			}
		}
		// html
		$html .= '</table>';
	}	
	// return    	
	return $html;
}

// email / wrapper for wp_mail
function mgm_mail($to, $subject, $message, $headers = '', $attachments = array()){	
	//issue #: 473:
	//appy mail settings only for mgm mails						
	add_filter('wp_mail_content_type', 'mgm_get_mail_content_type', 10);	
	// send mail
	return wp_mail( $to, $subject, $message, $headers, $attachments );	
}

// file download path
function mgm_get_file_url($filename) {
	return str_replace(trailingslashit(get_option('siteurl')), str_replace('\\', '/', ABSPATH), $filename);
}

// all posts
function mgm_get_posts(){
	global $wpdb;
	$sql = "SELECT ID, post_title FROM " . $wpdb->posts . "	WHERE post_status = 'publish' AND post_type IN ('page','post') ORDER BY post_title";
	$posts = $wpdb->get_results($sql);
	// return
	return $posts;
}

// mgm_format_currency
function mgm_format_currency($number){
	// strip 00
	$number = preg_replace('/\.00$/','', $number);
	// format
	if(preg_match('/\.\d+$/',$number))
		$number = number_format($number ,2, '.', ',');
		
	return $number;
}

// get 
function mgm_get_user_id() {
	get_currentuserinfo();
	global $current_user, $wpdb;

	if (isset($_GET['username'])) {
		$sql = "SELECT ID FROM " . $wpdb->users . " WHERE user_login = '" . $_GET['username'] . "'";
		$results = $wpdb->get_results($sql);
		$results = array_reverse($results);
		$row = array_pop($results);
		$user_ID = $row->ID;
	} else if (isset($_GET['user_id'])) {
		$user_ID = $_GET['user_id'];
	} else {
		$user_ID = $current_user->ID;
	}

	return $user_ID;
}

// login form box
function mgm_login_form($register_text='', $lostpassword_text='') {
	$mgm_system = mgm_get_class('system');
	// register
	$register_html='';
	if ($register_text) {
		//get urls from settings
		$register_link = mgm_get_custom_url('register');		
		$register_html = '
		<div id="mgm_register_div">
			<a class="mgm-register-link" href="' . $register_link . '">' . $register_text . '</a>
		</div>';
	}
	
	// loast password
	$lostpassword_html ='';
	if ($lostpassword_text) {
		//get urls from settings
		$lostpassword_link = mgm_get_custom_url('lostpassword');		
		$lostpassword_html = '
		<div id="mgm_lost_pass_div">
			<a class="mgm-lostpassword-link" href="' . $lostpassword_link . '">' . $lostpassword_text . '</a>
		</div>';
	}
	
	// build html
	$html = '<form class="mgm_form" id="mgm_login_form" action="' . mgm_get_custom_url('login') . '" method="post">
				<label>' . __('Username:','mgm') . '</label>
				<div>
					<input type="text" name="log" id="user_login" class="input" value="" tabindex="10" />
				</div>				
				<label>' . __('Password:','mgm') . '</label>
				<div>
					<input type="password" name="pwd" id="user_pass" class="input" value="" tabindex="20" />
				</div>	
				<br>			
				<div>
					<span id="remember_me_container"><input id="rememberme" type="checkbox" tabindex="90" value="forever" name="rememberme"/> '.__('Remember Me','mgm').'</span><br>
					<input class="mgm-login-button" type="submit" name="wp-submit" id="wp-submit" value="' . __('Login &raquo;','mgm') . '" tabindex="100" />';
	
	if(isset($mgm_system->setting['enable_post_url_redirection']) && $mgm_system->setting['enable_post_url_redirection'] == 'Y')
		$html .= '<input type="hidden" name="redirect_to" value="' . get_permalink() . '" />';
		
	$html .=	'</div>			
				<div>
					' . $register_html . $lostpassword_html . '
				</div>				
			</form>';	
	// issue#202 ----------------------------------------------
	// apply filter
	$html = apply_filters('mgm_login_form_html', $html);
	// end ----------------------------------------------------
	// return 
	return $html;
}

// register form box
function mgm_user_register_form($args=array(), $use_default_links = false) {	
	// hide from logged in user	
	if(is_user_logged_in())	return '';		
	
	// get system
	$system = mgm_get_class('system');
	$hide_custom_fields = ($system->setting['hide_custom_fields'] == 'Y') ? true : false;
	
	// default_register_fields
	$register_fields = mgm_get_config('default_register_fields',array());
	// get active custom fields on register
	$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true)));	
	
	// error_html
	$error_html = '';		
	// save
	if ( isset($_POST['method']) && $_POST['method'] == 'create_user' ) {		
		// load wp lib for register
		require_once( ABSPATH . WPINC . '/registration.php');
		// init
		$user_login = $user_email = '';		
		// loop to check		
		foreach($register_fields as $cfield=>$wfield){
			// set custom
			if(isset($_POST['mgm_register_field'][$cfield])){
				// set from custom
				$$wfield['name'] = $_POST['mgm_register_field'][$cfield];
			}else{
			// default
				$$wfield['name'] = $_POST[$wfield['name']];
			}	
		}		
		// get error
		$errors = mgm_register_new_user($user_login, $user_email);
		// no error
		if ( !is_wp_error($errors) ) {
			// get redirect
			$redirect = mgm_get_custom_url('login', $use_default_links, array('checkemail' => 'registered'));	
			// check default
			$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : $redirect;
			// redirect
			wp_safe_redirect( $redirect_to );
			// exit
			exit();
		}
		// errors		
		$error_html = mgm_set_errors($errors, true);
	}	

	// html	
	
	// commented the below lines for issue#: 471
	// $form_action = get_permalink();		
	// set current url
	// if(!$form_action) { $form_action = mgm_current_url(); }
	// issue#: 532
	if(isset($args['package'])) {
		$form_action = get_permalink();
	}else
		$form_action = mgm_get_custom_url('register');
	// package code:	
	$package = mgm_request_var('package');	
	if(!empty($package)) {
		$form_action = add_query_arg(array('package' => $package), $form_action);
	}
	// membership code:
	$membership = mgm_request_var('membership');	
	if(!empty($membership)) {
		$form_action = add_query_arg(array('membership' => $membership), $form_action);
	}
	// wordpress register
	$wordpres_form = mgm_check_wordpress_login();
	
	// 	get row row template
	$form_row_template = $system->get_template('register_form_row_template');
	
	// get template row filter, mgm_register_form_row_template for custom, mgm_register_form_row_template_wordpress for wordpress
	$form_row_template = apply_filters('mgm_register_form_row_template'.($wordpres_form ? '_wordpress': ''), $form_row_template);	
	
	// get mgm_form_fields generator
	$form_fields = & new mgm_form_fields(array('wordpres_form'=>(bool)$wordpres_form,'form_row_template'=>$form_row_template,'cf_register_page'=>$cf_register_page,'args'=>$args));
	
	// default
	$form_html = '';
	
	// loop default register fields, create each if they are not defined in custom fields
	foreach($register_fields as $cfield=>$wfield){				
		// set not found
		$captured = false;
		// first check if in custom fields
		foreach($cf_register_page as $rfield){			
			// if
			if($rfield['name'] == $cfield){
				// skip custom fields by settings call
				// if($hide_custom_fields && $cfield['name'] != 'subscription_options') continue;
				if($hide_custom_fields && !in_array($field['name'], array('subscription_options','payment_gateways'))) continue;
				// set found
				$captured = true;				
				// do nothing
				break;
			}
		}	
		
		// not found		
		if(!$captured){			
			// create element
			$form_html .= str_replace(array('[user_field_wrapper]','[user_field_label]','[user_field_element]'),array($wfield['name'],mgm_stripslashes_deep($wfield['label']),$form_fields->get_field_element($wfield,'mgm_register_field')),$form_row_template);			
		}
	}		
	
	// register custom fields
	$form_html .= mgm_register_form_extend(true, $form_fields);
	
	// output form	
	$html = '<div class="mgm_register_form">
				' . $error_html . '
				<form class="mgm_form" name="registerform" id="registerform"  action="' . $form_action . '" method="post">	               
				   ' . $form_html . '
					<p><input class="mgm-register-button" type="submit" name="wp-submit" id="wp-submit" value="' . __('Register &raquo;','mgm') . '" tabindex="100" /></p>		
					<input type="hidden" name="method" value="create_user">	
				</form>
			 </div>';	
			 
	// attach scripts,		
	$html .= mgm_attach_scripts(true);
	// print links	
	$login_link = mgm_get_custom_url('login', $use_default_links);	
	// link
	if (get_option('users_can_register')){		
		$lost_password_link = mgm_get_custom_url('lostpassword', $use_default_links);	
		$html .='<p class="have-account">Already have an account? <a class="mgm-login-link" href="#login-box" title="'. __('Log in','mgm') .'">'. __('Log in','mgm').'.</a></p>';
		//$html .='<a class="mgm-lostpassword-link" href="'.$lost_password_link.'" title="'. __('Password Lost and Found','mgm') .'">'.__('Lost your password','mgm').'</a>';
	}else {
		$html .='<a class="mgm-login-link" href="'.$login_link.'">'.__('Log in','mgm').'</a>';
	}
	
	// apply filter
	$html = apply_filters('mgm_register_form_html', $html);
	// return
	return $html;
}

// register form box
function mgm_transactions_page($args=array()) {		
	// return 
	return mgm_get_payment_html(true);exit;
}
	
// get links
function mgm_get_login_register_links() {
	$html  = '<div id="mgm_login_register_links">';
	$html .= mgm_get_login_link();
	$html .= '&nbsp;';
	$html .= mgm_get_register_link();
	$html .= '</div>';
	return $html;
}

// login 
function mgm_get_login_link() {
	$params = array();
	$login_link = mgm_get_custom_url('login');
	$permalink = get_permalink();
	//dont redirect back to login/register
	if(!in_array(trailingslashit($permalink), array(trailingslashit($login_link), mgm_get_custom_url('register'))))
		$params['redirect_to'] = rawurlencode($permalink);
	
	$login_link = add_query_arg($params, $login_link);
	return '<span id="mgm_login_link"><a class="mgm-login-link" href="' . $login_link . '">' . __('[ Login ]', 'mgm') . '</a></span>';
}

// register
function mgm_get_register_link() {
	$params = array();
	$reg_link = mgm_get_custom_url('register');
	$permalink = get_permalink();
	if(trailingslashit($reg_link) != trailingslashit($permalink))
		$params['mgm_redirector'] = rawurlencode($permalink);
	
	$register_link = add_query_arg($params, $reg_link);
	return '<span id="mgm_register_link"><a class="mgm-register-link" href="' . $register_link . '">' . __('[ Register ]', 'mgm') . '</a></span>';
}

// superuser
/*******************************deprecated****************
function mgm_superuser($user_id = 0) {
	if (!$user_id) {
		get_currentuserinfo();
		global $current_user;
		$user_id = $current_user->ID;
	}

	$user = new WP_User($user_id);
	if (true == $user->has_cap('administrator')) {
		return true;
	} else {
		return false;
	}
}
**********************************************************/
// check post purchasable
function mgm_post_is_purchasable($post_id = false) {
	get_currentuserinfo();
	global $current_user;
	// default
	$return = false;
	// get post id
	if (!$post_id) {
		$post_id = get_the_ID();
	}
	
	// echo $post_id;
	// post setting object
	$mgm_post = mgm_get_post($post_id);	
	
	// purchasable
	if ($mgm_post->purchasable=='Y') {
		// check expiry
		if ($expiry = $mgm_post->purchase_expiry) {
			if (strtotime($expiry) > time()) {
				$return = true;
			}
		} else {
			$return = true;
		}
	}
	// return
	return $return;
}

// rss token
function mgm_get_rss_token() {
	get_currentuserinfo();
	global $wpdb, $current_user;

	$token = false;

	if ($current_user->ID) {
		// member
		$mgm_member = mgm_get_member($current_user->ID);
		$token = $mgm_member->rss_token;
		if (!$token) {
			$token = mgm_create_rss_token();
			$mgm_member->rss_token = $token;
			// update option
			// update_user_option($current_user->ID,'mgm_member',$mgm_member, true);	
			$mgm_member->save();			
		}
	}
	// return
	return $token;
}
//create rss token
function mgm_create_rss_token() {
	$salt = 'rss token salt';
	$num = mt_rand(10000,15000);
	$string = $num . $salt . $num;
	return md5($string);
}
// do use rss
function mgm_use_rss_token() {	
	return (bool)(mgm_get_class('system')->setting['use_rss_token'] == 'Y');
}
// unchecked
function mgm_get_user_membership_type($user_id=false, $return='label') {
	// user
	if (!$user_id) {
		$user_id = mgm_get_user_id();
	}
	// default
	$membership_type = 'guest';
	// user
	if ($user_id) {
		$user = get_userdata($user_id);
		$mgm_member = mgm_get_member($user_id);
		$expiry = false;
		$membership_type = 'free';
		// member		
		if (isset($mgm_member)) {		
			// loop	
			foreach ($mgm_member as $key=>$value) {
				if ($key == 'membership_type' && $value) {
					$membership_type = strtolower($value);
				} else if ($key == 'expiry_date') {
					$expiry = $value;
				}
			}
		}
		// type
		if ($membership_type) {
			if ($mgm_member->status != MGM_STATUS_ACTIVE) {
				$membership_type = 'free';
			} else if ($expiry && time() > mysql2date('U', $expiry)) {
				$membership_type = 'free';
			}
		}		
	}
	
	// return
	if($return == 'label'){
		return mgm_get_class('membership_types')->get_type_name($membership_type);
	}elseif($return == 'nicecode'){
		return mgm_get_class('membership_types')->get_type_nicecode($membership_type);	
	}else{
		return $membership_type;
	}
}
// unchecked
function mgm_check_redirect_condition($system=NULL) {	
	// user
	$user = wp_get_current_user();
	
	// system
	if(!$system){
		$system = mgm_get_class('system');
	}

	// init
	$return      = false;
	$current_url = mgm_current_url();	
	
	// check 
	if (!(isset($_GET['page']) && $_GET['page'] == 'mgm/membership/content')) {		
		if ($system->setting['no_access_redirect_loggedin_users'] != '' || $system->setting['no_access_redirect_loggedout_users'] != '') {
			$return = true;			
			if ($system->setting['no_access_redirect_loggedin_users'] && !$user->ID) {				
				$return = true;
			} else if ($system->setting['no_access_redirect_loggedout_users'] && $user->ID) {				
				$return = true;
			} else if ($system->setting['redirect_on_homepage']=='N' && is_home()) {
				$return = true;
			}
			
			// check token request or feed
			if ((mgm_request_var('token') && mgm_use_rss_token()) || is_feed()) {
				$return = false;
			}			
			
			// only for single, page
			if(is_single() || is_page()){
				$return = true;
			}else{
				$return = false;
			} 
		}
	}
	
	// return
	return $return;
}
// unchecked
function mgm_current_url() {
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = mgm_strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}
// unchecked
function mgm_strleft($s1, $s2) {
	return substr($s1, 0, strpos($s1, $s2));
}

// object to array
function mgm_object2array( $object ){
	if( !is_object( $object ) && !is_array( $object ) ){
		return $object;
	}
	
	if( is_object( $object ) ){
		$object = get_object_vars( $object );
	}
	
	return array_map( 'mgm_object2array', $object );
}

/**
 * Magic Members parse download tag
 *
 * @package MagicMembers
 * @since 2.5
 * @desc parse download tag embeded in pots/page, works via wp shortcode api 
 * @param string post/page content
 * @return string modified content
 */ 
function mgm_download_parse($content) {
	global $wpdb;
	// get system
	$system = mgm_get_class('system');
	// hookname
	$hook = ($system->setting['download_hook'] ? $system->setting['download_hook'] : 'download');
	// match
	if (substr_count($content,"[" . $hook . "#")) {
		// get downloads	
		$downloads = $wpdb->get_results('SELECT id, title, filename, post_date, members_only, user_id,code FROM `' . TBL_MGM_DOWNLOAD . '`');
		// if has downloads
		if ($downloads) {
			$patts = $subs = array();
			// loop
			foreach($downloads as $d) {
				// url
				// $download_url = add_query_arg(array('code'=>($d->code ? $d->code : $d->id )), mgm_home_url('download'));
				// $download_url = trailingslashit(get_option('siteurl')) . add_query_arg(array('code'=>($d->code ? $d->code : $d->id )), $slug); iss#602 fix, use wp home_url
				// issue #364:
				$download_slug = !empty($system->setting['download_slug']) ? $system->setting['download_slug'] : 'download'; 
				// download url
				$download_url = mgm_home_url(add_query_arg(array('code'=>($d->code ? $d->code : $d->id )), $download_slug));
				// trim last slash
				$download_url = rtrim($download_url, '/');
				
				// Download link
				$link    = '<a href="' . $download_url . '" title="' . $d->title . '" >' . $d->title . '</a>';
				$patts[] = "[" . $hook . "#" . $d->id . "]";
				$subs[]  = $link;
				
				// image
				$download_image_button = sprintf('<img src="%s" alt="%s" />',MGM_ASSETS_URL . 'images/download.gif', $d->title);
				// add filter
				$download_image_button = apply_filters('mgm_download_image_button', $download_image_button, $d->title);
				// Image link
				$link    = '<a href="'.$download_url . '" title="'.$d->title.'">'.$download_image_button.'</a>';
				$patts[] = "[" . $hook . "#" . $d->id . "#image]";
				$subs[]  = $link;
				
				// Button link
				$link    = '<input type="button" name="btndownload-'.$d->id.'" onclick="window.location=\''.$download_url.'\'" title="'.$d->title.'" value="'.__('Download','mgm').'"/>';
				$patts[] = "[" . $hook . "#" . $d->id . "#button]";
				$subs[]  = $link;

				// Download link with filesize
				$link    = '<a href="'.$download_url . '" title="'.$d->title.'" >'.$d->title.' - '.mgm_file_get_size($d->filename).'</a>';
				$patts[] = "[" . $hook . "#" . $d->id . "#size]";
				$subs[]  = $link;

				// Download url only
				$link    = $download_url ;
				$patts[] = "[" . $hook . "#" . $d->id . "#url]";
				$subs[]  = $link;
			}
			// replace
			$content = str_replace($patts, $subs, $content);
		}
	}
	// return
	return $content;
}

/**
 * Magic Members get file size
 *
 * @package MagicMembers
 * @since 2.5
 * @desc get downloadble file size
 * @param string file path
 * @return string size formatted
 */ 
function mgm_file_get_size($fileurl) {
	// init
	$size = NULL;
	// s3 file
	if(mgm_is_s3_file($fileurl)){
		$size = mgm_get_s3file_size($fileurl);
	}else{
		// path
		$filepath = str_replace(trailingslashit(get_option('siteurl')),"./", $fileurl);
		// file exists
		if (file_exists($filepath)) {
			$size = filesize($filepath);
		}
	}	
	// size
	if ($size) {
		// bytes array
		$bytes = array('bytes','KB','MB','GB','TB');
		// loop
		foreach($bytes as $byte) {
			// check
			if($size > 1024){
				$size = $size / 1024;
			} else {
				break;
			}
		}
		// return	
		return round($size, 2) . ' ' . $byte;
	}
	// error
	return '';
}
// download posts
function mgm_get_download_posts($download_id) {
	global $wpdb;
	// sql
	$sql = 'SELECT post_id FROM `' . TBL_MGM_DOWNLOAD_POST_ASSOC . '` WHERE download_id = ' . $download_id;
	// fetch
	return $wpdb->get_results($sql);
}

/**
 * Magic Members verify file download
 *
 * @package MagicMembers
 * @since 2.5
 * @desc verify file download
 * @param string download code
 * @return none
 */ 
function mgm_download_file($code) {
	get_currentuserinfo();
	global $wpdb, $current_user;
	
	// allow default
	$allow_download = true;
	// data fetch
	if ($download = mgm_get_download($code)) {
		// log		
		// mgm_pr($download);
		// for members
		if ($download->members_only=='Y' ) {
			// reset as restricted
			$allow_download = false;
			// user check
			if ($current_user->ID) {
				// allow admin
				if (isset($current_user->caps['administrator'])) { // is_super_admin
					$allow_download = true;
				}else{
					// get post mapped
					$posts = mgm_get_download_posts($download->id);
					// loop	
					foreach ($posts as $post) {
						// only  when user has access to mapped post
						if (mgm_user_has_access($post->post_id)) {
							// set access
							$allow_download = true;
							// skip
							break;
						}
					}
				} 
			}
		}// end member restriction check
		
		// check expire
		$download_expired = false;
		// allowed alreay
		if($allow_download){			
			// expire date
			if(!is_null($download->expire_dt)){
				// expired
				if(time() > strtotime($download->expire_dt)){
					$download_expired = true;					
				}
			}
		}
		
		// allowed
		if ($allow_download && !$download_expired) {
			// file
			$filepath = mgm_get_abs_file($download->filename);						
			// check
			if (file_exists($filepath)) {									
				// do the  download				
				// mgm_force_download($file);	
				mgm_force_download_restart($filepath); 
				// delete if s3 file
				if(mgm_is_s3_file($filepath)){
					// delete
					mgm_delete_file($filepath);
				}
				// exit
				exit();			
			} else {				
				_e('You can not download this file because it does not exist. Please notify the Administrator.', 'mgm'); exit();
			}
		} else {
			// message			
			_e(sprintf('You can not download this file because %s',($download_expired ? 'it expired' : 'you do not have access')),'mgm'); exit();
		}
	} else {
		die(__('You can not download this file because you do not have access','mgm'));
	}
}

/**
 * Magic Members force download
 *
 * @package MagicMembers
 * @since 2.5
 * @desc force download
 * @param string filepath
 * @return none
 */ 
function mgm_force_download($file){
	global $mgm_mimes;
	
	// file name	
	$filename = basename($file);
	// the file extension
	$fparts = explode('.', $filename);
	$extension = end($fparts);
		
	// default mime if we can't find it
	if ( ! isset($mgm_mimes[$extension])){
		$mime = 'application/octet-stream';
	}else{
		$mime = (is_array($mgm_mimes[$extension])) ? $mgm_mimes[$extension][0] : $mgm_mimes[$extension];
	}
	// ie
	if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")){
		header('Content-Type: "'.$mime.'"');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Content-Transfer-Encoding: binary");
		header('Pragma: public');
		header("Content-Length: ".@filesize($file));
	}else{
		header('Content-Type: "'.$mime.'"');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Pragma: no-cache');
		header("Content-Length: ".@filesize($file));
	}	
	// print
	print file_get_contents($file);
	// exit
	exit();
}

/**
 * Magic Members force download with restart functionlity
 *
 * @package MagicMembers
 * @since 2.5
 * @desc force download with restart functionlity
 * @param string filepath
 * @return none
 */ 
function mgm_force_download_restart($fileLocation){
	global $mgm_mimes;
	if( connection_status()!= 0 ) return false;
	//extended server configurations:
	ini_set('max_execution_time', 	'7200');
	ini_set('upload_max_filesize', 	'1000M');
	ini_set('post_max_size', 		'1000M');		
	$maxSpeed = 2000;
	$doStream = false;
	$fileName = basename($fileLocation);
	$extension = strtolower(end(explode('.',$fileName)));
	// default mime if we can't find it
	if ( ! isset($mgm_mimes[$extension])){
		$contentType = 'application/octet-stream';
	}else{
		$contentType = (is_array($mgm_mimes[$extension])) ? $mgm_mimes[$extension][0] : $mgm_mimes[$extension];
	}
	header("Cache-Control: public");
	header("Content-Transfer-Encoding: binary\n");
	header("Content-Type: $contentType");
	$contentDisposition = 'attachment';
	if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
		$fileName= preg_replace('/\./', '%2e', $fileName, substr_count($fileName,'.') - 1);
		header("Content-Disposition: $contentDisposition;filename=\"$fileName\"");
	} else {
		header("Content-Disposition: $contentDisposition;filename=\"$fileName\"");
	}
	header("Accept-Ranges: bytes");
	$range = 0;
	$size = filesize($fileLocation);	
	if(isset($_SERVER['HTTP_RANGE'])) {
		list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']);
		str_replace($range, "-", $range);
		$size2=$size-1;
		$new_length=$size-$range;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range$size2/$size");		
	} else {
		$size2=$size-1;
		header("Content-Range: bytes 0-$size2/$size");
		header("Content-Length: ".$size);		
	}
	if ($size == 0 ) { 		
		die('Empty file! download aborted');
	}
	if(function_exists('set_magic_quotes_runtime'))
		set_magic_quotes_runtime(0);
	$fp = fopen("$fileLocation","rb");
	fseek($fp,$range);						
	while(!feof($fp)) {
		set_time_limit(0);	
		ini_set('memory_limit','1024M');	
		echo fread($fp,1024*$maxSpeed);		
		ob_flush();		
		flush();		
		usleep(1000);		
		ob_end_flush();	//keep this as there were some memory related issues(#545)						
	}	
	
	fclose($fp);		
	return((connection_status()==0) && !connection_aborted());
}

// read file
function mgm_readfile($file){
	// We don't need to write to the file, so just open for reading.
	/*$fp = fopen( $file, 'r' );

	// Content
	$file_data = fread( $fp, filesize($file) );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );*/
	
	$handle = @fopen($file, "rb");
	if ($handle) {
	   while (!feof($handle)) {
		   $buffer .= fgets($handle, 4096);		   
	   }
	   fclose($handle);
	}

	// return
	return $buffer;
}
// check s3 file
function mgm_is_s3_file($fileurl){
	//return ( preg_match('/^https:\/\/s3\.amazonaws\.com/',$fileurl) || preg_match('/^s3file_/', basename($fileurl)) );
	return ( preg_match('/^https:\/\/s3\.amazonaws\.com/',$fileurl) || 
			 preg_match('/^s3file_/', basename($fileurl)) ||
			 preg_match('/https:\/\/s3(.*)\.amazonaws\.com/', $fileurl) //Eg: https://s3-eu-west-1.amazonaws.com/ 			 
			 );		 
}
// abs path
function mgm_get_abs_file($fileurl) {
	// check s3 file	
	if(mgm_is_s3_file($fileurl)){
		return $fileurl = mgm_download_s3file($fileurl);
	}	
	// return 
	return str_replace(trailingslashit(get_option('siteurl')), str_replace('\\', '/', ABSPATH), $fileurl);
}
// get download
function mgm_get_download($code=false) {
	global $wpdb;
	// init
	$row = new stdClass();
	// set
	$row->id = $row->title = $row->filename = $row->post_date = $row->members_only = $row->user_id = false;	
	// check
	if ($code) {
		// sql
		$sql = "SELECT id, title, filename, post_date, members_only, user_id, expire_dt  
				FROM `" . TBL_MGM_DOWNLOAD . "`	WHERE code = '{$code}'";
		// get 		
		$row = $wpdb->get_row($sql);
	}		
	// return 
	return $row;
}

// get s3 info
function mgm_get_s3file_info($fileurl){
	// system 
	$system = mgm_get_class('system');
	// set keys
	$aws_key = $system->setting['aws_key'];
	$aws_secret_key = $system->setting['aws_secret_key'];	
	// Include the SDK
	require_once MGM_LIBRARY_DIR . 'third_party/awssdk/sdk.class.php';	
	// s3 object
	$s3 = new AmazonS3($aws_key, $aws_secret_key);
	// get urlpath
	$urlpath = parse_url($fileurl, PHP_URL_PATH);
	// get parts
	$url_parts = explode('/', $urlpath);
	// bucket
	do{
		$bucket = array_shift($url_parts);
	}while(empty($bucket));	
	// filename, including path
	$filename  = implode('/',$url_parts);	
	// object
	$s3_info = new stdClass;
	// set vars
	$s3_info->s3       = $s3;
	$s3_info->bucket   = $bucket;
	$s3_info->filename = $filename;
	// return
	return $s3_info; 
}
/**
 * Magic Members download s3 file to local file
 *
 * @package MagicMembers
 * @since 2.5
 * @desc force download s3 file to local file
 * @param string filepath
 * @return string local filepath
 */ 
function mgm_download_s3file($fileurl){
	// s3 info
	$s3_info = mgm_get_s3file_info($fileurl);
	
	// local file
	$localfile = MGM_FILES_DOWNLOAD_DIR . 's3file_' . time() . '_' . basename($s3_info->filename);
	
 	// download
	$response = $s3_info->s3->get_object($s3_info->bucket, $s3_info->filename, array('fileDownload' => $localfile));
	
	// Success?
	// var_dump($response);
	if($response->isOK()){
		return $localfile;
	}
	// error
	return false;
}
/**
 * Magic Members get size of s3 file
 *
 * @package MagicMembers
 * @since 2.5
 * @desc force get size of s3 file
 * @param string filepath
 * @return string local filepath
 */ 
function mgm_get_s3file_size($fileurl){
	// s3 info
	$s3_info = mgm_get_s3file_info($fileurl);
	
	// response
	$response = $s3_info->s3->get_object_filesize($s3_info->bucket, $s3_info->filename);
	
 	// check
	if($response){
		return $response;
	}
	// error
	return false;
}
// user access to post
function mgm_user_has_access($post_id = false, $allow_on_purchasable = false) {
	get_currentuserinfo();
	global $current_user, $user_data, $wpdb;

	// get user
	if (isset($_GET['username']) && isset($_GET['password'])) {
		$user = wp_authenticate($_GET['username'], $_GET['password']);
	} else if (mgm_request_var('token') && mgm_use_rss_token()) {
		$user = mgm_get_user_by_token(mgm_request_var('token'));
	} else {
		$user = $current_user;
	}
		
	// default return
	$return = false;
	
	// post id
	if (!$post_id) {
		$post_id = get_the_id();
	}        
	// if post			
	if ($post_id) {
		// get post data
        $post = get_post($post_id);        
		// check if purchable    
		$purchasable = mgm_post_is_purchasable($post_id);		
		// check publish status
		$is_published = ($post->post_status == 'publish');		
		
		// allow if set
		if ($allow_on_purchasable && $purchasable) {// if set
			$return = true;
		} else if (isset($user->caps['administrator'])) {// admin
			$return = true;
		} else if (!$is_published) {// not published
			$return = false;
		} else { // check other access					
			// get mgm post data				
			$mgm_post = mgm_get_post($post_id);
			
			// allowed types
			$membership_types = $mgm_post->get_access_membership_types();
			
			$arr_memtypes = array();
			// logged in user
			if ($user->ID > 0){		
				// current user type
				$membership_type = mgm_get_user_membership_type($user->ID, 'code'); //status is implied through the type.				
				$arr_memtypes = mgm_get_subscribed_membershiptypes($user->ID);
			} // end user check
			
			// not defined
			if (empty($arr_memtypes)) {
				$arr_memtypes[] = 'guest';
			}
				
			// check accessible membership types for current post first
			//if (in_array($membership_type, $membership_types)) {
			if (array_diff($membership_types, $arr_memtypes) != $membership_types) { // if any match found
				// set access
				$return = true;
				// check hide content
				if ($user->ID > 0){	
					$mgm_member = mgm_get_member($user->ID);					
					// if set
					/*if ($pack_join = $mgm_member->join_date) {
						// if hide old content is set in subscription type
						if ($hide_old_content =  $mgm_member->hide_old_content) {
						   $post_date = strtotime($post->post_date);
						   // reset no access
						   $return = false;	
						   // join date, TODO, We have to make it take last_active_date or similar for DRIP posts					   
						   if ($pack_join < $post_date) {
							   $return = true;    
						   }
						}
					 }*/
					$return = mgm_check_post_packjoin($mgm_member, $post);
					 if(!$return) {
					 	//check other memberships if any:
					 	if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
							foreach ($mgm_member->other_membership_types as $key => $memtypes) {
								
								$memtypes = mgm_convert_array_to_memberobj($memtypes, $user->ID);
								if($memtypes->status == MGM_STATUS_ACTIVE) {
									$return = mgm_check_post_packjoin($memtypes, $post);
									if($return)//stop if any of the packs returned true
										break;
								}
								
							}
					 	}
					 }
				}	 
			}			
			
			// on access, also check duration and type
			if ($return == true && $user->ID > 0) {
				// check membership wise min duration
				/*
				$mt_access_delay = (int)$access_delay[$membership_type];
				// delay
				if ($mt_access_delay > 0) {
					$reg     = $user->user_registered;
					$reg     = mktime(0,0,0,substr($reg, 5, 2), substr($reg, 8, 2), substr($reg, 0, 4));
					$user_at = $reg + (86400*$mt_access_delay);
					if ($user_at >= time()) {
						$return = false;
					}
				}*/
				$access_delay    = $mgm_post->access_delay;
				if(in_array( $mgm_member->membership_type, $membership_types ) && $mgm_member->status == MGM_STATUS_ACTIVE ) {
					$return = mgm_check_post_access_delay($mgm_member, $user, $access_delay);	
				}else 
					$return = false;
				
				if(!$return) {					
					//check other memberships if any:
				 	if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
						foreach ($mgm_member->other_membership_types as $key => $memtypes) {
							$memtypes = mgm_convert_array_to_memberobj($memtypes, $user->ID);
							if(in_array( $memtypes->membership_type, $membership_types ) && $memtypes->status == MGM_STATUS_ACTIVE ) {
								
								$return = mgm_check_post_access_delay($memtypes, $user, $access_delay);									
								if($return)//stop if any of the packs returned true
									break;
							}
							
						}
				 	}
				}
			}
			
			// if not accessible yet, check purchasable
			if ($return == false && $user->ID > 0) {
				// on purchasable, check user has purchased and access expired
				if($purchasable){
					// true/false
					$return = mgm_user_has_purchased_post($post_id, $user->ID);
				}						
			}				
		}
	}
	$user_id = (isset($user->ID)) ? $user->ID : 0;
	// filter
    $return = apply_filters('mgm_user_has_access_additional', $return, $post_id, $user_id, $allow_on_purchasable);

	// return
	return $return;
}

// mgm_member_unsubscribe
function mgm_member_unsubscribe(){
	// user_id from post
	extract($_POST);	
	// system	
	$system     = mgm_get_class('system');	
	$packs_obj  = mgm_get_class('subscription_packs');	
	$dge        = ($system->setting['disable_gateway_emails'] == 'Y') ? true : false;
	
	// find user
	$user = get_userdata($user_id);	
	$mgm_member = mgm_get_member($user_id);
	// multiple membesrhip level update:		
	if(isset($_POST['membership_type']) && $mgm_member->membership_type != $_POST['membership_type']){
		$mgm_member = mgm_get_member_another_purchase($user_id, $_POST['membership_type']);	
	}	
		
	// get pack
	if($mgm_member->pack_id){
		$subs_pack = $packs_obj->get_pack($mgm_member->pack_id);
	}else{
		$subs_pack = $packs_obj->validate_pack($mgm_member->amount, $mgm_member->duration, $mgm_member->duration_type, $mgm_member->membership_type);
	}
	
	// types
	$duration_types = array('d'=>'DAY','m'=>'MONTH','y'=>'YEAR');	
					
	// default expire date				
	$expire_date = $mgm_member->expire_date;
	if($mgm_member->duration_type == 'l')
		$expire_date = date('Y-m-d');
						
	// if trial on 
	if ($subs_pack['trial_on'] && isset($duration_types[$subs_pack['trial_duration_type']])) {			
		// if cancel data is before trial end, set cancel on trial expire_date
		$trial_expire_date = strtotime("+{$subs_pack['trial_duration']} {$duration_types[$subs_pack['trial_duration_type']]}", $mgm_member->join_date);
		
		// if lower
		if(time() < $trial_expire_date){
			$expire_date = date('Y-m-d',$trial_expire_date);
		}
	}	
	
		
	// if today 
	if($expire_date == date('Y-m-d')){
		$new_status = MGM_STATUS_CANCELLED;
		$mgm_member->status_str = __('Subscription Cancelled','mgm');					
		$mgm_member->expire_date = date('Y-m-d');	
		// set status
		$mgm_member->status = $new_status;
	}else{
		$new_status = MGM_STATUS_AWAITING_CANCEL;			
		// reset on
		$mgm_member->status_reset_on = $expire_date;
		$mgm_member->status_reset_as = MGM_STATUS_CANCELLED;		
	}		
	
	// update user
	// update_user_option($user_id, 'mgm_member', $mgm_member, true);	
	//multiple memberhip level update:	
	if(isset($_POST['membership_type']) && $mgm_member->membership_type != $_POST['membership_type'])
		mgm_save_another_membership_fields($mgm_member, $user_id);
	else 
		// update_user_option($user_id, 'mgm_member', $mgm_member, true);	
		$mgm_member->save();	
		
	// send email notification to client
	$blogname = get_option('blogname');
	// email	
	$subject = sprintf(__('[%s] Subscription Cancelled','mgm'),$blogname);				
	$message = __('This is an automatic notification from %1$s to %2$s (%3$s). This is a notification to inform you that your subscription has been cancelled. For more information please contact %4$s','mgm');
	$message = sprintf($message, $blogname, $user->display_name, $user->user_email, $system->setting['admin_email']);

	// send email notification to user
	mgm_mail($user->user_email, $subject, $message);		

	// notify admin, only if gateway emails on
	if (!$dge) {
		$subject = "[$blogname] {$user->user_email} - {$new_status}";
		$message = "	User display name: {$user->display_name}\n\n<br />
				User email: {$user->user_email}\n\n<br />
				User ID: {$user->ID}\n\n<br />
				Membership Type: {$membership_type}\n\n<br />
				New status: {$new_status}\n\n<br />
				Status message: {$mgm_member->status_str}\n\n<br />					
				Payment Mode: Cancelled\n\n<br />
				POST Data was: \n\n<br /><br /><pre>" . print_r($_POST, true) . '</pre>';
		mgm_mail($system->setting['admin_email'], $subject, $message);
	}
	
	// after cancellation hook
	do_action('mgm_membership_subscription_cancelled', array('user_id' =>$user_id));	
	
	// message
	$message = sprintf(__("You have successfully Unsubscribed. Your account is marked for Cancellation on %s", "mgm"), ($expire_date == date('Y-m-d') ? 'Today' : date(MGM_DATE_FORMAT_LONG, strtotime($expire_date)) ));
	
	// redirect 	
	mgm_redirect('wp-admin/profile.php?page=mgm/profile&unsubscribed=true&unsubscribe_errors='.urlencode($message));
}

// get user
function mgm_get_user_by_token($token) {
	global $wpdb;
	// get all users except admin	
	$sql = "SELECT ID FROM " . $wpdb->users . " A JOIN " . $wpdb->usermeta . " B 
			ON (A.ID = B.user_id AND B.meta_key = 'mgm_member')";			
	//  mysql_real_escape_string()		
	$users = $wpdb->get_results($sql);
	// default
	$user_id =false;
	// loop
	foreach($users as $user){
		// member object
		$mgm_member = mgm_get_member($user->ID);
		// match
		if($mgm_member->rss_token == $token){
			$user_id = $user->ID;
			break;
		}
		// unset
		unset($mgm_member);
	}
	// set user
	if($user_id)
		return new WP_User($user_id);
		
	return false;	
}
// mgm_show_custom_fields
function mgm_show_custom_fields($user_ID=false, $submit_row=false, $return=false){
	// return
	return mgm_edit_custom_fields($user_ID, $submit_row, $return);
}

// edit custom fields
function mgm_edit_custom_fields($user_ID=false, $submit_row=false, $return=false) {
	// get user
	if (!$user_ID) $user_ID = mgm_get_user_id();
	
	// get form object
	if (is_object($user_ID)) $user_ID = $user_ID->ID;
	
	// system
	$system = mgm_get_class('system');
		
	// get custom_fields
	$profile_fields = mgm_get_config('default_profile_fields', array());
	// get active custom fields on profile page
	$cf_profile_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_profile'=>true)));
	
	$mgm_member = mgm_get_member($user_ID);
	//this is a fix for issue#: 589, see the notes for details:
	//This is to read saved coupons as array in order to fix the fatal error on some servers.	
	//This will change the object on each users profile view.
	//Also this will avoid using patch for batch update,	
	$arr_coupon = array('upgrade', 'extend');
	$oldcoupon_found = 0;
	foreach ($arr_coupon as $cpn_type) {
		if(isset($mgm_member->{$cpn_type}['coupon']) && is_object($mgm_member->{$cpn_type}['coupon'])) {
			$mgm_member->{$cpn_type}['coupon'] = (array) $mgm_member->{$cpn_type}['coupon'];
			$oldcoupon_found++ ;
		}
	}
	if($oldcoupon_found) {		
		$mgm_member->save();
	}
	
	// user
	$user = get_userdata($user_ID);	
	// init
	$html = '';	
	// capture
	$fields = array();	
	//default and readonly fields:
	$default_readonly = array();
	$arr_images = array();	
	//check logged in user is super admin:
	$is_admin = (is_super_admin()) ? true : false;
	
	// loop fields	
	foreach($cf_profile_page as $field){
		// issue#: 255			
		if (in_array($field['name'], array_keys($profile_fields)) ) {			
			//if custom field = defualt field, is read only
			if($field['attributes']['readonly'] && !$is_admin) {				
				$default_readonly[] = $profile_fields[ $field['name'] ]['id'];
				//email and url id is different than custom fields:
				if(in_array($field['name'],array('email','url')))
					$default_readonly[] = $field['name']; 			 	
			}
			continue;
		}
		// init value
		$value = '';
		//disable readonly for admin user(issue#: 515)
		$ro = (($field['attributes']['readonly'] == true && !$is_admin ) ? 'readonly="readonly"':false);		
		
		// value 
		if (isset($mgm_member->custom_fields->$field['name'])) {
			$value = $mgm_member->custom_fields->$field['name'];
		}				
		// date	
		if ($field['name'] == 'birthdate') {
			if ($value) {								
				$value = date(MGM_DATE_FORMAT_SHORT, strtotime($value));
			} else {
				$value = '';				
			}	
			$element = '<input type="text" name="mgm_profile_field['. $field['name'] .']" value="'. $value .'" '. $ro .' class="text '.(($ro)?'':'mgm_date').'" style="width:100px;" />';
		} else if ($field['name'] == 'country' ) {					
			$countries = mgm_field_values(TBL_MGM_COUNTRY, 'code', 'name');			
			if($ro) {					
				$countries = !empty($value) ? array($value => $countries[ $value ]) : array(" " => "&nbsp;");
			}			
			$options   = mgm_make_combo_options($countries, $value, MGM_KEY_VALUE);						
			$element   ='<select name="mgm_profile_field['. $field['name'] .']" > ' . $options . ' </select>';		
		} 
		else {
			if ($field['type'] == 'text') {
				$element = '<input type="text" name="mgm_profile_field['. $field['name'] .']" value="'. $value .'" '. $ro .' class="text" style="width:250px;" />';
			} else if ($field['type'] == 'password') {
				continue;
			} else if ($field['type'] == 'textarea') {
				$element = '<textarea name="mgm_profile_field['. $field['name'] .']" cols="40" rows="5" '. $ro .'>'. $value .'</textarea>';
			} else if ($field['type'] == 'checkbox') {
				$options= preg_split('/[;,]/', $field['options']); 									
				$element= mgm_make_checkbox_group('mgm_profile_field['. $field['name'] .']', $options, $value, MGM_VALUE_ONLY, '', 'div');
			} else if ($field['type'] == 'radio') {
				$options = preg_split('/[;,]/', $field['options']); 									
				$element = mgm_make_radio_group('mgm_profile_field['. $field['name'] .']', $options, $value, MGM_VALUE_ONLY);
			} else if ($field['type'] == 'select') {
				$element  = '<select name="mgm_profile_field['. $field['name'] .']" '. $ro .'>' ;	
				$options  = preg_split('/[;,]/', $field['options']); 
				if($ro) {	
					$options = (!empty($value)) ? array($value => $value) :array(" " => "&nbsp;"); 
				}				
				$element .= mgm_make_combo_options($options, $value, MGM_VALUE_ONLY);								
				$element .= '</select>';							
			} else if ($field['type'] == 'html') {
				$element  = '';
				$element .= '<div style="height: 200px; overflow: auto; background: #FBFBFB; border:1px solid #E5E5E5; padding:3px; margin: 5px 0 5px 0">'.html_entity_decode(mgm_stripslashes_deep($field['value'])).'</div>';
			} else if ($field['type'] == 'image') {
				$form_fields = & new mgm_form_fields();
				$element = $form_fields->get_field_element($field, 'mgm_profile_field',$value);
				if(!in_array($field['name'], $arr_images ))	
					$arr_images[] = $field['name'];
			}	
		}
			
		// set array
		if ($element) {
			$fields[] = array('name'=>$field['name'],'label'=>$field['label'], 'field'=>$element);
		}
	}
		
	// if fields
	if (count($fields)) {
		$html = '<table class=\'form-table\' style="width: 100%;">
					<col style="width:125px;"/>';

		foreach ($fields as $i=>$row) {
			$html .= '	<tr>
							<th style="text-align: left; vertical-align: top;">' . mgm_stripslashes_deep($row['label']) . '</th>
							<td style="text-align: left; vertical-align: top;">' . $row['field'] . '</td>
						</tr>';
		}
		
		// button
		if ($submit_row) {
			$html .= '	<tr>
							<td style="text-align: right;" colspan="2">
								<input name="update_mgm_custom_fields_submit" type="hidden" value="1" />
								<input name="submit" type="submit" value="' . __('Update your profile', 'mgm') . '" class="button"/>
							</td>
						</tr>';
		}

		$html .= '</table>';
		$html .= mgm_attach_scripts(true,array());
		$yearRange = mgm_get_calendar_year_range();
		//include scripts for image upload:
		if(!empty($arr_images)) {		
			$html .= mgm_upload_script_js('your-profile', $arr_images);
		}
		$html .= '<script language="javascript">jQuery(document).ready(function(){try{mgm_date_picker(".mgm_date",false,{yearRange:"'.$yearRange.'"});}catch(x){}});</script>';
	}
		
	if(!empty($default_readonly)) {
		$html .= '<script language="javascript">';
		$html .= 'jQuery(document).ready(function(){try{';
		$html .= 'jQuery.each('. json_encode($default_readonly) .', function(){jQuery("#"+this).attr("readonly", true)})';	
		$html .= '}catch(x){}})';
		$html .= '</script>';
	}
	
	// return	
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}

// save
function mgm_save_custom_fields() {
	// get user id
	$user_id = @(int)$_POST['user_id'];
	
	// get member & user
	$user = get_userdata($user_id);	
	// member 
	$mgm_member = mgm_get_member($user_id);
	
	// default return
	$return = false;
	
	// submit 
	if (isset($_POST['submit'])) {	
		
		// password update
		if ($pass = $_POST['pass1']) {
			$mgm_member->user_password= $pass;
		}	
		
		// get default fields
		$profile_fields = mgm_get_config('default_profile_fields', array());
		// get active profile fileds
		$cf_on_profilepage = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_profile'=>true)));
					
		// loop fields
		foreach($cf_on_profilepage as $field){
			// skip html
			if($field['type'] == 'html' || $field['type'] == 'label') continue;//issue#: 206
			// custom
			if(isset($_POST['mgm_profile_field'][$field['name']])){
				$value = $_POST['mgm_profile_field'][$field['name']];
			}else if(isset($_POST[$profile_fields[$field['name']]['name']])){
				$value = $_POST[$profile_fields[$field['name']]['name']];
			}else{
				$value = $_POST[$field['name']];
			}
			// set
			$mgm_member->custom_fields->$field['name'] = $value;									
		}
		// update
		// update_user_option($user_id, 'mgm_member', $mgm_member, true);		
		$mgm_member->save();
		// return as true	
		$return = true;
		//important: the below function is to reinsert the user multiple roles.
		//This is required as the default profile page deletes the unselected roles from user 
		mgm_reset_roles();
	}		
	// mgm_array_dump($user);die;
	// return
	return $return;
}
//reset user roles 
function mgm_reset_roles() {	
	if ( current_user_can('edit_users') && !IS_PROFILE_PAGE ) {		
		$user_id = 0;
		if(isset($_POST['user_id']) && is_numeric($_POST['user_id']))
			$user_id = $_POST['user_id'];
		elseif (isset($_GET['user_id']) && is_numeric($_GET['user_id']))
			$user_id = $_GET['user_id'];
			
		if($user_id > 1 && isset($_POST['role'])) {	
			$user = new WP_User($user_id);							
			if(!empty($user->roles)){
				$mgm_member = mgm_get_member($user_id);
				$pack_ids = mgm_get_members_packids($mgm_member);
				
				if(!empty($pack_ids)) {
					$role_updated =  0;										
					$obj_role = new mgm_roles();				
					foreach ($pack_ids as $pid) {
						$pack = mgm_get_class('subscription_packs')->get_pack($pid);									
						if(isset($pack['role']) && $pack['role'] != $_POST['role'] ) {							
							$obj_role->add_user_role($user_id, $pack['role']);							
						}
					}
					
					//add selected role:									
					$obj_role->add_user_role($user_id, $_POST['role'], true, false);					
					
				}							
			}			
		}
	}
}

// for shortcode
function mgm_edit_custom_field_standalone() {
	$html = '';

	$html .= '	<div class="mgm_custom_fields_standalone">';

	if (isset($_POST['update_mgm_custom_fields_submit'])) {
		if (mgm_save_custom_fields()) {
			$html .= '<div class="mgm_feedback updated fade" id="message">' . __('Your profile has been updated successfully', 'mgm') . '</div>';
		}
	}

	$html .= '		<form method="POST">';

	$html .= mgm_edit_custom_fields(false, true, true);

	$html .= "		</form>
				</div>";

	return $html;
}

function mgm_get_custom_field_array($user_ID) {
	$fld_obj = get_option('mgm_custom_fields');
	$entries = $fld_obj->entries;
	$order = $fld_obj->order;

	$skip_array = array(
	__('Terms and Conditions','mgm')
	, __('Subscription Introduction','mgm')
	, __('Subscription Options','mgm')
	);

	$userfields = get_usermeta($user_ID, 'mgm_custom_fields');

	if (strpos($order, ';') !== false) {
		$orders = explode(';', $order);
	} else {
		$orders = array($order);
	}

	foreach ($orders as $order) {
		foreach ($entries as $entry) {
			if ($order == $entry['id']) {
				if (in_array($entry['name'], $skip_array)) {
					continue;
				} else {
					$return[strtolower(str_replace(' ','_',$entry['name']))] = $userfields[$entry['id']];
				}
			}
		}
	}

	if (isset($return['birthdate']) && $return['birthdate'] != '') {
		$bday_array = explode('-', $return['birthdate']);
		$return['birthdate_unixtime'] = strtotime($bday_array[2] . '-' . $bday_array[0] . '-' . $bday_array[1]);
	}

	return is_array($return)?$return:array();
}
// no access redirect
function mgm_no_access_redirect($system) {	
	// user
	$user = wp_get_current_user();

	// int url
	$url = false;

	// user logged in
	if ($user->ID == 0) {
		$url = $system->setting['no_access_redirect_loggedout_users'];
	} else {
		$url = $system->setting['no_access_redirect_loggedin_users'];
	}
	
	// if url
	if ($url) {
		if (!headers_sent()) {
			@header('location: ' . $url);
		} else {
			$return = '	<script>document.location="' . $url . '";</script>';
		}
	}
	
	// return
	return $return;
}

// checks user has purchased post
function mgm_user_has_purchased_post($post_id, $user_id) {
	get_currentuserinfo();
	global $wpdb, $current_user;

	$return = false;

	if (isset($current_user->caps['administrator']) && $current_user->caps['administrator'] >= 1) {
		$return = true;
	} else {
		// get duration
		$duration = mgm_get_post($post_id)->get_access_duration();
		// sql
		$sql = 'SELECT `purchase_dt`,`is_expire`,`is_gift` FROM `' . TBL_MGM_POSTS_PURCHASED . '` WHERE
				post_id = ' . $post_id . ' AND user_id = "' . $user_id.'"';
		// get 
		$purchased = $wpdb->get_row($sql);
		// date is set		
		if ($purchase_dt = $purchased->purchase_dt) {
			// duration is indefinite or gift with no expire
			if ((int)$duration == 0 || ($purchased->is_gift == 'Y' && $purchased->is_expire == 'N')) {
				$return = true;
			} else if (strtotime($purchase_dt) + ($duration*86400) > time()) {
				$return = true;
			}
		}
	}
	// return	
	return $return;
}
// get postpack post in csv string
function mgm_get_postpack_posts_csv($pack_id) {
    $array = array();
    if ($posts = mgm_get_postpack_posts($pack_id)) {
        foreach ($posts as $i=>$post) {
            $array[] = $post->post_id;
        }
    }
    // implode
    return implode(',', $array);
}
// get packpack data
function mgm_get_postpack($pack_id = false) {
    global $wpdb;
    
    $postpack = new stdClass();
    $postpack->id = $postpack->name = $postpack->description = $postpack->product = $postpack->create_dt = $postpack->cost = false;
    // set
    if ($pack_id) {
        $sql = 'SELECT id, name, cost, description, product, create_dt FROM `' . TBL_MGM_POST_PACK . '` WHERE id = ' . $pack_id;
        $postpack = $wpdb->get_row($sql);
    }
    // return
    return $postpack;
}

// get pp packs
/*
function mgm_get_ppp_pack_posts($pack_id = false) {
    global $wpdb;
    
    $return = new stdClass();
    $return->id = $return->pack_id = $return->post_id = $return->unixtime = false;
    
    if ($pack_id) {
        $sql = 'SELECT id, pack_id, post_id, unixtime FROM `' . TBL_MGM_POST_PACK_POST_ASSOC . '` WHERE pack_id =  ' . $pack_id;
        $return = $wpdb->get_results($sql);
    }
    
    return $return;
}
*/

// get packpacks
function mgm_get_postpack_posts($pack_id = false, $count = false) {
    global $wpdb;
    // when set
    if ($pack_id) {
        $sql    = 'SELECT id, pack_id, post_id, create_dt  FROM `' . TBL_MGM_POST_PACK_POST_ASSOC . '`  WHERE pack_id =  ' . $pack_id;
        $return = $wpdb->get_results($sql);
		// count / objects
		return ($count) ? count($return) : $return;
    }else{
	// error	
		$return = new stdClass();
    	$return->id = $return->pack_id = $return->post_id = $return->create_dt = false;
		// count / object
		return ($count) ? 0 : $return;
	}
}

// validate coupon
function mgm_validate_coupon($code, $cost){
	// get coupon	
	$code = trim($code);
	if(!empty($code)) {
		$coupon = mgm_get_coupon_data($code);	
		// if found
		if($coupon){
			// init
			//$new_coupon = new stdClass;
			$new_coupon = array();
			// copy
			$new_coupon = $coupon;
			// what type of coupon is it %, scalar, sub_id
			$type = mgm_get_coupon_type($coupon['value']);	
			// double check we still have content
			if($type)  {
				// check on type
				switch($type){
					case 'percent':
						// string % with number, issue #135, accept period for fraction value
						$value = preg_replace('/[^0-9.]/', '', $coupon['value']);
						$percent = $value / 100;
						$cost = $cost * (1 - $percent); 
						if($cost < 0){
							$cost = 0;
						}
						// set
						$new_coupon['cost'] = $cost;	
					break;
					case 'sub_pack':
						// sub_pack#Price_Duration-Unit_Duration-Type_Membership-Type
						// validate and split
						$value = preg_replace('/[^A-Za-z0-9_-]/', '', str_replace('sub_pack#','',$coupon['value']));
						list($new_cost, $new_duration, $new_duration_type, $new_membership_type, $billing_occurance) = explode('_', $value, 5);	// 4 only									
						// set
						$new_coupon['cost']            = $new_cost;	
						$new_coupon['duration']        = $new_duration;
						$new_coupon['duration_type']   = strtolower($new_duration_type);
						$new_coupon['membership_type'] = str_replace('-','_',$new_membership_type);
						//billing occurances:
						if(is_numeric($billing_occurance))
							$new_coupon['num_cycles']  = $billing_occurance;	
					break;				
					case 'sub_pack_trial':
						// subs_pack_trial#Trial-Duration-Unit_Trial-Duration-Type_Trial-Price_Trial-Occurrences
						// validate and split
						$value = preg_replace('/[^A-Za-z0-9_-]/', '', str_replace('sub_pack_trial#','',$coupon['value']));
						list($new_duration, $new_duration_type, $new_cost, $new_num_cycles) = explode('_', $value, 4);	// 4 only									
						// set
						$new_coupon['trial_on']            = 1;
						$new_coupon['trial_cost']          = $new_cost;	
						$new_coupon['trial_duration']      = $new_duration * $new_num_cycles;
						$new_coupon['trial_duration_type'] = strtolower($new_duration_type);
						$new_coupon['trial_num_cycles']    = $new_num_cycles;
					break;
					case 'scalar':
					default:
						// issue #135, accept period for fraction value
						$value = preg_replace('/[^0-9.]/', '', $coupon['value']);
						$cost = $cost - $value; 
						if($cost < 0){
							$cost = 0;
						}
						// set
						$new_coupon['cost'] = $cost;	
					break;
				}
			}			
			// format cost, issue 297
			// if($new_coupon['cost']){
			// issue#: 306
			if(isset($new_coupon['cost']) && is_numeric($new_coupon['cost'])){
				$new_coupon['cost'] = number_format($new_coupon['cost'], 2, '.','');
			}	
			// return object
			return $new_coupon;
		}
	}
	
	// error
	return false;
}

// get coupon data 
function mgm_get_coupon_data($coupon_name){
	global $wpdb;
	
	// sql
	$sql = "SELECT id,name,value,use_limit,used_count,expire_dt,description FROM `" . TBL_MGM_COUPON . "` WHERE name = '{$coupon_name}'";	
	// get
	$coupon = $wpdb->get_row($sql);	
	//  check
	if($coupon){		
		// limit validate
		if(!is_null($coupon->use_limit)){
			if((int)$coupon->used_count >= (int)$coupon->use_limit){
				return false;
			}
		}
		// expire validate
		if(!is_null($coupon->expire_dt)){
			if(time() > strtotime($coupon->expire_dt)){
				return false;
			}
		}
		
		// trim date
		unset($coupon->expire_dt,$coupon->use_limit,$coupon->used_count);
		$coupon = (array) $coupon;
		// return
		return $coupon;
	}
	// 
	
	return false;
}
// get coupon type
function mgm_get_coupon_type($coupon_value) {
	// type of coupon
	if(strpos($coupon_value, '%') !== false){
		// Percentage
		$return = 'percent';
	}else if(preg_match('/^sub_pack#/i', $coupon_value)){
		// subscription pack
		$return = 'sub_pack';
	}else if(preg_match('/^sub_pack_trial#/i', $coupon_value)){
		// subscription pack id
		$return = 'sub_pack_trial';
	}else {
		//everything else
		$return = 'other';
	}
	//return 
	return $return;
}

// spider/bot check
function mgm_is_a_bot() {
	$spiders = array('googlebot','google','msnbot','ia_archiver','lycos','jeeves','scooter','fast-webcrawler','slurp@inktomi',
	                 'turnitinbot','technorati','yahoo','findexa','findlinks','gaisbo','zyborg','surveybot','bloglines','blogsearch',
					 'pubsub','syndic8','userland','gigabot','become.com');

	$useragent = $_SERVER['HTTP_USER_AGENT'];

	if (empty($useragent)) {
		return false;
	}

	// Check For Bot
	foreach ($spiders as $spider) {
		if (stristr($useragent, $spider) !== false) {
			return true;
		} else {
			return false;
		}
	}
}

// check content protection
function mgm_protect_content($content_protection=NULL) {
	// if not passed
	if(!$content_protection) $content_protection = mgm_get_class('system')->setting['content_protection'];
	// is protection on 
	return ( $content_protection != 'none' ) ? true : false;	
}

/*// check post hide?
function mgm_content_protection() {
	return (mgm_get_class('system')->setting['hide_posts'] == 'Y') ? true : false;
}*/

// build http
function mgm_http_build_query($data, $encode=true){	
	// query
	$_query = '';
	foreach($data as $key => $value) {
		if (is_array($value)) {
			foreach($value as $item) {
				if (strlen($_query) > 0) $_query .= "&";
				$_query .= ($encode==true) ?  ("$key=".urlencode($item)) : ("$key=$value");
			}
		} else {
			if (strlen($_query) > 0) $_query .= "&";						
			$_query .= ($encode==true) ? ("$key=".urlencode($value)) : ("$key=$value");
				
		}
	}
	
	return $_query;
}
// get currencies
function mgm_get_currencies(){
	$currencies = array(
						'AUD' => sprintf(__('%s - Australian Dollar','mgm'), 'AUD'),
						'BRL' => sprintf(__('%s - Brazilian Real','mgm'), 'BRL'),
						'CAD' => sprintf(__('%s - Canadian Dollar','mgm'), 'CAD'),
						'CHF' => sprintf(__('%s - Swiss Franc','mgm'), 'CHF'),
						'CZK' => sprintf(__('%s - Czech Koruna','mgm'), 'CZK'),
						'DKK' => sprintf(__('%s - Danish Krone','mgm'), 'DKK'),
						'EUR' => sprintf(__('%s - Euro','mgm'), 'EUR'),
						'GBP' => sprintf(__('%s - Pound Sterling','mgm'), 'GBP'),
						'HKD' => sprintf(__('%s - Hong Kong Dollar','mgm'), 'HKD'),
						'HUF' => sprintf(__('%s - Hungarian Forint','mgm'), 'HUF'),
						'ILS' => sprintf(__('%s - Israeli New Sheqel','mgm'), 'ILS'),
						'INR' => sprintf(__('%s - Indian Rupee','mgm'), 'INR'),
						'JPY' => sprintf(__('%s - Japanese Yen','mgm'), 'JPY'),
						'MXR' => sprintf(__('%s - Mexican Peso','mgm'), 'MXR'),
						'MYR' => sprintf(__('%s - Malaysian Ringgit','mgm'), 'MYR'),
						'NOK' => sprintf(__('%s - Norwegian Krone','mgm'), 'NOK'),
						'NZD' => sprintf(__('%s - New Zealand Dollar','mgm'), 'NZD'),
						'PHP' => sprintf(__('%s - Philippine Peso','mgm'), 'PHP'),
						'PLN' => sprintf(__('%s - Polish Zloty','mgm'), 'PLN'),
						'SEK' => sprintf(__('%s - Swedish Krona','mgm'), 'SEK'),
						'SGD' => sprintf(__('%s - Singapore Dollar','mgm'), 'SGD'),
						'THB' => sprintf(__('%s - Thai Baht','mgm'), 'THB'),
						'TRY' => sprintf(__('%s - Turkish Lira','mgm'), 'TRY'),
						'TWD' => sprintf(__('%s - Taiwan New Dollar','mgm'), 'TWD'),
						'USD' => sprintf(__('%s - U.S. Dollar','mgm'), 'USD')
						);
	
	return $currencies;
}
// get locales
function mgm_get_locales(){
	$locales = array(
					'AU' => __('Australia','mgm'),
					'AT' => __('Austria','mgm'),
					'BE' => __('Belgium','mgm'),
					'CA' => __('Canada','mgm'),
					'CN' => __('China','mgm'),
					'FR' => __('France','mgm'),
					'DE' => __('Germany','mgm'),
					'IT' => __('Italy','mgm'),
					'NL' => __('Netherlands','mgm'),
					'PL' => __('Poland','mgm'),
					'ES' => __('Spain','mgm'),
					'CH' => __('Switzerland','mgm'),
					'GB' => __('United Kingdom','mgm'),
					'US' => __('United States','mgm'),
					'CZ' => __('Czech Republic','mgm'),
					'DK' => __('Denmark','mgm'),
					'FI' => __('Finland','mgm'),
					'GF' => __('French Guiana','mgm'),
					'GR' => __('Greece','mgm'),
					'GP' => __('Guadeloupe','mgm'),
					'HU' => __('Hungary','mgm'),
					'IN' => __('India','mgm'),
					'ID' => __('Indonesia','mgm'),
					'IE' => __('Ireland','mgm'),
					'IL' => __('Israel','mgm'),
					'LU' => __('Luxembourg','mgm'),
					'MY' => __('Malaysia','mgm'),
					'MQ' => __('Martinique','mgm'),
					'NZ' => __('New Zealand','mgm'),
					'NO' => __('Norway','mgm'),
					'PT' => __('Portugal','mgm'),
					'RE' => __('Reunion','mgm'),
					'SK' => __('Slovakia','mgm'),
					'KR' => __('South Korea','mgm'),
					'SE' => __('Sweden','mgm'),
					'TW' => __('Taiwan','mgm'),
					'TH' => __('Thailand','mgm'),
					'TR' => __('Turkey','mgm'),
					'CL' => __('Chile','mgm'),
					'EC' => __('Ecuador','mgm'),
					'JM' => __('Jamaica','mgm'),
					'UY' => __('Uruguay','mgm'),
					'BM' => __('Bermuda','mgm'),
					'BG' => __('Bulgaria','mgm'),
					'KY' => __('Cayman Islands','mgm'),
					'CR' => __('Costa Rica','mgm'),
					'CY' => __('Cyprus','mgm'),
					'DO' => __('Dominican Republic','mgm'),
					'SV' => __('El Salvador','mgm'),
					'EE' => __('Estonia','mgm'),
					'GI' => __('Gibraltar','mgm'),
					'GT' => __('Guatemala','mgm'),
					'IS' => __('Iceland','mgm'),
					'KE' => __('Kenya','mgm'),
					'KW' => __('Kuwait','mgm'),
					'LV' => __('Latvia','mgm'),
					'LI' => __('Liechtenstein','mgm'),
					'LT' => __('Lithuania','mgm'),
					'MT' => __('Malta','mgm'),
					'PA' => __('Panama','mgm'),
					'PE' => __('Peru','mgm'),
					'QA' => __('Qatar','mgm'),
					'RO' => __('Romania','mgm'),
					'SM' => __('San Marino','mgm'),
					'SI' => __('Slovenia','mgm'),
					'ZA' => __('South Africa','mgm'),
					'AE' => __('United Arab Emirates','mgm'),
					'VE' => __('Venezuela','mgm'),
					'VN' => __('Vietnam','mgm')
					);
	// sort
	//issue#: 538
	//sort($locales);	
	asort($locales);		
	// return	
	return $locales;				
}
// get languages
function mgm_get_languages(){
	$languages = array(
					 'EN' => __('English','mgm'),
					 'DE' => __('German','mgm'),
					 'FR' => __('French','mgm'),
					 'ES' => __('Spanish','mgm'),
					 'IT' => __('Italian','mgm'),
					 'PL' => __('Polish','mgm'),
					 'GR' => __('Greek','mgm'),
					 'RO' => __('Romanian','mgm'),
					 'RU' => __('Russian','mgm'),
					 'TR' => __('Turkish','mgm'),					 
					 'CN' => __('Chinese','mgm'),
					 'CZ' => __('Czech','mgm')					 
					);
	return $languages;				
}
// currency
function mgm_convert_to_currency($num) {
    if (strpos($num, '.') == false) {
        $num = $num . '.00';
    } else {
        $num = sprintf("%01.2f", (float)$num);
    }
    return $num;
}
// decimal
function mgm_convert_to_decimal($num) {
	// fraction
    if (strpos($num, '.') !== false) {
        $num = (float)$num * 100;
    }// return
    return $num;
}
// cent
function mgm_convert_to_cents($num) {
	// fraction    
    return (float)$num * 100;    
}
// get words from content
function mgm_words_from_content($content, $word_limit, $start=0){	
	// split by space
	$all_words = preg_split("/\s+/", $content);
	// init
	$words = array();
	// capture
	foreach($all_words as $word){
		// remove space
		$word = trim($word);
		// skip no value
		if(empty($word)) continue;
		// skip html 
		if(preg_match('/<(.*)>(.*)<\/(.*)>/', $word)) continue;		
		// add
		$words[] = $word; 		
	}
	$partial_content = array_slice($words, $start, $word_limit );
	$partial_content = implode(' ', $partial_content);	
	
	//check and update if any closing tag needed.	
	//$partial_content = mgm_close_open_tags($partial_content);
	
	// return 
	return $partial_content ; 
}
//close if any broken htmls tags exist: 
//check this function
function mgm_close_open_tags($html, $ignore=array('img', 'hr', 'br')) {    
	if (preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $opentags)) {    	
	    $opentags[1] = array_diff($opentags[1], $ignore);
	    $opentags[1] = array_values($opentags[1]);
	    preg_match_all("#</([a-z]+)>#iU", $html, $closetags);
	    $opened = count($opentags[1]);
	    if (count($closetags[1]) == $opened) return $html;
	    $opentags[1] = array_reverse($opentags[1]);
	    for ($i=0;$i<$opened;$i++) {
	        if (!in_array($opentags[1][$i], $closetags)) $html .= '</'.$opentags[1][$i].'>';
	        else unset($closetags[array_search($opentags[1][$i], $closetags)]);
	    }
	}
	return $html;
}
// deep stripslashes
function mgm_stripslashes_deep_once($data){	
	// clean till found '\'
	do{
		$data = stripslashes($data);
	}while(strpos($data, '\\') !==false);	
	// return
	return $data;
}

// deep stripslashes recursive
function mgm_stripslashes_deep($value) {
	if ( is_array($value) ) {
		$value = array_map('mgm_stripslashes_deep', $value);
	} elseif ( is_object($value) ) {
		$vars = get_object_vars( $value );
		foreach ($vars as $key=>$data) {
			$value->{$key} = mgm_stripslashes_deep( $data );
		}
	} else {		
		// clean till found '\'
		do{
			$value = stripslashes($value);
		}while(strpos($value, '\\') !== false);
	}
	// return	
	return $value;
}
// deep array merge recursive
/*function mgm_array_merge_deep($value1,$value2) {
	// return merged, wrapper for attending any bug later on
	return  array_merge_recursive($value1,$value2);	
}*/
// taken from php.net
function mgm_array_merge_recursive_unique($array0, $array1)	{
	$arrays = func_get_args();
	$remains = $arrays;
	// We walk through each arrays and put value in the results (without
	// considering previous value).
	$result = array();
	// loop available array
	foreach($arrays as $array) {
		// The first remaining array is $array. We are processing it. So
		// we remove it from remaing arrays.
		array_shift($remains);
		// We don't care non array param, like array_merge since PHP 5.0.
		if(is_array($array)) {
			// Loop values
			foreach($array as $key => $value) {
				if(is_array($value)) {
					// we gather all remaining arrays that have such key available
					$args = array();
					foreach($remains as $remain) {
						if(array_key_exists($key, $remain)) {
							array_push($args, $remain[$key]);
						}
					}
					if(count($args) > 2) {
						// put the recursion
						$result[$key] = call_user_func_array(__FUNCTION__, $args);
					} else {
						foreach($value as $vkey => $vval) {
							$result[$key][$vkey] = $vval;
						}
					}
				} else {
					// simply put the value
					$result[$key] = $value;
				}
			}
		}
	}
	return $result;
}
// ui version
function mgm_get_jqueryui_version(){
	// compare version if greater than 2.9
	if (version_compare(get_bloginfo('version'), '2.9', '>=')){
		// ui 1.7.3 for jQuery 1.4+ options : 1.7.3 , 1.8.2
		$jqueryui_version = get_option('mgm_jqueryui_version');
		if(!$jqueryui_version){// not defined, use as coded
			$jqueryui_version = '1.7.3';		
			update_option('mgm_jqueryui_version', $jqueryui_version); // and update		 
		}
	}else{
		// ui 1.7.2 for jQuery 1.3.2+
		$jqueryui_version = '1.7.2';			 
	}
	// return
	return $jqueryui_version;
}
// trim 
function mgm_trim($string, $trim_chars = " \t\n\r\0\x0B")
{
 	return str_replace(str_split($trim_chars), '', $string);
}
// get include
function mgm_get_include($template, $data=false){
	// data
	if(is_array($data))
		extract($data);
	
	ob_start();		
	@include($template);
	return ob_get_clean();		
}
// private text
function mgm_private_text_tags($text){		
	global $post;	
	//get login link from settings:
	$login_link = mgm_get_custom_url('login', false, array('redirect_to' => get_permalink($post->ID)));
	// login
	//$login_url = (is_object($post)) ? sprintf(' <a href="%s"><b>Login</b></a>',site_url('wp-login.php?redirect_to='.get_permalink($post->ID))) : '';	
	$login_url = (is_object($post)) ? sprintf(' <a href="%s"><b>Login</b></a>', $login_link) : '';	
	// replace
	$text = str_replace('[login]',$login_url, $text);	
	// return 
	return $text;
}
// get message
function mgm_get_template($name, $data=array(), $type= 'messages'){
	global $wpdb;
	// check from db first
	$content = $wpdb->get_var("SELECT `content` FROM `".TBL_MGM_TEMPLATE."` WHERE `name`='{$name}' AND `type`='{$type}'");
	// not in db
	if(!isset($content) || (isset($content) && empty($content))){	
		// check old content
		$content = mgm_get_old_template_content($name);			
		// stil empty, take from file
		if(empty($content)){
			// template file
			$template_file = MGM_CORE_DIR . MGM_DS . 'html' . MGM_DS . $type . MGM_DS . $name . '.html';
			// get content
			if(file_exists($template_file)){
				$content = file_get_contents($template_file);
			}
		}
		// insert
		if($content){
			// strp first
			$content = mgm_stripslashes_deep($content);
			// and update database
			$wpdb->insert(TBL_MGM_TEMPLATE, array('name'=>$name,'type'=>$type,'content'=>addslashes($content),'create_dt'=>date('Y-m-d h:i:s')));	
		}	
	}		
	// check template parser
	if($content){						
		//patch to update old users message:
		$content = mgm_replace_oldlinks_with_tag($content, $name);
		// check
		if(is_array($data)){
			foreach($data as $key=>$value){
				$content = str_replace('['.$key.']', $value, $content);
			}
		}	
		// return
		return mgm_stripslashes_deep($content);
	}		
	// return
	return '';
}
//patch for old messages
function mgm_replace_oldlinks_with_tag($content, $name) {
	switch ($name) {
		case 'login_errmsg_null':
		case 'login_errmsg_expired':
		case 'login_errmsg_trial_expired':	
			$oldlink = add_query_arg(array('action' => 'upgrade', 'username'=>'[[USERNAME]]'), mgm_home_url('subscribe'));
			$content = str_replace($oldlink, '[subscription_url]', $content);
			break;
		case 'payment_success_message':		//double link issue	
			if(!preg_match("/\[login_url\]/", $content)) {
				$pos_profile_string = strrpos($content, __('Your Profile','mgm').'</a>');
				$pos_href = strrpos(substr($content, 0, $pos_profile_string), 'href=' );
				$needle = mgm_get_custom_url('login');	
				$prev_link = substr($content, $pos_href, $pos_profile_string);									
				if(strstr($prev_link, $needle) || strstr($prev_link, '/wp-login.php')) {					
					$link = "href=\"{$needle}\">";
					$content = substr($content, 0, $pos_href) . $link . substr($content, $pos_profile_string, strlen($content) );
				}				
			}
		case 'payment_failed_message':	//double link issue			
			if(!preg_match("/\[register_url\]/", $content)) {
				$pos_register_string = strrpos($content, 'Register</a>');
				$pos_href = strrpos(substr($content, 0, $pos_register_string), 'href=');
				$needle = mgm_get_custom_url('register');
				$prev_link = substr($content, $pos_href, $pos_register_string);
				if(strstr($prev_link, $needle) || strstr($prev_link, '/subscribe/?method=register') || strstr($prev_link, '/wp-login.php?action=register') ) {					
					$link = "href=\"{$needle}\">";
					$content = substr($content, 0, $pos_href) . $link . substr($content, $pos_register_string, strlen($content) );
				}
			}
			break;				
	}
	// return
	return $content;
}
// update message
function mgm_update_template($name, $content, $type= 'messages'){
	global $wpdb;
	// strp first
	$content = mgm_stripslashes_deep($content);
	// save to db
	$success = $wpdb->update(TBL_MGM_TEMPLATE, array('content'=>addslashes($content)), array('name'=>$name,'type'=>$type));	
	// return 
	return ($success === FALSE) ? false : true; 
}
// get old content
function mgm_get_old_template_content($template){
	// system
	$system = mgm_get_class('system');
	// init
	$content = '';
	// payment were different
	if(preg_match('/^payment/',$template)){		
		// new name
		$template_new = str_replace('payment_','',$template);
		// fetch
		if(isset($system->setting['payment'][$template_new])){
			// content
			$content = $system->setting['payment'][$template_new];				
		}
	}else{
		// fetch
		if(isset($system->setting[$template])){
			// content
			$content = $system->setting[$template];				
		}
	}	
	// content
	return $content;
}
// print template wrapper
function mgm_print_template_content($template,$type='messages'){
	return mgm_get_template($template, NULL, $type);
}
// mgm_get_message_template
function mgm_get_message_template($message){
	$data = array();
	// set urls
	$data['home_url']     = trailingslashit(get_option('siteurl'));
	$data['site_url']     = trailingslashit(site_url());	
	$data['register_url'] = trailingslashit(mgm_get_custom_url('register'));					
	// login or profile
	$data['login_url']    = trailingslashit(mgm_get_custom_url((is_user_logged_in() ? 'profile' : 'login')));
	// check
	if(is_array($data)){
		foreach($data as $key=>$value){
			$message = str_replace('['.$key.']', $value, $message);
		}
	}	
	// return
	return $message;
}
// concat
function mgm_str_concat(){
	$args_size= func_num_args();
	if($args_size>0){
		$args = func_get_args();
		return implode(' ', array_map('trim',$args));
	}
	return '';
}
// register links
function mgm_sidebar_register_links($username=false, $return=false, $template='sidebar') {
	global $wpdb, $user, $duration_str, $mgm_sidebar_widget;
	// username
	if (!$username) {
		$username = $_GET['username'];
	}
	// member data
	$mgm_member = mgm_get_member( $user->ID);
	$system = mgm_get_class('system');
	$membership_type = strtolower($mgm_member->membership_type);
	$packs_obj = mgm_get_class('subscription_packs');
	$packs = $packs_obj->packs;
	
	if (!$packs) {
		$packs = array();
	}
	
	$html = '';
	$border = '';

	$active_modules = $system->get_active_modules('payment');	
	$mod_count = count($active_modules);
	$modules_dir = MGM_MODULES_DIR ;
	$base = add_query_arg(array('ud'=>1, 'username'=>$username), mgm_home_url('subscribe'));

	foreach ($packs as $pack) {
		$dur_type = $duration_str[$pack['duration_type']];
		$dur_str = ($pack['duration'] == 1 ? rtrim($dur_type, 's'):$dur_type);
		$ac_type = strtolower($pack['membership_type']);

		if (in_array($ac_type, array($membership_type, 'trial', 'free'))) {
			continue;
		}

		$cost = $pack['cost'];

		$pack_str = $packs_obj->get_pack_desc($pack);		
		$html .= '<div style="border-bottom: 1px solid #EFEFEF; overflow: auto; margin-bottom: 5px;text-align:left;">';

		if ($template != 'sidebar') {
			$html .= '<div style="float: left; margin-bottom: 5px; font-size: 14px; font-style:italic;">' . $pack_str . '</div>';

			if ($pack['description'] != '') {
				$html .= '<div id="mgm_pack_string" style="float: left; width: 100%; margin-bottom: 5px;" id="mgm_pack_overview">' . $pack['description'] . '</div>';
			}
		} else {
			$html .= '<div style="float: left; ' . ($mod_count > 1 ? 'width: 100%;':'') . ' margin-bottom: 5px; font-size: 14px;" id="mgm_pack_string_sidebar">' . $pack_str . '</div>';
		}

		if ($mod_count) {
			if ($active_modules) {
				$tran_id = 0;
				foreach ($active_modules as $module) {
					if ($module == 'mgm_trial') {
						continue;
					}
					$mod_obj = mgm_get_module($module,'payment');	
					// create transaction
					if($tran_id == 0){
						$tran_id = $mod_obj->_create_transaction($pack, array('user_id' => $user->ID));
					}
					// button
					//issue#: 398
					//$button = mgm_get_module($module,'payment')->get_button_subscribe(array('pack'=>$pack));					
					$button = $mod_obj->get_button_subscribe(array('tran_id' => $tran_id,'pack'=>$pack,'widget' => true));					
					// button
					if ($button) {
						$html .= '<div style="float: left; overflow:auto; margin: 0px 5px 5px 0px; text-align: left;">';
						$html .= '<table style="margin: 0px; border-collapse: collapse; border: none !important;" cellpadding="2" cellspacing="0">';
						$html .= $button;
						$html .= '</table>';
						$html .= '</div>';
					}
				}
			}
		} else {
			$html .= __('There are no gateways available at this time.','mgm');
		}

		$html .= '<div style="padding: 0; margin: 0; clear: both;"></div>';
		$html .= '</div>';

	}

	// html
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}

// wrapper for get_userdata, copies data form custom fields
function mgm_get_userdata($user_id){
	// get user
	$user = get_userdata($user_id);
	// member
	$mgm_member = mgm_get_member($user_id);
	// check profile fields
	$profile_fields = mgm_get_config('default_profile_fields', array());
	// loop
	foreach($profile_fields as $name=>$field){
		// wordpress is not set
		if(empty($user->$field['name'])){
			// check mgm if set
			if(isset($mgm_member->custom_fields->$name) && !empty($mgm_member->custom_fields->$name)){
				// default
				$user->$field['name'] = $mgm_member->custom_fields->$name;
				// compat
				if(!preg_match('/user_/',$field['name'])){
					// set
					$compat_field = 'user_'.str_replace('_','',$field['name']);
					// set
					$user->$compat_field = $mgm_member->custom_fields->$name;
				}
			}
		}
	}
	// return	
	return $user;
}

// deprecated : not used
function mgm_get_userdatabylogin($user_login='') {
	// login	
	if (isset($_GET[$user_login])) {
		$return = get_userdatabylogin($_GET[$user_login]);
	} else if($login){
		$return = get_userdatabylogin($user_login);
	} else {
		// current
		$current_user = wp_get_current_user();
		$return = get_userdata($current_user->ID);
	}
	// return
	return $return;
}

// update defaults, copy form mgm to wordpress
function mgm_update_default_userdata($user_id){
	// db
	global $wpdb;
	// user	
	$user = get_userdata($user_id);
	// set aside member object
	$mgm_member = mgm_get_member($user_id);
	// default
	$profile_fields = mgm_get_config('default_profile_fields',array());
	// loop
	foreach($profile_fields as $name=>$field){
		// do not update pasword/login here !!!
		if(in_array($name, array('username','email','password','password_conf'))) continue;
		// check if empty
		if(empty($user->$field['name'])){
			// check custom
			if(isset($mgm_member->custom_fields->$name) && !empty($mgm_member->custom_fields->$name)){
				// value
				$value = $mgm_member->custom_fields->$name;
				// check diff
				if($name == 'url' || $name == 'display_name'){
				// users table update
					$wpdb->query("UPDATE `{$wpdb->users}` SET `{$field['name']}` = '{$value}' WHERE ID = '{$user_id}'");										
				}else{
				// meta update	
					update_user_option($user_id,$field['name'],$value,true);	
				}
			}
		} 	
	}		
	// return 
	return $user_id ;	
}

// send 
function mgm_autoresponder_send($user_id){
	// get user
	$mgm_member = mgm_get_member($user_id);	
	// if subscribed	
	if($mgm_member->subscribed){
		// send with active					
		$return = mgm_get_module($mgm_member->autoresponder,'autoresponder')->send($user_id);				
	}
	// return
	return $user_id;
}

// mgm_get_post_purchase_buttons
function mgm_get_post_purchase_buttons(){
	// pack
	$pack = NULL;
	// post purchase
	if(isset($_POST['post_id'])){
		// post id
		$post_id = $_POST['post_id'];
		// gete mgm data
		$mgm_post = mgm_get_post($post_id);
		$cost     = mgm_convert_to_currency($mgm_post->purchase_cost);
		$product  = $mgm_post->product;
		// post data
		$post    = get_post($post_id);
		$title   = $post->post_title;
		// item name
		$item_name = 'Purchase Post - '.$title ;
		// set pack
		$pack = array('duration'=>1, 'item_name'=>$item_name, 'buypost'=>1,'cost'=>$cost, 'title'=>$title, 'product'=>$product, 'post_id'=>$post_id);
	}else if(isset($_POST['postpack_id'])){
		// post pack purchase 
		$postpack_id = $_POST['postpack_id'];// pcak id
		$postpack_post_id = $_POST['postpack_post_id'];// post id where pack is listed, redirect here
		// get pack
		$postpack = mgm_get_postpack($postpack_id);
		$cost     = mgm_convert_to_currency($postpack->cost);
		$product  = json_decode($postpack->product, true);
		// item name
		$item_name = 'Purchase Post Pack - '.$postpack->name;
		// post id
		$post_id   = mgm_get_postpack_posts_csv($postpack_id);
		// set pack
		$pack = array('duration'=>1, 'item_name'=>$item_name, 'buypost'=>1,'cost'=>$cost, 'title'=>$postpack->name, 
		              'product'=>$product, 'post_id'=>$post_id, 'postpack_id'=>$postpack_id, 'postpack_post_id'=>$postpack_post_id);
	}	
	
	// check
	if(!$pack){
		echo __('Error in Payment! No data available ');
		die();
	}
	
	// get coupon
	$coupon = mgm_save_partial_fields(array('on_postpurchase'=>true),'mgm_postpurchase_field',$pack['cost'],false);
	if($coupon !== false){			
		// main			
		if($pack && $coupon['cost']){
			// original
			$pack['original_cost'] = $pack['cost'];
			$pack['cost'] = $coupon['cost'];
		}	
						
		// mark pack as coupon applied
		$pack['coupon_id'] = $coupon['id'];		
	}
	// get payment modules
	$a_payment_modules = mgm_get_class('system')->get_active_modules('payment');
	// init 
	$payment_modules = array();			
	// when active
	if($a_payment_modules){
		// loop
		foreach($a_payment_modules as $payment_module){
			// not trial
			if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;				
			// store
			$payment_modules[] = $payment_module;					
		}
	}
	// init
	$button = '';
	// transaction 
	$tran_id = 0;
	// loop modules
	foreach ($payment_modules as $module) {						
		// object
		$mod_obj = mgm_get_module($module, 'payment');						
		// check buypost support 
		if(in_array('buypost',$mod_obj->supported_buttons)){								
			// create transaction
			if($tran_id==0){
				$tran_id = $mod_obj->_create_transaction($pack);
			}
			// button code
			$button_code = $mod_obj->get_button_buypost(array('pack'=>$pack,'tran_id'=>$tran_id), true);							 
			// get button
			$button .= "<div style='width:100%;'>" . $button_code . "</div>";
		}
	} 
	
	// html
	$return   = '<div style="margin-bottom:5px;width:100%;">'. __('Please Select a Payment Gateway.','mgm').'</div>';
	$return  .= $button;
	// return 
	return $button;
}
/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function mgm_register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );
	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
	if ( $errors->get_error_code() )
		return $errors;
	$user_pass = wp_generate_password();
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		return $errors;
	}
	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
	wp_new_user_notification( $user_id, $user_pass );
	return $user_id;
}
//set custom errors
function mgm_set_errors($wp_error, $return = false) {	
	if ( empty($wp_error) )
		$wp_error = new WP_Error();
	if ( $wp_error->get_error_code() ) {
		$error_string = '';
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);
			foreach ( $wp_error->get_error_messages($code) as $error ) {
				$error = mgm_replace_message_links($code, $error);
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}	
		$error_string .= "\n".'<link rel="stylesheet" href="'. MGM_ASSETS_URL . 'css/mgm_messages.css' .'" type="text/css" media="all" />';			
		if ( !empty($errors) )
			$error_string .= '<div class="mgm_message_error">' . apply_filters('login_errors', $errors) . "</div>\n";
		if ( !empty($messages) )
			$error_string .= '<p class="mgm_message_success">' . apply_filters('login_messages', $messages) . "</p>\n";		
		if($return)
			return $error_string;
		else 	
			echo $error_string;		
	}
	//let return false if $return = true;	
}
//replace message links with custom link
function mgm_replace_message_links($code, $error_str) {		
	switch ($code) {
		case 'invalid_username':		
		case 'incorrect_password'://replace old lost password link with new custom link
			$prev_link 	= site_url('wp-login.php?action=lostpassword', 'login');
			$replace 	= mgm_get_custom_url('lostpassword');
			$error_str = str_replace($prev_link, $replace, $error_str);
			break;
	}
	
	return $error_str;
}
// get url
function mgm_get_url() {
	$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$http = (isset($_SERVER['https'])) ? "https://" : "http://";
	return $http . $url ;
}
// check wp form
function mgm_check_wordpress_login(){
	// current url
	$current_url = mgm_current_url();	
	// checl
	if(preg_match('/wp-login\.php/',$current_url) || preg_match('/wp-signup\.php/',$current_url)){// considering multi-site 
		return true;
	} 
	// return
	return false;
}
// lost password
function mgm_user_lostpassword_form($use_default_links = true) {		
	$form_action = mgm_get_url();		
	$user_login = '';
	if(isset($_POST['wp-submit-lp'])) {		
		$user_login = htmlentities(mgm_stripslashes_deep($_POST['user_login']));
		$_POST['user_login'] = sanitize_text_field($_POST['user_login']);
		$errors = mgm_retrieve_password();	
		if ( !is_wp_error( $errors ) ) {									
			mgm_redirect(add_query_arg( array('lp_updated' => 'true'), $form_action));					
			exit;
		}	
	}
	$html = '';
	$html .= "\n".'<link rel="stylesheet" href="'. MGM_ASSETS_URL . 'css/mgm_messages.css' .'" type="text/css" media="all" />';
	$html .= '<p class="mgm_message">' . __('Please enter your username or e-mail address. You will receive a new password via e-mail.','mgm') . '</p>';
	
	if ( isset($_GET['lp_updated']) ){
		$html .= '<div class="mgm_message">';
		$message = apply_filters('mgm_lostpassword_success_message', __('Check your e-mail for the confirmation link.','mgm'));
		$html .= '<p><strong>'.$message.'</strong></p></div>';
	}
	// set error !
	if(isset($errors) && is_object($errors)) {
		/*Not working on some servers: issue 381
		ob_start();
		mgm_set_errors($errors);
		$error_html = ob_get_clean();
		*/
		$error_html = mgm_set_errors($errors, true);
		if($error_html && !empty($error_html))
			$html = $error_html . $html;
	}
	
	$html .= '<form class="mgm_form" name="lostpasswordform" id="lostpasswordform" action="'. $form_action .'" method="post">';
	$html .= '<p>
				<label>'.__('Username or E-mail:','mgm').'<br />
				<input type="text" name="user_login" id="user_login" class="input" value="'. $user_login .'" size="40" tabindex="10" /></label>
			</p>';
	do_action('lostpassword_form');
	$html .= '<input type="hidden" name="redirect_to" value="" />
				<p><input type="submit" name="wp-submit-lp" id="wp-submit-lp" class="button-primary mgm-lostpassword-button" value="'.__('Get New Password','mgm').'" tabindex="100" /></p>';
	$html .= '</form>';
	//issue#: 488
	//$html .='<p id="nav">';
	
	// print	
	$login_link = mgm_get_custom_url('login');
	if (get_option('users_can_register')){
		$register_link = mgm_get_custom_url('register');
		$html .='<a class="mgm-login-link" href="'.$login_link.'">'. __('Log in','mgm').'</a> | ';
		$html .='<a class="mgm-register-link" href="'.$register_link.'">'.__('Register','mgm').'</a>';
	}else {
		$html .='<a class="mgm-login-link" href="'.$login_link.'">'.__('Log in','mgm').'</a>';
	}	
	//$html .= '</p>';
	
	// return
	return $html;
}
//retrive/lost password
function mgm_retrieve_password() {
	global $wpdb, $current_site;

	$errors = new WP_Error();
	if ( empty( $_POST['user_login'] ) && empty( $_POST['user_email'] ) )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.','mgm'));
	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by_email(trim($_POST['user_login']));
		if ( empty($user_data) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.','mgm'));
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_userdatabylogin($login);
	}	
	do_action('lostpassword_post');
	if ( $errors->get_error_code() )
		return $errors;
	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.','mgm'));
		return $errors;
	}
	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	do_action('retreive_password', $user_login);  // Misspelled and deprecated
	do_action('retrieve_password', $user_login);
	$allow = apply_filters('allow_password_reset', true, $user_data->ID);
	if ( ! $allow )
		return new WP_Error('no_password_reset', __('Password reset is not allowed for this user','mgm'));
	else if ( is_wp_error($allow) )
		return $allow;
	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
	if ( empty($key) ) {
		// Generate something random for a key...
		$key = wp_generate_password(20, false);
		do_action('retrieve_password_key', $user_login, $key);
		// Now insert the new md5 key into the db
		$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
	}
	$title = apply_filters('retrieve_password_title', $title);
	$message = apply_filters('retrieve_password_message', $message, $key);

	if ( $message && !mgm_mail($user_email, $title, $message) ) {
		$errors->add('empty_username', __('The e-mail could not be sent.','mgm') . "<br />" . __('Possible reason: your host may have disabled the mail() function...','mgm') );
		return $errors;	
	}
	return true;
}
// validate module
// types : payment/autoresponder
function mgm_is_valid_module($module, $type='payment'){
	// check in avilable modules
	$available_modules = mgm_get_class('system')->active_modules[$type];				
	// match
	if(in_array($module, $available_modules)){
		return true;
	}
	// error
	return false;
}
//custom user login
function mgm_user_login($use_default_links = true) {
	
	//$form_action = mgm_get_url();
	$form_action = mgm_get_custom_url('login');		
	$user_login = '';
	$user_pwd	= '';
	$html = '';
	
	$errors = mgm_custom_user_login();
	//check logged in cookie:	
	$rememberme = ! empty( $_POST['rememberme'] );
	$interim_login = isset($_REQUEST['interim-login']);
	
	if ( isset($_POST['log']) )
		$user_login = esc_attr(stripslashes($_POST['log']));
	//issue# 525	
	elseif ($cookie_userid = wp_validate_auth_cookie('','logged_in')) {	//check a valid logged cookie exists	
		$arr_loggedin_cookie = wp_parse_auth_cookie('', 'logged_in');
		//get mgm_member
		$mgm_member = mgm_get_member($cookie_userid);
		//mark checked
		$rememberme = true;	
		//get login from cookie		
		$user_login = esc_attr(stripslashes($arr_loggedin_cookie['username']));			
		//password from meber object
		$user_pwd = $mgm_member->user_password;	
	}
	
	$redirect_to = '';
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];		
	}
	
	// set error !
	if(isset($errors) && is_object($errors)) {
		/* Not working on some servers: issue 381
		ob_start();
		mgm_set_errors($errors);
		$error_html = ob_get_clean();
		*/
		$error_html = mgm_set_errors($errors, true);
		if(!empty($error_html))
			$html = $html . $error_html;
	}	 
	
	$html .= '<form class="mgm_form" name="loginform" id="loginform" action="'.$form_action.'" method="post">
	<p>
		<label>'.__('Username','mgm').'<br />
		<input type="text" name="log" id="user_login" class="input" value="'.esc_attr($user_login).'" size="40" tabindex="10" /></label>
	</p>
	<p>
		<label>'.__('Password', 'mgm').'<br />
		<input type="password" name="pwd" id="user_pass" class="input" value="'. esc_attr($user_pwd) .'" size="40" tabindex="20" /></label>
	</p>';
	
	do_action('login_form');
	
	$html .='<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" '.checked( $rememberme, true, false ).'  /> '.__('Remember Me','mgm').'</label></p>
		<p>
			<input class="mgm-login-button" type="submit" name="wp-submit" id="wp-submit" value="'.__('Log In','mgm').'" tabindex="100" />';
	if ( $interim_login ) {
		$html .= '<input type="hidden" name="interim-login" value="1" />';
	} else {
		$html .= '<input type="hidden" name="redirect_to" value="'.esc_attr($redirect_to).'" />';
	}
	$html .= '<input type="hidden" name="testcookie" value="1" />
		</p>
	</form>';
	
	// print links	
	if ( !$interim_login ) {
		
		$register_link = mgm_get_custom_url('register');
		$lost_password_link = mgm_get_custom_url('lostpassword');	
		
		if ( isset($_GET['checkemail']) && in_array( $_GET['checkemail'], array('confirm', 'newpass') ) ) :
		elseif ( get_option('users_can_register') ) :
			$html .= '<a class="mgm-register-link" href="'. $register_link.'">' .__('Register').'</a> |
			<a class="mgm-lostpassword-link" href="'. $lost_password_link .'" title="'.__('Password Lost and Found').'">'.__('Lost your password?').'</a>';
		else :
			$html .= '<a class="mgm-lostpassword-link" href="'. $lost_password_link .'" title="'.__('Password Lost and Found').'">'.__('Lost your password?').'</a>';
	endif;
		
	} else {
		$html .='</div>';
	}
	
	$html .= '<script type="text/javascript">
				function wp_attempt_focus(){
				setTimeout( function(){ try{';
				if ( $user_login || $interim_login ) {
					$html .='d = document.getElementById(\'user_pass\');';
				} else {
					$html .='d = document.getElementById(\'user_login\');';
				}
				//$html .= ' d.value = \'\';';
				$html .= ' d.focus();';
				$html .= '} catch(e){}';
				$html .= '}, 200);';
				$html .= '}';
	
	if ( @!$error ) {
		$html .= 'wp_attempt_focus()';
	}
	$html .= '</script>';
	// return
	return $html;
}

// login
function mgm_custom_user_login() {
	$secure_cookie = '';
	if(isset($_GET['action']) && !empty($_GET['action']) ) {
		switch ($_GET['action']) {
			//logout
			case 'logout':
				do_action('mgm_logout');
				break;	
				
			//check password reset:	
			case 'rp':
			case 'resetpass':						
				$errors = apply_filters('mgm_validate_reset_password',$_GET['key'], $_GET['login']);
				if ( is_wp_error($errors) ) {
					return $errors;	
				}
				do_action('mgm_reset_password', $_GET['key'], $errors ); //please note: $errors will carry user object if no error				
				break;
		}
	}
	
	$interim_login = isset($_REQUEST['interim-login']);
	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_userdatabylogin($user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}	
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}
	
	$reauth = empty($_REQUEST['reauth']) ? false : true;
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;
	
	$user = wp_signon('', $secure_cookie);
	
	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);
	
	if ( !is_wp_error($user) && !$reauth ) {
		if ( $interim_login ) {
			$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
			login_header( '', $message ); ?>
			<script type="text/javascript">setTimeout( function(){window.close()}, 8000);</script>
			<p class="alignright">
			<input type="button" class="button-primary" value="<?php esc_attr_e('Close'); ?>" onclick="window.close()" /></p>
			</div></body></html>
		<?php exit;
		}
		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) )
			$redirect_to = admin_url('profile.php');
		wp_safe_redirect($redirect_to);
		exit();
	}
	
	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) || $reauth )
		$errors = new WP_Error();
	
	// If cookies are disabled we can't log in even with a valid user+pass	
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) ) {
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));		
	}

	// Some parts of this script use the main login form to display a message
	if ( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )
		$errors->add('loggedout', __('You are now logged out.'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
		$errors->add('registerdisabled', __('User registration is currently not allowed.'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
		$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
		$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
		$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
	elseif	( $interim_login )
		$errors->add('expired', __('Your session has expired. Please log-in again.'), 'message');

	// Clear any stale cookies.
	if ( $reauth )
		wp_clear_auth_cookie();

	if ( $errors->get_error_code() )
		return $errors;
	else 
		return true;		
}

// create default pages
function mgm_create_default_pages(){	
	global $wpdb;
	// if only not run
	if(!get_option('mgm_default_pages')){
		global $wpdb,$wp_rewrite;
		// get object
		if(!is_object($wp_rewrite)){
			$wp_rewrite= new WP_Rewrite();
		}
		// system
		$system = mgm_get_class('system');
		
		// homeurl
		$homeurl = trailingslashit(get_option('siteurl'));
		// default		
		$default = array('post_author'=>1, 'post_date'=>date("Y-m-d H:i:s"), 'post_date_gmt'=>gmdate("Y-m-d H:i:s"), 'post_status'=>'publish', 
		                 'comment_status'=>'closed', 'ping_status'=>'closed', 'post_type'=>'page');
		
		// contents
		$register_page_content     = '[user_register]';	
		$profile_page_content      = '[user_profile]';	
		$login_page_content        = '[user_login]';	
		$lostpassword_page_content = '[user_lostpassword]';		
		$transaction_page_content  = '[transactions]';	
		$membership_details_page_content  = '[membership_details]';	
		$membership_contents_page_content = '[membership_contents]';	
		// define pages
		$pages = array(  
			array('post_title'=>'Register'      , 'post_content'=>$register_page_content     , 'post_name'=>'register'),
			array('post_title'=>'Profile'       , 'post_content'=>$profile_page_content      , 'post_name'=>'profile'),	
			array('post_title'=>'Login'         , 'post_content'=>$login_page_content        , 'post_name'=>'login'),	
			array('post_title'=>'Lost Password' , 'post_content'=>$lostpassword_page_content , 'post_name'=>'lostpassword'),	
     		array('post_title'=>'Transactions'  , 'post_content'=>$transaction_page_content  , 'post_name'=>'transactions'),
			array('post_title'=>'Membership Details'  , 'post_content'=>$membership_details_page_content  , 'post_name'=>'membership-details'),
			array('post_title'=>'Membership Contents' , 'post_content'=>$membership_contents_page_content , 'post_name'=>'membership-contents')
		);
		// order
		$menu_order = 0;
		// capture new
		$new_pages = array();
		// loop
		foreach($pages as $page){
			// check already exists, impove to add type also wp 3+	
			$current_page = get_page_by_title( $page['post_title'], ARRAY_A, (isset($page['post_type']) ? $page['post_type'] : 'page'));			
			// does not exist		
			if(isset($current_page['ID']) && (int)$current_page['ID'] > 0) {
				// store
				$page = $current_page;			
			}else{				
				// order
				$page['menu_order'] = $menu_order++;								
				// insert post
				$page['ID'] = @wp_insert_post( array_merge($default, $page) );						
				// update guid				
				$page['guid'] = add_query_arg(array('page_id'=>(int)$page['ID']), $homeurl);	
				// update
				$wpdb->update( $wpdb->posts, array( 'guid' => $page['guid'] ), array('ID'=>$page['ID']) );	
			}
		}		
		
		// flush
		if($menu_order>1 && is_object($wp_rewrite)){
			$wp_rewrite->flush_rules();
		}
		
		// permalink type
		$custom_permalink = (get_option('permalink_structure')) ? true : false;		
		// loop
		foreach($pages as $page){
			// page url
			$page_url = $custom_permalink ? trailingslashit($homeurl.$page['post_name']) : add_query_arg(array('page_id'=>(int)$page['ID']), $homeurl);	
			// set
			$system->setting[str_replace('-','_',$page['post_name']) . '_url'] = $page_url;				
		}		
		// update object
		// update_option('system', $system);
		$system->save();
		// track
		update_option('mgm_default_pages', 1);
	}
}
//get url from settings
function mgm_get_custom_url($name, $load_default = false, $query_arg = array()) {
	// get system
	$system = mgm_get_class('system');
	// on name
	switch($name) {
		case 'login':
			$url = site_url('wp-login.php?action=login', 'login');
			if(!$load_default && (isset($system->setting['login_url']) && !empty($system->setting['login_url']))) 
				$url = $system->setting['login_url'];				
		break;
		case 'register':
			$url = site_url('wp-login.php?action=register', 'login');
			if(!$load_default && (isset($system->setting['register_url']) && !empty($system->setting['register_url']))) 
				$url = $system->setting['register_url'];
			elseif(!$load_default)
				$url = add_query_arg(array('method'=>'register'),mgm_home_url('subscribe'));	
		break;	
		case 'lostpassword':
			$url = site_url('wp-login.php?action=lostpassword');
			if(!$load_default && (isset($system->setting['lostpassword_url'])&& !empty($system->setting['lostpassword_url']))) 
				$url = $system->setting['lostpassword_url'];
		break;
		case 'profile':
			$url = admin_url('profile.php');
			if(!$load_default && (isset($system->setting['profile_url']) && !empty($system->setting['profile_url']))) 
				$url = $system->setting['profile_url'];
		break;
		case 'transactions':						
			if((isset($system->setting['transactions_url']) && !empty($system->setting['transactions_url']))) 
				$url = $system->setting['transactions_url'];
			else
				$url = mgm_home_url('subscribe');	
				
			// fix for https
			$url = mgm_ssl_url($url);		
		break;	
		case 'membership_details':			
			$url = admin_url(sprintf('%s.php?page=mgm/profile',(is_super_admin() ? 'users':'profile')));
			if(!is_super_admin() && !$load_default && (isset($system->setting['membership_details_url']) && !empty($system->setting['membership_details_url']))) 
				$url = $system->setting['membership_details_url'];
		break;
		case 'membership_contents':			
			$url = admin_url(sprintf('%s.php?page=mgm/membership/content',(is_super_admin() ? 'users':'profile')));
			if(!is_super_admin() && !$load_default && (isset($system->setting['membership_contents_url']) && !empty($system->setting['membership_contents_url']))) 
				$url = $system->setting['membership_contents_url'];
		break;
		default:
			$url = mgm_home_url('subscribe');
		break;	
	}
	
	// mgm_array_dump($query_arg);
	// add query
	if(!empty($query_arg)) {
		$url = add_query_arg( $query_arg, $url);		
	}	
	// return
	return $url;
}

// mgm_country_from_code
function mgm_country_from_code($code){
	global $wpdb;
	// get country
	$country = $wpdb->get_var("SELECT `name` FROM ". TBL_MGM_COUNTRY ." WHERE `code` = '{$code}'");	
	// return 
	return $country;
}

// mgm_get_user_custom_fields
function mgm_get_member_custom_fields($user_id){
	// mgm_member
	$mgm_member = mgm_get_member($user_id);
	// get
	$custom_fields = $mgm_member->custom_fields;	
	// return 
	return $custom_fields;
}

// mgm_get_user_custom_fields
function mgm_set_member_custom_fields($user_id, $custom_fields=array()){
	// mgm_member
	$mgm_member = mgm_get_member($user_id);	
	// set fields
	$mgm_member->set_custom_fields($custom_fields, true);// merge
	// update option
	// update_user_option($user_id,'mgm_member',$mgm_member, true);
	$mgm_member->save();	
	// return 
	return true;
}

// create slug
function mgm_create_slug($text, $len=50){
	// trim
	if($len) $text = substr(trim($text),0,$len);
	// return
	return strtolower(preg_replace('/\s+/', '_', $text));
}

// selected subscription
function mgm_get_selected_subscription($args=array()){
	// encoded
	$encoded = false;
	// get 
	if(empty($args)) {
		$args    = $_GET;
		$encoded = true;// from GET encoded
	}		
	// mgm_array_dump($args);
	// init
	$selected = array();
	// package
	if(isset($args['package']) && !empty($args['package'])){
		// decode		
		$package = ($encoded) ? base64_decode($args['package']) : $args['package'];											
		// check
		if(strpos($package,'#') !== FALSE){
			list($selected['name'],$selected['id']) = explode('#',$package);
		}else{
			$selected['name'] = $package;
		}
	}else if(isset($args['membership']) && !empty($args['membership'])){
	// membership
		// set		
		$selected['name'] = ($encoded) ? base64_decode($args['membership']) : $args['membership'];			
	}
	// return 
	return (isset($selected['name'])) ? $selected :  false;
}

// select subscription
function mgm_select_subscription($pack,$selected){
	// init
	$checked = '';
	// default select
	if(isset($selected['name'])){
		// match
		if($selected['name'] == $pack['membership_type']){
			// set
			if(isset($selected['id'])){
				// match
				if($selected['id'] == $pack['id']){
					$checked = ' checked="checked"';
				}
			}else{
				$checked = ' checked="checked"';
			}
		}									
	}else{
		$checked = (intval($pack['default']) == 1 ? ' checked="checked"':'');	
	}		
	// return
	return $checked;
}

// checks if pack is allowed on extend subscription page
function mgm_pack_extend_allowed($pack){
	// set allow_renewal
	if(!isset($pack['allow_renewal'])){
		$pack['allow_renewal'] = 1;
	}
	// active
	if(!isset($pack['active']['extend'])){
		$pack['active']['extend'] = 1;
	}
	// return if both are true
	return (bool)$pack['allow_renewal'] & (bool)$pack['active']['extend'] ;
}

// checks if pack is allowed on upgrade subscription page
function mgm_pack_upgrade_allowed($pack){	
	// active
	if(!isset($pack['active']['upgrade'])){
		$pack['active']['upgrade'] = 1;
	}
	// return 
	return (bool)$pack['active']['upgrade'] ;
}

// checks if pack is allowed on register subscription page
function mgm_pack_register_allowed($pack){	
	// active
	if(!isset($pack['active']['register'])){
		$pack['active']['register'] = 1;
	}
	// return 
	return (bool)$pack['active']['register'] ;
}

// mgm_notice
function mgm_notice($message, $error = false){	
	echo sprintf('<div id="message" class="%s"><p><strong>%s</strong></p></div>',( $error ? 'error' : 'updated fade'),$message);    
}
// expire member: for force expire test
function mgm_expire_member($user_id,$expire_date){
	// get member
	$mgm_member = mgm_get_member($user_id);
	// if today 
	if($expire_date == date('Y-m-d')){
		$new_status              = MGM_STATUS_CANCELLED;
		$new_status_str          = __('Subscription cancelled','mgm');
		$mgm_member->status      = $new_status;
		$mgm_member->status_str  = $new_status_str;					
		$mgm_member->expire_date = date('Y-m-d');			
	}else{
		// status
		$new_status     = MGM_STATUS_AWAITING_CANCEL;	
		$new_status_str = __(sprintf('Subscription awaiting cancellation on %s',date(MGM_DATE_FORMAT, strtotime(expire_date))),'mgm');			
		// set reset date
		$mgm_member->status_reset_on = $expire_date;
		$mgm_member->status_reset_as = MGM_STATUS_CANCELLED;
	}			
	// update user
	// update_user_option($user_id, 'mgm_member', $mgm_member, true);	
	$mgm_member->save();
}
//check logged in user is having the supplied subscriptions 
function mgm_user_is($type = array()) {
	// get current user
	$current_user = wp_get_current_user();
	//if user not logged in
	if(!isset($current_user->ID) || (isset($current_user->ID) && $current_user->ID == 0))
		return false;
	
	// get object	
	$mgm_member = mgm_get_member($current_user->ID);
	$arr_mt = mgm_get_subscribed_membershiptypes($current_user->ID, $mgm_member);
	$membership_type = strtolower($mgm_member->membership_type);	
	if(!in_array($membership_type, $arr_mt))
		$arr_mt[] = $membership_type;
	$return = false;
	// check
	if($type) {		
		if(is_string($type)){
			//if($membership_type == strtolower($type) )
			if(in_array(strtolower($type), $arr_mt) )
				$return = true; 
		}elseif(is_array($type)){ 			
			foreach ($type as $t)
				if(in_array(strtolower($t), $arr_mt)) {
					$return = true;
					break;					
				}					
		}
	}
	// return
	return $return;	
}

// encode
function mgm_encode_package($pack){
	// subs	
	$subs_text = implode('|', array($pack['cost'],$pack['duration'],$pack['duration_type'],$pack['membership_type'],$pack['id']));	
	// return
	return base64_encode($subs_text);		
}
// decode
function mgm_decode_package($package){
	// get
	@list($cost, $duration, $duration_type, $membership_type, $pack_id) = explode('|', base64_decode($package));
	// return
	return array('cost'=>$cost, 'duration'=>$duration, 'duration_type'=>$duration_type, 'membership_type'=>$membership_type, 'pack_id'=>$pack_id);
}
// get packages
/**
 * return packs
 *
 * @param obj $packs_obj :can be null
 * @param obj $types_obj :can be null
 * @param array $exclude : exclude ids, can be null
 * @return array
 */
function mgm_get_subscription_packages($packs_obj = null, $types_obj = null, $exclude = array()){
	// object
	$packs_obj = mgm_get_class('subscription_packs');	
	$types_obj = mgm_get_class('membership_types');
	// packages
	$packages = array();	
	// loop		
	foreach ($packs_obj->get_packs('all') as $pack) {	
		//skip passed ids		
		if(in_array($pack['id'], $exclude)) continue;
		// enc
		$subs_opt_enc = mgm_encode_package($pack);
		// key
		$packages[] = array('id' => $pack['id'], 'key'=>$subs_opt_enc,'label'=>$packs_obj->get_pack_desc($pack),'membership'=>$types_obj->get_type_name($pack['membership_type']),'description'=>$pack['description']);		
	}	
	// return
	return $packages;
}
//manually check a script is already included
function mgm_is_script_already_included($script, $is_url = false) {	
	global $wp_scripts,$mgm_scripts;
		
	if(is_array($wp_scripts->registered)) {		
		$i = 0;
		foreach ($wp_scripts->registered as $obj) {			
			$file = (!$is_url) ? basename($obj->src) : $obj->src;				
			if($script == $file || (is_array($mgm_scripts) && in_array($file,$mgm_scripts)) ) {		
				return true;
			}
		}		
	}	
	
	if(is_array($mgm_scripts) && in_array($script,$mgm_scripts)) {			
		return true;
	}
	
	return false;
}
// mgm_get_calendar_year_range
function mgm_get_calendar_year_range(){
	// system
	$system = mgm_get_class('system');
	// ranges
	$range_lower = $system->setting['date_range_lower'];
	$range_upper = $system->setting['date_range_upper'];
	
	// defaults
	if(!$range_lower) $range_lower = 50;
	if(!$range_upper) $range_upper = 10;
	
	// return 
	return sprintf('-%d:+%d',$range_lower,$range_upper);	
}
//check mgm scripts can be loaded: using in mgm_init.php
function mgm_if_load_admin_scripts() {
	$page = mgm_request_var('page');
	return ((!empty($page) && preg_match('/mgm\/admin/', $page)) || //mgm pages 
			(is_numeric(mgm_request_var('post')) && mgm_request_var('action') == 'edit') || //edit post page
			'post-new.php' == basename($_SERVER['SCRIPT_NAME']) //add new post page
			) ? true : false;
}

function mgm_is_customfield_active($fields = array(), $cf_register_page = array()) {
	if(!empty($fields)) {
		if(empty($cf_register_page))
			$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true)));			
		foreach ($cf_register_page as $cf) {
			if(in_array($cf['name'], $fields)) {
				return true;
			}
		}
	}
	return false;
}
//create test cookie
function mgm_check_cookie() {
	if ( !is_user_logged_in() ) {
		//Set a cookie now to see if they are supported by the browser.
		setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
		if ( SITECOOKIEPATH != COOKIEPATH )
			setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);
	}
}
// create token
function mgm_create_token($type = 'alphanum', $len=8){
	// unique
	if( $type != 'unique'){
		// type
		switch ($type)
		{
			case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'alphanum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'numeric'	:	$pool = '0123456789';
			break;
			case 'nozero'	:	$pool = '123456789';
			break;
		}
		// init
		$str = '';
		// loop
		for ($i=0; $i < $len; $i++)
		{
			$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		// return
		return $str;
	}else{
		// return
		return md5(uniqid(mt_rand()));
	}	
	// default
	return mt_rand();	
}
//autologin redirection
function mgm_auto_login_redirect($id) {
	global $current_user;
	//check user logged in
	if(isset($current_user->ID) && $current_user->ID > 0 )
		return false;
	//check settings	
	$setting = mgm_get_class('system')->setting;		
	if(!isset($setting['enable_autologin']) || (isset($setting['enable_autologin']) && $setting['enable_autologin'] != 'Y' ))
		return false;
			
	$id = mgm_decode_id($id);	
	if(is_numeric($id)) {		
		//mgm_import_dependency('classes/mgm_payment');
		$obj_payment = new mgm_payment();
		$transaction = $obj_payment->_get_transaction($id);					
		//consider only registration
		if($transaction['payment_type'] != 'subscription_purchase' || (!isset($transaction['data']['is_registration']) || (isset($transaction['data']['is_registration']) && $transaction['data']['is_registration'] != 'Y' )) )
			return false;
			
		if(is_numeric($transaction['data']['user_id']) && $transaction['data']['user_id'] > 0 && mgm_verify_transaction($transaction) ) {			
				
			$user_id = $transaction['data']['user_id'];
			$secure_cookie = false;
			if( !force_ssl_admin() && get_user_option('use_ssl', $user_id )) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}			
			$user = wp_set_current_user($user_id);				
			if(is_object($user)) {
				$mgm_member = mgm_get_member($user_id);							
				//login and redirect:
				wp_signon(array('user_login' => $user->user_login, 'user_password' => $mgm_member->user_password ), $secure_cookie);												
				//if not yet redirected from mgm_login_redirect:						
				wp_safe_redirect(mgm_get_custom_url('profile'));				
				exit;
			}	
			//done	
		}
	}
	
	return false;
}
//verify transaction
function mgm_verify_transaction($transaction) {	
	//check IP
	if(!isset($transaction['data']['client_ip']) || (isset($transaction['data']['client_ip']) && $transaction['data']['client_ip'] != $_SERVER['REMOTE_ADDR']) ) //treat as fraud if try from different IP
		return false;
	//check datetime:
	if(!isset($transaction['transaction_dt']) || (isset($transaction['transaction_dt']) && (strtotime(date('Y-m-d H:i:s')) - strtotime($transaction['transaction_dt'])) > (60*10) )) { //delay is restricted to 10 minutes
		return false;
	}
	if($transaction['status'] != MGM_STATUS_ACTIVE)
		return false;
				
	return true;	
}
/**
 * 
 * converts mgm_member object to array to be saved in multiple membership type array
 *
 * @package MagicMembers
 * @since 2.5
 * @param $mgm_member, object 
 * @return array
 */
function mgm_get_members_packids($mgm_member) {
	$pack_ids = array();
	if(is_numeric($mgm_member->pack_id)) {
		$pack_ids[] = $mgm_member->pack_id;
	}
	if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types) ) {
		foreach ($mgm_member->other_membership_types as $key => $val) {
			$val = mgm_convert_array_to_memberobj($val, $mgm_member->id);
			if(is_numeric($val->pack_id) && $val->status == MGM_STATUS_ACTIVE)
				$pack_ids[] = $val->pack_id;
		}
	}
	
	return $pack_ids;
}
/** 
 * convert mgm_member object to array to be saved in multiple membership type array
 *
 * @package MagicMembers
 * @since 2.5
 * @param $mgm_member, object 
 * @return array
 */ 
function mgm_convert_memberobj_to_array($mgm_member) {
	$arr_mgm_member = array();
	
	if(!empty($mgm_member)) {		
		foreach ($mgm_member as $key => $value) {				
			if(is_array($value) || is_object($value)) {
				$arr_mgm_member[$key] = mgm_convert_memberobj_to_array($value); 
			}else {	
				$arr_mgm_member[$key] = $value;	
			}			
		}
	}	
	
	return $arr_mgm_member;
}
/** 
 *convert array to mgm_member object to be used as $mgm_member object
 * This is specifically for multiple membership type
 *
 * @package MagicMembers
 * @since 2.5
 * @param $mgm_member, object 
 * @return array
 */ 
function mgm_convert_array_to_memberobj($arr_mgm_member, $user_id, $attach_id = true) {
	$mgm_member = new stdClass();
	
	if(!empty($arr_mgm_member)) {		
		foreach ($arr_mgm_member as $key => $value) {
			if(is_array($value) || is_object($value)) {
				$mgm_member->$key = mgm_convert_array_to_memberobj($value, $user_id);
			}else {
				$mgm_member->$key = $value;
			}
		}
	}	
	//attach id:
	if($attach_id)
		$mgm_member->id = $user_id;
	
	return $mgm_member;
}
//saves multiple mgm_member objects(inner mgm_objects)
//Every primary mgm_member object will have an array other_membership_types[] to hold multiple mgm_member objects.

//$user_id: user id
//$fields: array/object of mgm_member fields 
//update_index: index of inner mgm_member object in other_membership_types array, if specifically passed other_membership_types arrayt will be updated directly
function mgm_save_another_membership_fields($fields, $user_id, $update_index = null) {	
	$mgm_member = mgm_get_member($user_id);	
	
	//make sure each membership object is an array:
	$fields = mgm_convert_memberobj_to_array($fields);
	$arr_remove = array('ID','id', 'name', 'code', 'description', 'saving','custom_fields', 'other_membership_types', 'setting');
	foreach ($arr_remove as $remove) {
		if(isset($fields[$remove]))
			unset($fields[$remove]);
	}		
	//checks if it is a new entry in other_membership_types array	
	if(!isset($mgm_member->other_membership_types) || (isset($mgm_member->other_membership_types) && empty($mgm_member->other_membership_types))) {		
		$mgm_member->other_membership_types[] = $fields;		
	}else {		
		//looping through multiple membership object 	
		$saved = false;
		foreach ($mgm_member->other_membership_types as $key => $memtypes) {
			//this is to treat each member array(old objects) as member object:(considering backward compatibility)			
			$memtypes = mgm_convert_array_to_memberobj($memtypes, $user_id);			
			//if supplied mgm_object's membership type == existing member object's membership type
			//OR
			//supplied index == $key 
			if( ($memtypes->membership_type == $fields['membership_type']) || (is_numeric($update_index) && $update_index == $key ) ) {			
				//reset if already saved				
				$mgm_member->other_membership_types[$key] = $fields;
				$saved = true;
				break;
			}
		}	
		//if didn't find any match insert as new	
		if(!$saved) {			
			$mgm_member->other_membership_types[] = $fields; 			
		}
	}
	// update user
	// update_user_option($user_id, 'mgm_member', $member, true);	
	$mgm_member->save();
	
}
//get member object for another purchase
//checks and returns inner mgm_member object if multiple membership types exist within the primary mgm_member object.
//where $membership_type is the membership to be matched with
function mgm_get_member_another_purchase($user_id, $membership_type) {
	// get member
	$mgm_member = mgm_get_member($user_id);	
	$arr_remove = array('ID','id', 'name', 'code', 'description', 'saving','custom_fields', 'other_membership_types', 'setting');
	// check
	if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0){
		// loop
		foreach ($mgm_member->other_membership_types as $key => $memtypes) {
			$memtypes = mgm_convert_array_to_memberobj($memtypes, $user_id);			
			//skip default values:
			//if(strtolower($memtypes->membership_type) == 'guest' || $memtypes->status == MGM_STATUS_NULL )	continue;			
			if(strtolower($memtypes->membership_type) == 'guest' )	continue;
			// match			
			if($memtypes->membership_type == $membership_type ) {				
				foreach ($arr_remove as $remove) {
					if(isset($memtypes->$remove))
						unset($memtypes->$remove);
				}
				//return an object of the type mgm_member:
				return $memtypes;// what is this object ? array?
			}
		}
	}
	// return	
	return null;
}
//get purchased membership types
function mgm_get_subscribed_membershiptypes($user_id, $mgm_member = null) {
	if($mgm_member == null)
		$mgm_member = mgm_get_member($user_id);	
	
	$types = array();
	if(isset($mgm_member->membership_type) && !empty($mgm_member->membership_type)) {
		$types[] = $mgm_member->membership_type;
	}
	if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types) ) {
		foreach ($mgm_member->other_membership_types as $key => $val) {
			$val = mgm_convert_array_to_memberobj($val, $user_id);
			if(isset($val->membership_type) && !empty($val->membership_type) && $val->status == MGM_STATUS_ACTIVE)
				$types[] = $val->membership_type;
		}
	}
	
	return $types;
}
//get purchased membership types with name/label
function mgm_get_subscribed_membershiptypes_with_label($user_id, $mgm_member = null ) {
	$arr_mtlabel = array();
	$arr_mt = mgm_get_subscribed_membershiptypes($user_id, $mgm_member);
	$mgm_membership_types = mgm_get_class('membership_types')->membership_types;
	foreach ($mgm_membership_types as $type => $label )				
		if(in_array($type, $arr_mt)) {
			$arr_mtlabel[$type] = $label;						
		}
	return $arr_mtlabel;				
}
//check post pack join
function mgm_check_post_packjoin($mgm_member, $post) {
	$return = true;	
	if ($pack_join = $mgm_member->join_date) {
		// if hide old content is set in subscription type
		if ($mgm_member->hide_old_content) {
		   $post_date = strtotime($post->post_date);
		   // reset no access
		   $return = false;	
		   // join date, TODO, We have to make it take last_active_date or similar for DRIP posts					   
		   if ($pack_join < $post_date) {
			   $return = true;    
		   }
		}
	 }
	 return $return;
}
//post access delay check
function mgm_check_post_access_delay($mgm_member, $user, $access_delay) {
	$return = true;
	$mt_access_delay = (int)$access_delay[$mgm_member->membership_type];
	// delay
	if ($mt_access_delay > 0) {
		$reg     = $user->user_registered;
		$reg     = mktime(0,0,0,substr($reg, 5, 2), substr($reg, 8, 2), substr($reg, 0, 4));
		$user_at = $reg + (86400*$mt_access_delay);
		if ($user_at >= time()) {
			$return = false;
		}
	}
	return $return;
}
//check any more subscriptions exist to purchase:
function mgm_check_purchasable_level_exists($user_id, $mgm_member = null) {	
	$subscribed_types = mgm_get_subscribed_membershiptypes($user_id, $mgm_member);	
	$subscribed_types = array_unique(array_merge($subscribed_types, array('free','trial','guest')));
	$mgm_membership_types = mgm_get_class('membership_types')->membership_types;	
	$mgm_membership_types = array_unique(array_keys($mgm_membership_types));
		
	return ((count($subscribed_types) > 3 && count(array_diff($mgm_membership_types, $subscribed_types)) == 0) ? false : true);			
}
//remove role from user:
function mgm_remove_userroles($user_id, $mgm_member, $no_status_check = false) {	
	if($mgm_member->status == MGM_STATUS_EXPIRED || $no_status_check) {
		if($mgm_member->pack_id){
			$free_role = 'subscriber';
			//find role role assigned to free membership
			$arr_packs = mgm_get_class('subscription_packs')->get_packs();
			foreach ($arr_packs as $p) {
				if($p['membership_type'] == 'free') {
					$free_role = $p['role'];
					break;
				}
			}
			//get role assigned to the pack 
			$pack = mgm_get_class('subscription_packs')->get_pack($mgm_member->pack_id);			
			$remove_role = $pack['role'];			
			if($remove_role == $free_role || $remove_role == "")
				return;
			//instanciate role class				
			$obj_role = new mgm_roles();				
			$obj_role->replace_user_role($user_id, $remove_role, $free_role );	
		}
	}
}
//readjust user object to keep the role - pack balance
function mgm_remove_excess_user_roles($user_id, $add_if_absent = false) {	
	$mgm_member = mgm_get_member($user_id);
	$user = new WP_User($user_id);
	$pack_ids = mgm_get_members_packids($mgm_member);
	$pack_roles = array();
	$obj_role = new mgm_roles();
	foreach ($pack_ids as $pack_id) {
		$pack = mgm_get_class('subscription_packs')->get_pack($pack_id);
		if(!empty($pack['role']))
			$pack_roles[] = $pack['role'];
	}		
	
	//remove from user object: 
	if(isset($user->roles) && !empty($user->roles) && !empty($pack_roles)) {		
					
		$arr_all_roles = $obj_role->_get_default_roles();
		$arr_mgm_roles = $obj_role->_get_mgm_roles();
		if(!empty($arr_mgm_roles))
			$arr_all_roles = array_merge($arr_all_roles, $arr_mgm_roles);
		foreach ($user->roles as $role) {
			if(!in_array( $role, $pack_roles )) {		
				//make sure delete only default/mgm roles:		
				if(in_array($role, $arr_all_roles)) {
					$user->remove_role($role);													
				}		
			}
		}
	}	
	
	//add if role is absent:
	if(!empty($pack_roles) && $add_if_absent) {
		$user = new WP_User($user_id);
		foreach ($pack_roles as $prole) {
			if(!in_array( $prole, $user->roles )) {
				$obj_role->add_user_role($user_id, $prole, false, false);								
			}
		}
	}
}
function mgm_print_userroles($user_id) {	
	$obj_role = new mgm_roles();				
	$obj_role->print_role($user_id);		
}
//upload photo:
function mgm_photo_file_upload() {	
	$user = wp_get_current_user();
	// init
	$download_file = array();
	// init messages
	$status  = 'error';	
	$message = 'file upload failed';
	$field_name = 'photo';
	$field_type = ($user->ID > 0) ? 'profile' : 'register';	
	if(isset($_FILES['mgm_'.$field_type.'_field']['tmp_name'][$field_name])) {		
		// upload check
		if (is_uploaded_file($_FILES['mgm_'.$field_type.'_field']['tmp_name'][$field_name])) {
			// random filename
			srand(time());
			$uniquename = substr(microtime(),2,8).rand(1000, 9999);
			// paths
			$oldname = strtolower($_FILES['mgm_'.$field_type.'_field']['name'][$field_name]);
			$newname = preg_replace('/(.*)\.(.*)$/i', $uniquename.'.$2', $oldname);	
			$thumb_name = (str_replace('.','_thumb.',$newname));		
			$medium_name = (str_replace('.','_medium.',$newname));	
			$filepath = MGM_FILES_UPLOADED_IMAGE_DIR . $newname;
			$arr_type = explode('/', $_FILES['mgm_'.$field_type.'_field']['type'][$field_name]);			
			if(strtolower($arr_type[0]) == 'image' && in_array(strtolower($arr_type[1]), array('jpg','jpeg','pjpeg','png','x-png','gif'))) {						
				$setting = mgm_get_class('system')->setting;
				if(isset($setting['image_size_mb']) && !empty($setting['image_size_mb']))	
					$max_size = $setting['image_size_mb']; 		 
				else
					$max_size = '2'; 
				//check size:	
				if($_FILES['mgm_'.$field_type.'_field']['size'][$field_name] > 0 && (round($_FILES['mgm_'.$field_type.'_field']['size'][$field_name]/(1024*1024),2)) <= $max_size ) {
					// upload
					if(move_uploaded_file($_FILES['mgm_'.$field_type.'_field']['tmp_name'][$field_name], $filepath)) {	
						// permission
						chmod($filepath, 0755);		
						$obj_irs = mgm_get_class('image_resize');
						if($obj_irs->resize_image($filepath,  MGM_FILES_UPLOADED_IMAGE_DIR . $thumb_name )) {
							$obj_irs->resize_image($filepath,  MGM_FILES_UPLOADED_IMAGE_DIR . $medium_name,'medium' );
							chmod(MGM_FILES_UPLOADED_IMAGE_DIR . $thumb_name, 0755);
							chmod(MGM_FILES_UPLOADED_IMAGE_DIR . $medium_name, 0755);
							unlink(MGM_FILES_UPLOADED_IMAGE_DIR . $newname);
							
							//delete previous image:
							$user = wp_get_current_user();					
							if($field_type == 'profile') {
								$mgm_member = mgm_get_member($user->ID);						
								if(isset($mgm_member->custom_fields->$field_name) && !empty($mgm_member->custom_fields->$field_name)) {
									$prev_thumb 	= MGM_FILES_UPLOADED_IMAGE_DIR . basename($mgm_member->custom_fields->$field_name);
									$prev_medium 	= MGM_FILES_UPLOADED_IMAGE_DIR . basename(str_replace('_thumb','_medium',$mgm_member->custom_fields->$field_name));
									if(file_exists($prev_thumb))
										unlink($prev_thumb);
									if(file_exists($prev_medium))	
										unlink($prev_medium);
								}
							}
							// set download_file				
							$download_file  = array('hidden_field_name' => 'mgm_'.$field_type.'_field['.$field_name.']','file_name' => $medium_name, 'file_url' => MGM_FILES_UPLOADED_IMAGE_URL . $medium_name, 'width' => $setting['medium_image_width'], 'height' => $setting['medium_image_height']);					
							// status
							$status  ='success';	
							$message =__('file uploaded successfully, it will be attached when you save the data.','mgm');
						}
					}
				}else {
					$message =__(sprintf('Please select an image file with size less than %s.', $max_size ),'mgm');
				}
			}else {
				$message =__('Please select an image file.','mgm');
			}
		}
	}
	// send ouput		
	ob_end_clean();	
	echo json_encode(array('status'=>$status,'message'=>$message, 'upload_file'=>$download_file));
	// end out put
		
	ob_flush();
	exit();
}
//get package redirect url:
function mgm_get_user_package_redirect_url($user_id) {	
	$user = new WP_user($user_id);	
	$mgm_member = mgm_get_member($user_id);
	//get highlighted role:
	$role = $user->roles[0];
	$packids 	= mgm_get_members_packids($mgm_member);
	$obj_pack 	= mgm_get_class('subscription_packs');
	$obj_mem 	= mgm_get_class('membership_types');
	if(!empty($packids)) {
		foreach ($packids as $pid) {
			$pack = $obj_pack->get_pack($pid);			
			//get login redirect url of the highlighted role:
			if($role == $pack['role']) {				
				$login_redirect_url = $obj_mem->get_login_redirect($pack['membership_type']);	
				if(!empty($login_redirect_url))	
					return $login_redirect_url;		
				break;
			}
		}
	}
	// return
	return null;
}
//logout redirection url:
function mgm_logout_redirect_url() {
	global $current_user;
	
	if(isset($current_user->ID) && $current_user->ID > 0 ) {
		$mgm_member = mgm_get_member($current_user->ID);
		$role 		= $current_user->roles[0];
		$packids 	= mgm_get_members_packids($mgm_member);
		$obj_pack 	= mgm_get_class('subscription_packs');
		$obj_mem 	= mgm_get_class('membership_types');								
		//get from highlighted packs
		if(!empty($packids)) {
			foreach ($packids as $pid) {
				$pack = $obj_pack->get_pack($pid);			
				//get login redirect url of the highlighted role:				
				if($role == $pack['role']) {			
					$logout_redirect_url = $obj_mem->get_logout_redirect($pack['membership_type']);	
					break;
				}
			}
		}
		
		//get from settings
		if(empty($logout_redirect_url)) {
			$system = mgm_get_class('system');	
			$logout_redirect_url = trim($system->setting['logout_redirect_url']);
			//get site url			
			if(empty($logout_redirect_url)) {
				$logout_redirect_url = get_option('siteurl');
			}
		}
				
		return $logout_redirect_url;					
	}
	
	return false;
}
//find users with the given package
function mgm_get_users_with_package($pack_id, $uids = array()) {
	global $wpdb;	    
	if(empty($uids)) {
		//from cache
		$uids = wp_cache_get('all_user_ids', 'users');	 
		if(!$uids) {	    
			//$uids = $wpdb->get_col('SELECT ID from ' . $wpdb->users. ' WHERE ID <> 1');
			$uids = mgm_get_all_userids();
			wp_cache_set('all_user_ids', $uids, 'users');
		}
	}

	$arr_pack_users = array();
	foreach ($uids as $uid) {
		$user = mgm_get_member($uid);
		if(isset($user->pack_id) && $user->pack_id == $pack_id ) {
			$arr_pack_users[] = $uid; 
		}
	}
	return $arr_pack_users;
}
//public function to get transaction details:
function mgm_get_transaction($transaction_id){
	// global
	global $wpdb;	
	// check
	if((int)$transaction_id>0){
		// sql
		$sql = "SELECT * FROM ".TBL_MGM_TRANSACTION." WHERE id='{$transaction_id}'";		
		// row
		$row  = $wpdb->get_row($sql,ARRAY_A);		
		// reset data
		$row['data'] = json_decode($row['data'],true);
		// return
		return $row;
	}
	// error
	return false;
}
//check a date is valid: can be enhanced
function mgm_is_valid_date($date, $delimiter = '/') {
	$arr_date = explode($delimiter, $date, 3);
	if(count($arr_date) == 3 && is_numeric($arr_date[0]) && is_numeric($arr_date[1]) && is_numeric($arr_date[2]) )
		return true;
		
	return false;	
}
//get all user ids 
function mgm_get_all_userids($fields = array('ID'), $func = 'get_col') {
	global $wpdb;
	$result = array();
	$limit = 1000;
	$start = 0;
	$count  = $wpdb->get_var('SELECT count(*) from ' . $wpdb->users . ' WHERE ID <> 1');
	if($count) {
		for( $i = $start; $i < $count; $i = $i + $limit ) {
			$result = array_merge($result, mgm_patch_partial_users($i, $limit, $fields, $func));
			//a small delay of 0.01 second 
			usleep(10000);			
		}
	}
		
	return $result;
}

function mgm_patch_partial_users($start, $limit, $fields, $func) {
	global $wpdb;
	$qry = 'SELECT '. implode(',', $fields) .' from ' . $wpdb->users . ' WHERE ID <> 1 ORDER BY ID LIMIT '. $start.','.$limit;	
	$result  = $wpdb->$func($qry);	
	return (array) $result;
}
// wrapper for user option, due to some object serilization bug, system goes to shutdown
// use flat option datafetch
function mgm_get_user_option($option, $user_id){
	global $wpdb;
	// get var
	$meta_value = $wpdb->get_var("SELECT `meta_value` FROM `{$wpdb->usermeta}` WHERE meta_key = '{$option}' AND user_id ='{$user_id}' LIMIT 1");
	// check
	if($meta_value){
		return $meta_value = maybe_unserialize($meta_value);
	}	
	// error
	return false;	
}
/**
 * encode variable
 *
 * @param int/var $id
 * @return string
 */
function mgm_encode_id($id) {
	return base64_encode(base64_encode($id));
}
/**
 * decode variable
 *
 * @param int/var $id
 * @return string
 */
function mgm_decode_id($id) {
	return base64_decode(base64_decode($id));
}
// delete file
function mgm_delete_file($filepath){
	// check
	if(is_file($filepath)){
		// success
		return unlink($filepath);
	}
	// error
	return false;
}
// mgm_api_access_allowed
function mgm_api_access_allowed(){
	// if all
	if(MGM_API_ALLOW_IP == 'all') return true;
	
	// in list
	$allowed_ips = explode(',', MGM_API_ALLOW_HOST);
		
	// check
	if(is_array($allowed_ips)){
		// check
		return (in_array($_SERVER['HTTP_HOST'], $allowed_ips));
	}
	
	// false
	return false;
}
// mgm_ellipsize
function mgm_ellipsize($str,$len=50){
	// return if less
	if(strlen($str) < $len) return $str;
	
	// sub
	return $str = substr($str,0,$len). '...';
}
/**
 * return setting: date_farmat_short to mysql format
 *
 * @param string $date
 * @return string: mysql date(Y-m-d)
 */
function mgm_format_shortdate_to_mysql($date) {	
	$delimiters = array(',', '\/', '-', ' ', ':', ';');
	$delimiter = '/';
	$settings = mgm_get_class('system')->setting;
	$format = $settings['date_farmat_short'];
	foreach ($delimiters as $d) {
		if(preg_match("/$d/", $date)){
			$delimiter = stripslashes($d);
		}
	}
	
	$date_splitted = explode($delimiter, $date);
	
	$format_splitted = explode($delimiter, $format);
	
	foreach ($format_splitted as $key => $fs) {
		$fs = trim($fs);
		switch (strtolower($fs)) {
			case 'y':
			case 'yy':
			case 'yyyy':
				$year = $date_splitted[$key];
				break;
			case 'm':
			case 'mm':
				$month = $date_splitted[$key];
				break;	
			case 'd':
			case 'dd':
				$day = $date_splitted[$key];
				break;	
		}
	}
	
	//return mysql std date format Y-m-d
	if(isset($year) && isset($month) && isset($day)) {
		$year 	= substr($year, 0, 4);
		$month 	= substr($month, 0, 2);			
		$day 	= substr($day, 0, 2);
		
		return $year.'-'.$month.'-'.$day;
	}
		
	return false; 	
}
// get errors
function mgm_subscription_purchase_errors(){
	// error
	$error_field = mgm_request_var('error_field'); 
	// check
	if(!empty($error_field)) {
		// obj
		$errors = new WP_Error();
		// type
		switch (mgm_request_var('error_type')) {
			case 'empty':
				$error_string = 'You must provide a '.$error_field;
				break;
			case 'invalid':
				$error_string = 'Invalid '.$error_field;
				break;	
		}	
		// add			
		$errors->add( $error_field, __( '<strong>ERROR</strong>: '.$error_string, 'mgm' ));	
		// return
		return mgm_set_errors($errors, true);					
	}
	// nothing
	return '';
}
/**
 * Returns timezone formatted current server date/timestamp
 *
 * @param string $format (date format)
 * @return array
 */
function mgm_get_current_datetime($format = 'Y-m-d', $format_timestamp = true) {
	$return = array();
	$timestamp = strtotime(current_time('mysql'));		
	//format:
	$return['date'] 	= date($format, $timestamp);
	//get formatted timestamp
	$return['timestamp']= $format_timestamp ? strtotime($return['date']) : $timestamp;	
	
	return $return; 
}
// mgm redirect
function mgm_redirect($location, $status = 302, $type='header'){
	$setting = mgm_get_class('system')->setting;
	//if default value is not overridden:
	//read from settings:
	if($type == 'header' && isset($setting['redirection_method'])) {
		$type = $setting['redirection_method'];
	}
	// meta redirect
	switch($type){		
		case 'javascript':			
			// only if no headers
			//commented because if headers sent, wp_redirect cannot be used, so we will need to depend on js redirect
			//if(!headers_sent()){
			// print
				echo sprintf('<script language="javascript">window.location = "%s";</script>', $location); exit;
			//}
		break;
		case 'meta':			
			// only if no headers
			if(!headers_sent()){
			// print
			    echo sprintf('<meta http-equiv="refresh" content="1;url=%s" />', $location);exit;
			}
		break;
	}	
	// default always	
	wp_redirect($location, $status);
	exit;
}
// end file /core/libs/functions/mgm_misc_functions.php
