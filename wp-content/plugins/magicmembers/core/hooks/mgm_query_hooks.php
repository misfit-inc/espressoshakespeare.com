<?php 
// query
// generate rewrite
add_action('generate_rewrite_rules', 'mgm_rewrite_rules');
// add query vars
add_filter('query_vars', 'mgm_rewrite_queryvars' );
// flush rewrite
// add_action('init', 'mgm_flush_rewrite_rules');
// parse query hook for param loads
add_action('parse_query', 'mgm_parse_query');		
// parse protected
add_action('send_headers', 'mgm_url_router');
// add rules 
function mgm_rewrite_rules( $wp_rewrite ){	
	// global $wp_rewrite;
	// named 
	if(get_option('permalink_structure') != ''){	
		// array
		// issue#: 364
		if(!$download_slug = mgm_get_class('system')->setting['download_slug']){
			// default
			$download_slug = 'download';		
		}
		// set parsable vars
		$vars = array('subscribe','purchase','transactions','payments',$download_slug);
		// loop
		foreach($vars as $var){
			// if not a page 
			if(get_page_by_path($var) == NULL){
				// set rule
				$new_rules[$var.'$']    = 'index.php?'.$var.'=1';	
				$new_rules[$var.'(.*)'] = 'index.php?'.$var.'=' .$wp_rewrite->preg_index(1) ;
			}
		}	
		// add rules
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}

// add query vars
function mgm_rewrite_queryvars( $query_vars ){  
	// array
	// issue#: 364
	if(!$download_slug = mgm_get_class('system')->setting['download_slug']){
		// default
		$download_slug = 'download';	
	}
	// set parsable vars
	$vars = array('subscribe','purchase','transactions','payments',$download_slug);
	// loop
	foreach($vars as $var){
		// if not a page
		if(get_page_by_path($var)==NULL){
			// add 
			array_push($query_vars, $var);
		}
	}
	// return
	return $query_vars;
}

// flush rewrite rules: discarded for wp 3+
/*******************************************
function mgm_flush_rewrite_rules(){
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
}
********************************************/

// payment tracking
function mgm_parse_query(){
 	global $wpdb,$wp_query;				
   	
 	//if comes from thank you page with autologin parameter: 	
 	if($redirect_transid = mgm_request_var('redirect_transid')){
		// login
 		mgm_auto_login_redirect($redirect_transid);
 	}	 	
 	//check file uploads: 	
 	if( $upload = mgm_request_var('file_upload') ) {
		// option
 		switch ($upload) {
 			case 'image':
 				mgm_photo_file_upload();
 			break;
 		}
		// no process further
 		exit;
 	}
 	
	// default
	$process_payments = false;	
	
	// check
	foreach(array('payments','subscribe','purchase','transactions') as $query){
		// set if
		if(isset( $wp_query->query_vars[$query] )){
			// process
			$process_payments = true; break;
		}
	}
  	
	// payment process
	if( $process_payments ) {	
		// payment html
		mgm_get_payment_html();
		// exit
		exit();				
	}
		
	// download flag 
	if(!$download_slug = mgm_get_class('system')->setting['download_slug']){
	// wp-ecommerce also uses download as slug, check
		$download_slug = 'download';	
	}
	// set 
	if( isset( $wp_query->query_vars[$download_slug] ) ) {	
		// get method
		$code = mgm_request_var('code'); 		
		// check								
		mgm_download_file($code);		
		// exit 		
		exit();
	}	
}

// router for url protection, API calls
function mgm_url_router(){
	global $wpdb,$route,$wp_query,$window_title;	
	
	// trim
	$current_uri = trim($_SERVER['REQUEST_URI']);
	// check admin 
	if(!is_super_admin()){		
		// TODO, improve code for less query, WARNING, not to use direct URI in SQL, injection may happen		
		// having all is better to protet all scenario
		// sql
		$sql = "SELECT url,membership_types FROM `".TBL_MGM_POST_PROTECTED_URL."` WHERE `post_id` IS NULL ORDER BY LENGTH(`url`) DESC";
		// direct urls
		$direct_urls = $wpdb->get_results($sql);		
		// check
		if($direct_urls){
			// loop
			foreach($direct_urls as $direct_url){
				// url path only
				$uri = trim(parse_url($direct_url->url,PHP_URL_PATH));
				// append end
				if(substr($uri,-1) == '*'){
					$uri = preg_quote(str_replace('*','',$uri), '/') . '(.*)';
				}elseif(substr($uri,-4) == ':any'){
					$uri = preg_quote(str_replace(':any','',$uri), '/') . '(.*)';
				}else{
					$uri = preg_quote($uri,'/');
				}
				// pattern
				$uri_pattern = "#{$uri}#i";			
				// match
				if(strcasecmp($uri, $current_uri) == 0 || preg_match($uri_pattern, $current_uri)){
					// membership types
					$membership_types = json_decode($direct_url->membership_types,true);
					// check
					$current_user = wp_get_current_user();
					// access
					$access = false;
					// check
					if($current_user->ID){
						// get member
						// $member = mgm_get_member($current_user->ID);
						$user_membership_types = array();
						// default
						$user_membership_types[] = mgm_get_user_membership_type($current_user->ID,'code');
						// multiple
						$user_membership_types[] = mgm_get_subscribed_membershiptypes($current_user->ID);
						// loop 
						if(is_array($membership_types)){
							// loop
							foreach($membership_types as $membership_type){
								// check
								if(in_array($membership_type,$user_membership_types)){
									$access = true;
									break;
								}
							}
						}
					}
					// add filter
					if(!$access){
						add_filter('the_content', 'mgm_url_content_protection');	
					}				
				}
			}
		}	
	}
	
	// rest api
	if(mgm_api_access_allowed()){
		// match
		if(	preg_match('/^' . preg_quote(MGM_API_URI_PREFIX, '/') . '/', $current_uri) ){
			// forward to api handler
			mgm_restapi_server::init(); exit;
		}
	}	
}
// mgm_url_content_protection
function mgm_url_content_protection($content){
	// return 'Protected';
	$mgm_system = mgm_get_class('system');
	// check
	$current_user = wp_get_current_user();
	// message code	
	if($current_user->ID){// logged in user
		$message_code = mgm_post_is_purchasable() ? 'private_text_purchasable' : 'private_text_no_access';
	}else{// logged out user
		$message_code = mgm_post_is_purchasable() ? 'private_text_purchasable_login' : 'private_text';
	}
	// protected_message	
	$protected_message = sprintf('<span class="mgm_private_no_access">%s</span>',mgm_private_text_tags(mgm_stripslashes_deep($mgm_system->get_template($message_code, array(), true))));			
	// filter message
	$protected_message = mgm_replace_message_tags($protected_message);
	
	// return
	return $content = $protected_message;
}
// payment html
function mgm_get_payment_html($return=false){
	// get method
	$method = mgm_request_var('method'); 	
	
	// switch $method
	switch($method){
		case 'payment_return':// after payment return with get/post values and process
		case 'payment_notify':// silent post back, IPN		
		case 'payment_cancel':// cancelled	
		case 'payment_unsubscribe':// unsubscribe tracking	
		case 'payment_html_redirect': // proxy for html redirect
		case 'payment_credit_card': // proxy for credit_card processing						
			// get module
			$module = mgm_request_var('module');
			// validate module
			if(mgm_is_valid_module($module)){
				// object
				$module_obj = mgm_get_module($module, 'payment');					
				// process
				$output = $module_obj->invoke(str_replace(array('payment_'), 'process_', $method));// invoke process_return,process_notify,process_cancel,process_unsubscribe
				// html redirect					
				if($method=='payment_html_redirect'){
					// set in globals
					$GLOBALS['mgm_html_outout'] = $output;						
					// if template exists
					if($return){
						$template_file = MGM_CORE_DIR.'html/payment_processing_return.php';	
					}else if( file_exists( TEMPLATEPATH . '/payment_processing.php' ) ){	
						$template_file = TEMPLATEPATH.'/payment_processing.php';
					}else{
						$template_file = MGM_CORE_DIR.'html/payment_processing.php';	
					}	
					// include template
					include($template_file);
				}
			}else if($method=='payment_unsubscribe'){
			// default unsubscribe
				return mgm_member_unsubscribe();
			}else{
				return __('Invalid module supplied','mgm');
			}				
		break;
		case 'payment_processed':// processed				
			// get module
			$module = mgm_request_var('module');
			// validate module
			if(mgm_is_valid_module($module)){
				// object
				$module_object = mgm_get_module($module, 'payment');
				// redirect logic moved, in all cases same page is loaded			
				// if template exists
				if($return){
					$template_file = MGM_CORE_DIR.'html/payment_processed_return.php';	
				}else if( file_exists( TEMPLATEPATH . '/payment_processed.php' ) ){	
					$template_file = TEMPLATEPATH.'/payment_processed.php';
				}else{
					$template_file = MGM_CORE_DIR.'html/payment_processed.php';	
				}	
				// include template
				include($template_file);
			}else{
				return __('Invalid module supplied','mgm'); 
			}
		break;			
		case 'payment_purchase': 					
			// if template exists
			if($return){
				$template_file = MGM_CORE_DIR.'html/payment_post_purchase_return.php';	
			}else if( file_exists( TEMPLATEPATH . '/payment_post_purchase.php' ) ){	
				$template_file = TEMPLATEPATH.'/payment_post_purchase.php';
			}else{
				$template_file = MGM_CORE_DIR.'html/payment_post_purchase.php';	
			}	
			// include template
			include($template_file);
		break;	
		case 'payment_subscribe':// form
		case 'payment':// form
		default:				
			// if template exists
			if($return){
				$template_file = MGM_CORE_DIR.'html/payment_subscribe_return.php';	
			}elseif( file_exists( TEMPLATEPATH . '/payment_subscribe.php' ) ){	
				$template_file = TEMPLATEPATH.'/payment_subscribe.php';
			}else{
				$template_file = MGM_CORE_DIR.'html/payment_subscribe.php';	
			}	
			// include template
			include($template_file);
		break;
		case 'register':				
			// if template exists
			if($return){
				$template_file = MGM_CORE_DIR.'html/register_page_return.php';	
			}elseif( file_exists( TEMPLATEPATH . '/register_page.php' ) ){	
				$template_file = TEMPLATEPATH.'/register_page.php';
			}else{
				$template_file = MGM_CORE_DIR.'html/register_page.php';	
			}	
			// include template
			include($template_file);			
		break;
		case 'profile'://user profile page					
			// if template exists
			if($return){
				$template_file = MGM_CORE_DIR.'html/profile_page_return.php';	
			}elseif( file_exists( TEMPLATEPATH . '/profile_page.php' ) ){	
				$template_file = TEMPLATEPATH.'/profile_page.php';
			}else{
				$template_file = MGM_CORE_DIR.'html/profile_page.php';	
			}	
			// include template
			include($template_file);		
		break;
		case 'lost_password':
			// if template exists
			if($return){
				$template_file = MGM_CORE_DIR.'html/lost_password_page_return.php';	
			}elseif( file_exists( TEMPLATEPATH . '/lost_password_page.php' ) ){	
				$template_file = TEMPLATEPATH.'/lost_password_page.php';
			}else{
				$template_file = MGM_CORE_DIR.'html/lost_password_page.php';	
			}	
			// include template
			include($template_file);
		break;	
		case 'user_login':
			// if template exists
			if($return){
				$template_file = MGM_CORE_DIR.'html/login_page_return.php';	
			}elseif( file_exists( TEMPLATEPATH . '/login_page.php' ) ){	
				$template_file = TEMPLATEPATH.'/login_page.php';
			}else{
				$template_file = MGM_CORE_DIR.'html/login_page.php';	
			}	
			// include template
			include($template_file);
		break;	
	}
}
// end file	