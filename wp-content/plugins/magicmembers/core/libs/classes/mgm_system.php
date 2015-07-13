<?php
/**
 * Magic Members system class
 * extends object to save options to database
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_system extends mgm_object{
	// module types
	var $module_types   = array();
	// active modules
	var $active_modules = array();	
	// active plugins
	var $active_plugins = array();	
	// settings
	var $setting        = array();	
	
	// construct
	function __construct(){
		// php4
		$this->mgm_system();
	}
	
	// construct
	function mgm_system(){
		// parent
		parent::__construct(); 
		// defaults
		$this->_set_defaults();
		// read vars from db
		$this->read();// read and sync
	}	
	
	// defaults
	function _set_defaults(){
		// code
		$this->code        = __CLASS__;
		// name
		$this->name        = 'System Lib';
		// description
		$this->description = 'System Lib';
		
		// module_types
		$this->module_types = array('payment', 'autoresponder');
		
		// active payment modules		
		$this->active_modules['payment'] = array('mgm_free', 'mgm_trial', 'mgm_paypal');	
		
		// active autoresponder module		
		$this->active_modules['autoresponder'] = 'mgm_aweber';	
		
		// active plugins	
		$this->active_plugins = array('mgm_plugin_rest_api');	
		
		// settings payment
		$this->setting['payment'] = array();
		
		// settings autoresponder
		$this->setting['autoresponder'] = array();
		
		// currency
		$this->setting['currency'] = 'USD';
		
		// admin_email
		$this->setting['admin_email'] = get_option('admin_email');
		
		// subscription_name
		$this->setting['subscription_name'] = '[blogname] [membership] Subscription';
		
		// email_sender_name
		// $this->setting['email_sender_name'] = get_option('blogname');
		
		// login_redirect_url
		$this->setting['login_redirect_url'] = '';
		
		// hide_custom_fields
		$this->setting['hide_custom_fields'] = 'N';
		
		// hide_membership_content
		// $this->setting['hide_membership_content'] = 'N';
		
		// disable_gateway_emails
		$this->setting['disable_gateway_emails'] = 'Y';
		
		// download_hook
		$this->setting['download_hook'] = 'download';
		
		// download_slug
		$this->setting['download_slug'] = 'download';
		
		// admin_role
		// $this->setting['admin_role'] = 'administrator';
		
		// from_name		
		$this->setting['from_name'] = get_option('blogname');
		
		// from_email		
		$this->setting['from_email'] = get_option('admin_email'); // may cause trouble
		
		// email content_type		
		$this->setting['email_content_type'] = 'text/html';
		
		// email charset		
		$this->setting['email_charset'] = 'UTF-8';
		
		// reminder_days_to_start
		$this->setting['reminder_days_to_start'] = 5;
		
		// reminder_days_incremental
		$this->setting['reminder_days_incremental'] = 'Y';
		
		// reminder_days_incremental_ranges
		$this->setting['reminder_days_incremental_ranges'] = '5,3,1';
		
		// modified_registration		
		// $this->setting['modified_registration'] = 'Y';
						
		// content_protection , used instead of both hide_posts & public_access	
		$this->setting['content_protection'] = 'partly';	
		
		// public_content_words	
		$this->setting['public_content_words'] = '0'; // words	
		
		// content_hide_by_membership, all post page will be hidden if current user type does not match
		$this->setting['content_hide_by_membership'] = 'N';	
		
		// no_access_redirect_loggedin_users		
		$this->setting['no_access_redirect_loggedin_users'] = '';
		
		// no_access_redirect_loggedout_users		
		$this->setting['no_access_redirect_loggedout_users'] = '';		
		
		// redirect_on_homepage		
		$this->setting['redirect_on_homepage'] = 'N';
		
		// use_rss_token		
		$this->setting['use_rss_token'] = 'Y';
		
		// use_ssl_paymentpage		
		$this->setting['use_ssl_paymentpage'] = 'N';	
		
		// post exclusion
		$this->setting['excluded_pages'] = array();
		
		// post purchase
		$this->setting['post_purchase_price'] = 4.00;	

		// register url
		$this->setting['register_url'] = '';	
		
		// profile url
		$this->setting['profile_url'] = '';	
		
		// transactions page url
		$this->setting['transactions_url'] = '';

		// login page url
		$this->setting['login_url'] = '';
		
		// lost password page url
		$this->setting['lostpassword_url'] = '';	
		
		//membership_details_url url
		$this->setting['membership_details_url'] = '';	
		
		// membership_contents_url
		$this->setting['membership_contents_url'] = '';
		// add all these urls in get_custom_pages_url method, this is used in content protection
		// and disable locking in full protection mode
		
		// date ranges
		$this->setting['date_range_lower']  = '50';	// -	
		$this->setting['date_range_upper']  = '10';	// +	
		// formats
		$this->setting['date_farmat']       = MGM_DATE_FORMAT;
		$this->setting['date_farmat_long']  = MGM_DATE_FORMAT_LONG;
		$this->setting['date_farmat_short'] = MGM_DATE_FORMAT_SHORT;
		
		//autologin
		$this->setting['enable_autologin'] = 'N';	
		//enable_multiple_level_purchase
		$this->setting['enable_multiple_level_purchase'] = 'N';	

		//Image field settings:
		//thumbnail_image_width
		$this->setting['thumbnail_image_width'] = '32';		
		//thumbnail_image_height
		$this->setting['thumbnail_image_height']= '32';	
		//medium_image_width	
		$this->setting['medium_image_width'] 	= '120';	
		//medium_image_height	
		$this->setting['medium_image_height'] 	= '120';		
		//image_size_mb
		$this->setting['image_size_mb'] 		= '2';	
		
		//reCAPTCHA settings:		
		$this->setting['recaptcha_public_key'] 			= '';		
		$this->setting['recaptcha_private_key'] 		= '';		
		$this->setting['recaptcha_api_server'] 			= 'http://www.google.com/recaptcha/api';		
		$this->setting['recaptcha_api_secure_server'] 	= 'https://www.google.com/recaptcha/api';		
		$this->setting['recaptcha_verify_server'] 		= 'www.google.com';
						
		//custom logout redirect url
		$this->setting['logout_redirect_url'] 	= '';		
		
		// external downloads/aws s3
		$this->setting['aws_enable_s3']  = 'N';	
		$this->setting['aws_key'] 	     = '';	
		$this->setting['aws_secret_key'] = '';	
		
		//enable_nested_shortcode_parsing
		$this->setting['enable_nested_shortcode_parsing'] = 'Y';
		
		//enable post_url_redirection
		$this->setting['enable_post_url_redirection'] = 'N';
		
		//category access denied redirect url	
		$this->setting['category_access_redirect_url'] = '';
		
		//restapi server enable	
		$this->setting['rest_server_enabled'] = 'Y';// Y|N
		// allowed output formats 
		$this->setting['rest_output_formats'] = array('xml','json', 'phps', 'php');// response types
		// allowed input methods
		$this->setting['rest_input_methods'] = array('get','post', 'put', 'delete');// methods
		// consumsion limit
		$this->setting['rest_consumption_limit'] = 1000;// limit
	}
	
	// get_subscription_name
	function get_subscription_name($pack){
		// membership
		$membership = mgm_get_class('membership_types')->get_type_name($pack['membership_type']);
		// return
		return str_replace(array('[blogname]', '[membership]'), array(get_option('blogname'), $membership), $this->setting['subscription_name']);
	}
	
	// get template
	function get_template($name, $data=array(), $parse=false){
		// by name
		switch($name){
			case 'tos':
			case 'subs_intro':			
				return mgm_get_template($name, NULL, 'messages');
			break;
			case 'private_text':
			case 'private_text_no_access':
			case 'private_text_purchasable':
			case 'private_text_purchasable_login':			
				// parse enabled
				if($parse){
					$message_content = mgm_get_template($name, $data, 'messages');					
					// set template
					$template = mgm_get_template('private_text_template', NULL, 'templates');
					// return
					return str_replace('[message]', $message_content, $template);
				}else{
				// parse disabled
					return mgm_get_template($name, NULL, 'messages');
				}	
			break;
			case 'login_errmsg_null':
			case 'login_errmsg_expired':
			case 'login_errmsg_trial_expired':
			case 'login_errmsg_pending':
			case 'login_errmsg_cancelled':
			case 'login_errmsg_default':
				// parse enabled
				if($parse){
					// set url data					
					$data['subscription_url'] = add_query_arg(array('action' => 'complete_payment', 'username'=>'[[USERNAME]]'), mgm_get_custom_url('transactions'));
					// return
					return mgm_get_template($name, $data, 'messages');
				}else{
				// parse disabled
					return mgm_get_template($name, NULL, 'messages');
				}
			break;
			case 'pack_desc_template':
			case 'ppp_pack_template':
			case 'register_form_row_template':
			case 'profile_form_row_template':
			case 'register_form_row_autoresponder_template':// separate
				// parse enabled
				if($parse){
					// return
					return mgm_get_template($name, $data, 'templates');
				}else{
				// parse disabled	
					return mgm_get_template($name, NULL, 'templates');
				}					
			break;
			case 'reminder_email_template_subject':
			case 'reminder_email_template_body':
			case 'registration_email_template_subject':
			case 'registration_email_template_body':
			case 'payment_success_email_template_subject':
			case 'payment_success_email_template_body':
			case 'payment_success_subscription_email_template_body':
			case 'payment_failed_email_template_subject':
			case 'payment_failed_email_template_body':
			case 'payment_active_email_template_subject':
			case 'payment_active_email_template_body':
			case 'payment_pending_email_template_subject':
			case 'payment_pending_email_template_body':
			case 'payment_error_email_template_subject':
			case 'payment_error_email_template_body':
			case 'payment_unknown_email_template_subject':
			case 'payment_unknown_email_template_body':
			case 'subscription_cancelled_email_template_subject':
			case 'subscription_cancelled_email_template_body':			
			case 'retrieve_password_email_template_subject':
			case 'retrieve_password_email_template_body':
			case 'lost_password_email_template_subject':
			case 'lost_password_email_template_body':				
				// parse enabled
				if($parse){
					return mgm_get_template($name, $data, 'emails');
				}else{
				// parse disabled
					return mgm_get_template($name, NULL, 'emails');
				}	
			break;
			case 'payment_success_title':	
			case 'payment_success_message':	
			case 'payment_failed_title':	
			case 'payment_failed_message':	
				// parse enabled
				if($parse){
					// set urls
					$data['home_url']     = trailingslashit(get_option('siteurl'));
					$data['site_url']     = trailingslashit(site_url());	
					$data['register_url'] = trailingslashit(mgm_get_custom_url('register'));					
					// login or profile
					$data['login_url']    = trailingslashit(mgm_get_custom_url((is_user_logged_in() ? 'profile' : 'login')));												
					// return
					return mgm_get_template($name, $data, 'messages');
				}else{
				// parse disabled	
					return mgm_get_template($name, NULL, 'messages');
				}				
			break;
			default:
				return __(sprintf('%s not defined.', $name),'mgm');
			break;
		}
	}
	
	// set template
	function set_template($name, $content){
		switch($name){
			case 'tos':
			case 'subs_intro':
			case 'login_errmsg_cancelled':
			case 'private_text':
			case 'private_text_no_access':
			case 'private_text_purchasable':
			case 'private_text_purchasable_login':
			case 'login_errmsg_null':
			case 'login_errmsg_expired':
			case 'login_errmsg_trial_expired':
			case 'login_errmsg_pending':
			case 'login_errmsg_cancelled':
			case 'login_errmsg_default':
			case 'payment_success_title':	
			case 'payment_success_message':	
			case 'payment_failed_title':	
			case 'payment_failed_message':	
				$group = 'messages';
			break;
			case 'pack_desc_template':
			case 'ppp_pack_template':	
			case 'register_form_row_template':
			case 'profile_form_row_template':
			case 'register_form_row_autoresponder_template':// separate
			case 'private_text_template':
				$group = 'templates';
			break;
			case 'reminder_email_template_subject':
			case 'reminder_email_template_body':
			case 'registration_email_template_subject':
			case 'registration_email_template_body':
			case 'payment_success_email_template_subject':
			case 'payment_success_email_template_body':
			case 'payment_success_subscription_email_template_body':
			case 'payment_failed_email_template_subject':
			case 'payment_failed_email_template_body':
			case 'payment_active_email_template_subject':
			case 'payment_active_email_template_body':	
			case 'payment_pending_email_template_subject':
			case 'payment_pending_email_template_body':
			case 'payment_error_email_template_subject':
			case 'payment_error_email_template_body':
			case 'payment_unknown_email_template_subject':
			case 'payment_unknown_email_template_body':
			case 'subscription_cancelled_email_template_subject':
			case 'subscription_cancelled_email_template_body':
			case 'retrieve_password_email_template_subject':
			case 'retrieve_password_email_template_body':	
			case 'lost_password_email_template_subject':
			case 'lost_password_email_template_body':
				$group = 'emails';
			break;
		}			
		// update
		$return = mgm_update_template($name, $content, $group);		
	}
	
	// get active module, payment, autoresponder
	function get_active_modules($type='payment'){
		// type
		if($type == 'autoresponder'){
			// check
			if(isset($this->active_modules[$type])){
				// return
				return (array)$this->active_modules[$type];
			}
		}else{
		// active payment modules		
			if(is_array($this->active_modules[$type])){
				// return
				return array_unique($this->active_modules[$type]);	
			}
		}		
		// error	
		return array();	
	}
	
	// activate module
	function activate_module($module, $type='payment'){
		// autoresponder is not an array:
		if($type == 'autoresponder'){			
			$this->active_modules[$type] = $module;
		}else {
			// check
			if(!is_array($this->active_modules[$type]))
				$this->active_modules[$type] = array();				
			// push
			array_push($this->active_modules[$type], $module);
			// make unique
			$this->active_modules[$type] = array_unique($this->active_modules[$type]);
		}
		// update
		// update_option(get_class($this), $this);
		return $this->save();
	}
	
	// deactivate module
	function deactivate_module($module, $type='payment'){
		// remove from system active modules, get key
		$key = array_search($module, $this->active_modules[$type]);
		// if found
		if($key!==false){
			// unset
			unset($this->active_modules[$type][$key]);
			// update
			// update_option(get_class($this), $this);
			// return 
			return $this->save();
		}
		// return 
		return false;
	}
	
	// check is active
	function is_active_module($module,$type='payment'){
		// trim prefix
		$module = str_replace(array('mgm_','mgmx_'),'',$module);// TODO add custom prefix
		
		// type
		if($type == 'autoresponder'){
			// check
			if(isset($this->active_modules[$type])){
				// return
				return ($this->active_modules[$type] == $module) ? true : false;
			}
		}else{		
			// get modules
			$modules = $this->get_active_modules($type);
			// check
			if($modules){
				// check
				if(in_array($module,$modules)){
					// return
					return true;
				}
			}
		}	
		// return
		return false;
	}
	
	// get active plugin
	function get_active_plugins(){
		// active plugins			
		if(is_array($this->active_plugins))
			return array_unique($this->active_plugins);		
		// error	
		return array();	
	}
	
	// activate plugin
	function activate_plugin($plugin){
		// push
		array_push($this->active_plugins, $plugin);
		// make unique
		$this->active_plugins = array_unique($this->active_plugins);
		// update
		// update_option(get_class($this), $this);
		// return 
		return $this->save();
	}
	
	// deactivate plugin
	function deactivate_plugin($plugin){
		// remove from system active plugins, get key
		$key = array_search($plugin, $this->active_plugins);
		// if found
		if($key!==false){
			// unset
			unset($this->active_plugins[$key]);
			// update
			// update_option(get_class($this), $this);
			// return 
			return $this->save();
		}
		// return 
		return false;
	}
	
	// check is active
	function is_active_plugin($plugin){
		// trim prefix
		$plugin = str_replace(array('mgp_plugin_','mgpx_plugin_'),'',$plugin);// TODO add custom prefix
		
		// get plugins
		$plugins = $this->get_active_plugins();
		// check
		if($plugins){
			// check
			if(in_array($plugin,$plugins)){
				// return
				return true;
			}
		}
		// return
		return false;
	}
	
	// this is used in content protection and disable locking in full protection mode
	// function: mgm_content_protection_check() ; file: hooks/content_hooks.php
	function get_custom_pages_url(){
		// init 
		$custom_pages_url = array('register_url','profile_url','transactions_url','login_url','lostpassword_url','membership_details_url','membership_contents_url');
		// return var
		$return = array();
		// loop
		foreach($custom_pages_url as $page_url){
			// key
			$return[$page_url] = $this->setting[$page_url];
		}
		// return
		return $return;
	}
	
	// apply fix to old object
	function apply_fix($old_obj){	
		// to be copied vars
		$vars = array('active_modules','active_plugins','setting');
		// set
		foreach($vars as $var){
			// var
			$this->{$var} = (isset( $old_obj->{$var} ) ) ? $old_obj->{$var} : '';
		}					
		// save
		$this->save();	
	}
	
	// prepare save, define the object vars to be saved
	// internally called by object->save()
	function _prepare(){	
		// init array
		$this->options = array();	
		// to be saved vars
		$vars = array('active_modules','active_plugins','setting');
		// set
		foreach($vars as $var){
			// var
			$this->options[$var] = $this->{$var};
		}	
	}
	
	/**
	 * Overridden function:	
	 * See the comment below:
	 *
	 * @param string option_name name of option/var
	 * @param array current_value current value for class var(can be default)
	 * @param array new_value updated value
	 */
	function _option_merge_callback($option_name, $current_value, $new_value) {				
		// This is to make sure that active_modules['payment'] doesn't contain the default options incase user deletes disables any one of them.
		// issue#: 526
		switch($option_name){
			// active modules
			case 'active_modules':
				// to copy options array as it is:
				if( isset($new_value['payment']) ) {
					$current_value['payment'] = array(); 
				}	
			break;
			case 'setting':
				// check array keys
				if( isset($new_value['rest_output_formats']) && isset($new_value['rest_input_methods'])) {
					// reset
					$current_value['rest_output_formats'] = $current_value['rest_input_methods'] = array(); 
				}			
			break;			
		}
		// update class var
		$this->{$option_name} = mgm_array_merge_recursive_unique($current_value,$new_value);		
	}
}
// core/libs/classes/mgm_system.php