<?php
// [content filters] not implemented
// add_filter('the_excerpt'           , 'mgm_filter_excerpt', 1); 

// [content protection]
// what it does: adds protection to post automatically, manual [private] tags are parsed by shortcode parser
add_filter('the_excerpt'               , 'mgm_excerpt_protection',12); // protect, execute after do_shortcode aka shortcode processor
add_filter('the_content'               , 'mgm_content_protection',12); // protect, execute after do_shortcode aka shortcode processor
// [download]
add_filter('the_excerpt'               , 'mgm_download_parse'); // download tag
add_filter('the_content'               , 'mgm_download_parse'); // download tag
add_filter('wp_list_pages_excludes'    , 'mgm_list_pages_excludes');// exclude pages from pages menu
add_filter('wp_list_pages'             , 'mgm_list_pages');//add custom menu in pages menu

// [hide categories]
add_action('pre_get_posts'             , 'mgm_hide_protected', 12);
// [nested tags parse]
if(mgm_get_class('system')->setting['enable_nested_shortcode_parsing'] == 'Y') {	
	add_filter('the_excerpt'           , 'do_shortcode',12); // nested shortcode parsing, after default do_shortcode() 11 in wp-includes/shortcodes.php
	add_filter('the_content'           , 'do_shortcode',12); // nested shortcode parsing, after default do_shortcode() 11 in wp-includes/shortcodes.php
}
// add_filter('the_content'			   , 'mgm_load_payment_jsfiles');//load payment js/css files, discarded
// [footer credits]
add_action('mgm_footer_credits'        , 'mgm_print_footer_credits');
add_action('wp_footer'                 , 'mgm_footer_credits');
add_filter('retrieve_password_title'   , 'mgm_modify_retrieve_password_emailsubject');//modify password reset email subject
add_filter('retrieve_password_message' , 'mgm_modify_retrieve_password_emailbody');//modify password reset email body
add_filter('password_reset_title'      , 'mgm_modify_lost_password_emailsubject');//modify lost password reset email subject
add_filter('password_reset_message'    , 'mgm_modify_lost_password_emailbody',10,3);//modify lost password reset email body
//add_action('mgm_filter_scripts'		   , 'mgm_filter_scripts');//to remove duplicate scripts from $wp_scripts
add_action('wp_head'		   		   , 'mgm_filter_scripts', 100);//to remove duplicate scripts from $wp_scripts
//add_action('admin_head'		   		   , 'mgm_filter_scripts', 100);//to remove duplicate scripts from $wp_scripts
add_action('admin_enqueue_scripts'	   , 'mgm_filter_scripts', 100);//to remove duplicate scripts from $wp_scripts

// feed, not implemented
// add_filter('the_content_feed'       , 'mgm_feed_protection');
// 'user_profile_edit', 
// [content shortcodes]
$content_shortcodes = array('private','private_or','private_and','user_profile','user_subscription','user_other_subscriptions',
                            'user_upgrade','user_purchase_another_membership','user_subscribe','user_register', 'user_has_access',
							'user_account_is','user_contents_by_membership','user_lostpassword','user_login','logout_link','user_field',
							'transactions','no_access','payperpost_pack','packages','membership_details','membership_contents',
							'posts_for_membership','membership_extend_link','subscription_packs');							
// add callback for all
foreach($content_shortcodes as $shortcode){
	// add callback
	add_shortcode($shortcode, 'mgm_shortcode_parse');
}

// parse
function mgm_shortcode_parse($args, $content, $tag) {	
	// current_user
	$current_user = wp_get_current_user();		
	// system
	$system = mgm_get_class('system');   
	// tag block
	switch ($tag) {
		case 'private':			
			// [private] protected content [/private]
			if (mgm_protect_content() || mgm_post_is_purchasable()) {								
				$content  = mgm_replace_content_tags($tag, $content, $args);
			}
		break;
		case 'private_or':
			// [private_or] protected content [/private_or]
			$membership = str_replace('#', '', $args[0]);
    		$content = mgm_replace_content_tags($tag, $content, $membership);
		break;
		case 'private_and':
			// [private_and] protected content [/private_and]	
			$membership = str_replace('#', '', $args[0]);
    		$content = mgm_replace_content_tags($tag, $content, $membership);
		break;	
		case 'payperpost_pack':
			// [payperpost_pack#1] : 1 = pack_id, packs to be created in MGM -> PayPerPost -> Post Packs, use the id here
			$pack_id = str_replace('#', '', $args[0]);			
            if ($pack_id) {
                $content = mgm_replace_content_tags($tag, $content, $pack_id);
            }
		break;	
		case 'subscription_packs':
			// subscription packs / payment gateways
			$content = mgm_sidebar_register_links($current_user->user_login, true, 'page');// not tested
		break;
		/******************************************************************************
		the below code is not required as user_profile will be reused
		case 'user_profile_edit':
			// user profile edit
			$content = mgm_edit_custom_field_standalone();// edit
		********************************************************************************/	
		break;			
		case 'user_subscription':
			// user subscription 
			$content = mgm_user_subscription();// view current user
		break;
		case 'user_other_subscriptions':
			//other subscriptions
			$content = mgm_other_subscriptions();
		break;	
		case 'membership_details':		
			// user subscription 
			$content = mgm_membership_details();// view current user
		break;
		case 'user_upgrade':
			// user upgrade membership
			$content = mgm_get_upgrade_buttons($args);
		break;
		case 'user_purchase_another_membership':
			//purchase another subscription
			$content = mgm_get_purchase_another_subscription_button($args);
			break;
		case 'user_subscribe':
		case 'user_register':					
			// for payment
			if(isset($_GET['method']) && preg_match('/^payment/',$_GET['method'])){
				// user payments
				$content = mgm_transactions_page($args);
			}elseif(isset($_GET['method']) && $_GET['method'] == 'lostpassword'){				
				// user lostpasswird
				$content = mgm_user_lostpassword_form(false);							
			}elseif(isset($_GET['method']) && $_GET['method'] == 'login'){				
				// user login
				$content = mgm_user_login(false);				
			}else {								
				// user subscribe and user register
				$content = mgm_user_register_form($args);				
			}		
		break;		
		case 'user_profile':
			// user profile 
			$content = mgm_user_profile_form();// view
		break;	
		case 'transactions':
			// user payments/transactions			
			$content = mgm_transactions_page($args);
		break;	
		case 'user_contents_by_membership':	
			// user contents by membership level	
			$content = mgm_membership_content_page();	
		break;
		case 'user_lostpassword':
			$content = mgm_user_lostpassword_form(false);
		break;	
		case 'user_login':
			$content = mgm_user_login(false);
		break;		
		case 'user_field':
			$content = __('Experimental');
		break;	
		case 'membership_contents':
			//membership contents 
			$content = mgm_membership_contents();// view current user
		break;	
		case 'logout_link':
			//custom logout link						
			$args[0] = str_replace('#', '', $args[0]);
			$label = implode(" ", $args);
			$content = mgm_logout_link($label);
			break;	
		case 'membership_extend_link': //INCOMPLETE
			//membership extend link
			$args[0] = str_replace('#', '', $args[0]);
			$label = implode(" ", $args);
			//$content = mgm_membership_extend_link($label);
			$content = "";
		break;			
		default:
			$args = str_replace('#', '', $args[0]);
			$content = mgm_replace_content_tags($tag, $content, $args);
		break;
	}
	// return
	return $content;
}

// replace shortcode tag
function mgm_replace_content_tags($function, $matches, $argument = false ) {
	global $user_data;
	// current_user
	$current_user = wp_get_current_user();
	// init
	$return = '';	
	// tag 	
	switch ($function) {		
		case 'private':
			// [private] protected content [/private]
			$return = mgm_replace_post($matches);
		break;					
		case 'private_or':
			// [private_or] protected content [/private_or]
			// check
			if (mgm_user_has_access() || mgm_user_has_access($argument)){
                $return = mgm_replace_post($matches);
			}
		break;				
		case 'private_and':
			// [private_and] protected content [/private_and]	
			// check
			if (mgm_user_has_access() && mgm_user_has_access($argument)){
                $return = mgm_replace_post($matches);
			}
		break;					
		case 'user_has_access':
			// [user_has_access#123] : 123 = post_id
			if (mgm_user_has_access($argument)) {
				$return = $matches;
			}
		break;		
		case 'user_account_is':
			// [user_account_is#member] :  member = membership level OR
			// [user_account_is#member|#free|#guest]
			$user_id = false;
			// member from token
			if (mgm_get_var('token') && mgm_use_rss_token()) {
				// get user
				$user    = mgm_get_user_by_token(mgm_get_var('token'));
				$user_id = $user->ID;
			}
			// user membership type
			$membership_type = mgm_get_user_membership_type($user_id,'code');
			$membership_type = strtolower($membership_type);
			// type clean, add support
			$membership_type_clean = preg_replace('/[\_]+/', '-', $membership_type);
			// | can be used to seperate multiple membership types
			$arr_arg = explode("|", $argument);
			$arr_arg = array_map('strtolower',$arr_arg);			
			// check
			if (in_array($membership_type, $arr_arg) || in_array($membership_type_clean, $arr_arg) || current_user_can('edit_posts')) {
				$return = $matches;
			}
		break;		
		case 'no_access':
			// [no_access]
			if (!mgm_user_has_access()) {
				$return = $matches;
			}
		break;
       	case 'payperpost_pack':
			// [payperpost_pack#1] : 1 = pack_id, packs to be created in MGM -> PayPerPost -> Post Packs, use the id here
            $return = mgm_parse_postpack_template($argument);
	    break; 		
       	case 'posts_for_membership':
       		if(!empty($argument)) {
	       		$arr_arg = explode("|", $argument);
				$membership_types = array_map('strtolower',$arr_arg);									
	       		$return = mgm_posts_for_membership($membership_types);
       		}
       		break;
		// no tag
		default:
			// default
			$return = mgm_replace_post($matches);
		break;
	}
	// clean 
	return stripslashes($return);
}

// post replace
function mgm_replace_post($matches) {	
	global $wpdb;
	// current_user
	$current_user = wp_get_current_user();
	// get system
	$mgm_system = mgm_get_class('system');

	// returns nothing in the event of an empty string
	if ($matches == '')  return '';
	
    // MARK FOR NESTED SHORT CODE ERROR ISS:66
	// user has access copes with validation against user level and ppp
	if (mgm_user_has_access()) { // user has access				
		$return = '<span class="mgm_private_access">' . $matches . '</span>';
	} else {
		// check if Private Tag Redirection set ( Content Control -> Access -> Private Tag Redirection Settings)
		if (mgm_check_redirect_condition($mgm_system)) { // redirect for user set 	
			// return and exit other processing
			return mgm_no_access_redirect($mgm_system);	
		}else{
		// no redirect set
			if (!mgm_post_is_purchasable()) { // user has no access and post is not purchable	
				// not logged in
				if ($current_user->ID == 0) {
					$return = mgm_private_text_tags(mgm_stripslashes_deep($mgm_system->get_template('private_text', array(), true)));
				} else {
				// logged in
					$return = mgm_stripslashes_deep($mgm_system->get_template('private_text_no_access', array(), true));
				}	
			}else{// post is purchasable
				// get button
				$return = mgm_get_post_purchase_button();
			}// end purchasable check
			
		}// end redirect condition check
		
		// wrap with css class
		$return = '<span class="mgm_private_no_access">' . $return . '</span>';		
	}// end access check
	
	// additionally filter message tags	
	return  mgm_replace_message_tags($return);	
}

// protected cats
function mgm_hide_protected($query) {
	global $post;
	// current user
	$user = wp_get_current_user();	
	
	// get system
	$mgm_system = mgm_get_class('system');
	
	// user is not a spider
	 if (!mgm_is_a_bot() /*!is_robots()*/) {
	 	// hide post	
		$hide_posts = mgm_content_exclude_by_user($user->ID, 'post');// hide post
				
		// set filter
		//if (is_array($hide_posts)) {								
		if (is_array($hide_posts) && !empty($hide_posts)) {								
			$query->set('post__not_in', array_unique($hide_posts)); // set negation			
		}		

	 	// hide cats	
		$hide_cats = mgm_content_exclude_by_user($user->ID, 'category');//hide cats
						
		// set filter					
		if (is_array($hide_cats) && !empty($hide_cats)) {				
			$run_cat_notin = true;				
			//category not found redirection									
			//skip admin and home 
			//consider only posts:	
			if(!is_super_admin() && !is_home() && is_single() ) {					
				//skip if same url:
				if(!empty($mgm_system->setting['category_access_redirect_url']) && trailingslashit(mgm_current_url()) != trailingslashit($mgm_system->setting['category_access_redirect_url'])) {										
					//check returned category ids belongs to the loaded post:
										
					if(isset($post->ID) && is_numeric($post->ID)) {
						//get post categories
						$post_cats = wp_get_post_categories($post->ID);
						foreach ($post_cats as $cat) {
							//redirect if post category exists in blocked categories
							if(in_array($cat, $hide_cats)) {
								//redirect:								
								mgm_redirect($mgm_system->setting['category_access_redirect_url']);
								exit;
							}
						}
					}
					//hold category__not_in from running:	
					//This is to get the blocked post details in next run
					$run_cat_notin = false;
				}
			}
			
			//issue#: 510					
			if($run_cat_notin) {
//				if(substr(get_bloginfo('version'), 0,3) > 3.0 && !is_page()) {
//					$query->is_singular = false;			
//				}	
//					
				$query->set('category__not_in', array_unique($hide_cats)); // set negation	
				
				//issue#: 510
				if(substr(get_bloginfo('version'), 0,3) > 3.0 && !is_page()) {
					//note: selectively attach the filter to not apply in other scenarios
					add_filter('posts_search'			   , 'mgm_attach_category_not_in');//to filter posts as per category__not_in values	
				}					
			}
		}
		
		// term check
		add_filter('list_terms_exclusions', 'mgm_exclude_terms'); // terms		
	 } // endif
	
	 // return 
	 return $query;
}
/**
 * Filter to exclude the posts belong to category__not_in categories
 * Fix for the issue: (category__not_in doesn't seem to filter posts in MGM context)
 *
 * @param string $search
 * @return unknown
 */
function mgm_attach_category_not_in($search) {
	global $wpdb, $wp_query;
	if(isset($wp_query->query_vars['category__not_in']) && !empty($wp_query->query_vars['category__not_in'])) {				
		$where = sprintf(' AND ( %s.ID NOT IN (
									SELECT object_id
									FROM %s 
									WHERE term_taxonomy_id IN (%s)
							   ) ) ', $wpdb->posts, $wpdb->term_relationships, implode(',', $wp_query->query_vars['category__not_in'])) ;
		$search .= $where;
	}
	
	return $search;
}

// exclude user membership type
function mgm_content_exclude_by_user($user_id = 0, $content_type='category') {
	// not for admin
	if(is_super_admin()) return;
	
	global $wpdb;
	// get member
	$mgm_member      = mgm_get_member($user_id);
	$user = wp_get_current_user();	
	$temp_member = new stdClass();
	$extended_protection = mgm_get_class('system')->setting['content_hide_by_membership'];
	$membership_type = $mgm_member->membership_type;
	// set default
	$membership_type = (empty($membership_type)) ? 'guest' : $membership_type;
	//get user membership types: multiple level membership issue#: 400 modification
	$arr_mt = mgm_get_subscribed_membershiptypes($user_id, $mgm_member);
	if(!in_array($membership_type, $arr_mt))
		$arr_mt[] = $membership_type;
	
	// on type
	switch($content_type){
		case 'category':
			// category
			if (!$hide_cats = wp_cache_get('category_exclusion_' . $user_id, 'users')) {				
				// exclude protected categories 
				$hide_cats = array();
				// get post category settings
				$mgm_post_category = mgm_get_class('post_category');					
				// loop set				
				foreach($mgm_post_category->get_access_membership_types() as $category_id=>$membership_types ) {
					// exclude
					if($membership_types){ // not set public access
						//if (in_array($membership_type, $membership_types)) {
						//	continue;
						//}
						// multiple level membership issue#: 400 modification
						if(array_diff($membership_types, $arr_mt) != $membership_types)
							continue;
						// hide
						$hide_cats[] = $category_id;
					}			
				}
				// set cache
				wp_cache_set('category_exclusion_' . $user_id, $hide_cats, 'users');
			}
			// return
			return $hide_cats; 
			// end check
		break;
		case 'post':
			// post
			// no check if not required
			if(mgm_get_class('system')->setting['content_hide_by_membership'] == 'N'){
				return array();
			}			
			// check
			if (!$hide_posts = wp_cache_get('post_exclusion_' . $user_id, 'users')) {				
				// exclude protected posts 
				$hide_posts = array();
				// fetch all posts
				$posts = $wpdb->get_results("SELECT ID FROM `$wpdb->posts` WHERE `post_type` NOT IN('revision','attachment')");
				// check
				if($posts){
					// loop
					foreach($posts as $post){
						// get post
						$mgm_post = mgm_get_post($post->ID);
						$access_delay    = $mgm_post->access_delay;
						// check types
						if(is_array($mgm_post->access_membership_types) && count($mgm_post->access_membership_types)){
							// default
							$access = false;
							// check							
							foreach($mgm_post->access_membership_types as $a_membership_type){
								// match								
								//if($membership_type == $a_membership_type){
								// multiple level membership issue#: 400 modification
								if(in_array($a_membership_type, $arr_mt)) {
									// done
									$access = true; 
									//break;
									if($extended_protection == 'Y') {
										$temp_member->membership_type = $a_membership_type;
										//deny access if delay: issue#: 516
										if(mgm_check_post_access_delay($temp_member, $user, $access_delay)){
											//OK: 
											break;	
										}else {											
											$access = false; 		
										}
									}									
								}									
							}
							
							// protect
							if(!$access){
								$hide_posts[] = $post->ID;
							}												
						}
						// unset
						unset($mgm_post);
					}
				}				
				// set cache
				wp_cache_set('post_exclusion_' . $user_id, $hide_posts, 'users');
			}
			// return
			return $hide_posts; 
			// end check
		break;
	}		
	// empty
	return array();	
}

// exclude terms, cates, tags
function mgm_exclude_terms($exclusions = null) {
	global $user_ID;
	get_currentuserinfo();

	$hide_cats = mgm_content_exclude_by_user($user_ID, 'category');
	if (!empty($hide_cats )) {
		foreach((array) $hide_cats as $term_id ) {
			$exclusions .= 'AND t.term_id <> ' . intval($term_id) . ' ';
		}
	}
	// return
	return $exclusions;
}

// hide pages from list/menu, called by template tag wp_list_pages()
function mgm_list_pages_excludes($excluded) {
	global $wpdb, $user_ID;
	get_currentuserinfo();

	// get system object	
	$system = mgm_get_class('system');
	// update			
	$excluded_pages= $system->setting['excluded_pages'];
	// exclude
	if (is_array($excluded_pages) && is_array($excluded)) {
		$excluded = array_merge($excluded, $excluded_pages);// give preference to user settings
	}	
		
	// hide post
	$hide_posts = mgm_content_exclude_by_user($user_ID, 'post');
	// check
	if($hide_posts && is_array($excluded)){
		$excluded = array_merge($excluded, $hide_posts);// give preference to mgm settings
	}
	
	// default pages, by session
	$hide_pages = mgm_exclude_default_pages();
	// check
	if($hide_pages && is_array($excluded)){
		$excluded = array_merge($excluded, $hide_pages);// give preference to mgm settings
	}
	
	// return array
	return $excluded;
}
// add items to pages menu, called by template tag wp_list_pages()
function mgm_list_pages($output){	
	// logout url
	$logout_url = wp_logout_url();
	// title
	$title = __('Logout','mgm');	
	// append logout
	if ( is_user_logged_in() ) {
		// add
		$output .= sprintf('<li class="page_item"><a href="%s" title="%s">%s</a></li>',$logout_url,$title,$title);
	}
	// return output
	return $output;
}
// parse pack
function mgm_parse_postpack_template($postpack_id) {	
	// get currebt user
	get_currentuserinfo();	
	global $current_user,$post;	
	
	// system
	$mgm_system = mgm_get_class('system'); 
	
	// not logged in
	if(!$current_user->ID){
		// template
		$template = mgm_get_template('private_text_template', array(), 'templates');
		// message 
		$message = 'You need to be logged in to purchase the pack.';
		// login
		$message .= (is_object($post)) ? sprintf(__(' Please <a href="%s"><b>login</b> here.</a>','mgm'),site_url('wp-login.php?redirect_to='.get_permalink($post->ID))) : '';
		// message
		$message = str_replace('[message]', $message, $template);
		// return 
		return sprintf('<span class="mgm_private_no_access">%s</span>',$message);		
	}
	
	// currency   
    $currency      = $mgm_system->setting['currency'];
    $pack_template = $mgm_system->get_template('ppp_pack_template');
	// get pack
	$postpack      = mgm_get_postpack($postpack_id);
	// default
    if (!$pack_template) {
        $pack_template = '<div><div><h3>[pack_name] - [pack_cost] [pack_currency]</h3></div><div>[pack_description]</div><div>[pack_posts]</div><div>[pack_buy_button]</div></div>';
    }
    
	// post
    $post_string = '';
    $cost = mgm_convert_to_currency($postpack->cost);
	$show_button = false;// if all posts purchased, dont show button
	    
	// template
    $template = str_replace('[pack_name]', $postpack->name, $pack_template);
    $template = str_replace('[pack_cost]', $cost, $template);
    $template = str_replace('[pack_description]', $postpack->description, $template);
    $template = str_replace('[pack_currency]', $currency, $template);
    
	// list of posts
    if ($pack_posts = mgm_get_postpack_posts($postpack_id)) {
		// init string
        $post_string = '<ul>';
		// loop
        foreach ($pack_posts as $i=>$pack_post) {
			// check if user has access
			$access = mgm_user_has_purchased_post($pack_post->post_id,$current_user->ID);
			// set button mode
			if(!$access){
				// enable button
				$show_button = true;
			}
			// get obj
            $post_obj     = get_post($pack_post->post_id);
            $post_string .= '<li>' . sprintf('<a href="%s">%s</a>',get_permalink($post_obj->ID),$post_obj->post_title) . '</li>';
        }  
		// end
        $post_string .= '</ul>';
    }
    // display
    $template = str_replace('[pack_posts]', $post_string, $template);		
	// get button
	if($show_button){
		$buy_button = mgm_get_postpack_purchase_button($postpack_id, $pack_posts);
	}else{
		$buy_button = '';
	}
	// template
    return str_replace('[pack_buy_button]', $buy_button, $template);   
}	

// mgm_filter_excerpt
function mgm_filter_excerpt($content){
	// remove tags
	$content = $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content);
	// return 
	return $content;
}

// mgm_excerpt_protection
function mgm_excerpt_protection($content){		
	// check protection
	return mgm_content_protection_check($content,'excerpt');	
}

// mgm_content_protection
function mgm_content_protection($content){	
	// check protection
	return mgm_content_protection_check($content,'content');
}

// check protection
function mgm_content_protection_check($content, $type='excerpt'){
	global $wpdb,$post;
	
	// get user
	$user = wp_get_current_user();	
	// to disable showing multiple private messages on home page listing(issue#: 384) , disabled due to #429
	// $show_message = true; // (is_home() && $type == 'content')  ? false : true;
	// filter first
	$content = mgm_replace_message_tags($content);
	// filter payment messages
	$content = mgm_replace_payment_message_tags($content);
	
	// no check for admin or if user has access or user logged in
	if(is_super_admin() || mgm_user_has_access() /*|| $user->ID*/)
		return $content;
	
	// for full / or part protection, honor manual private tag setting via post interface or , mgm settings
	$mgm_system = mgm_get_class('system');
	$level      = $mgm_system->setting['content_protection'];
	
	// no check for post/page set as no access redirect, custom register/login urls 		
	if($post->ID){
		// get permalink
		$permalink = get_permalink( $post->ID );
		// no_access_urls
		$no_access_urls = array('no_access_redirect_loggedin_users','no_access_redirect_loggedout_users');
		// init
		$return = false;
		// loop
		foreach($no_access_urls as $no_access_url){
			// get setting
			$no_access_url_is = $mgm_system->setting[$no_access_url];
			// match
			if(!empty($no_access_url_is) && $permalink == trailingslashit($no_access_url_is)){
				// set flag
				$return = true; break;
			}
		}		
		// return 
		if($return)	return $content;
		
		// check urls
		$custom_pages_url = $mgm_system->get_custom_pages_url(); 
		// check
		foreach($custom_pages_url as $key=>$page_url){
			// match
			if(!empty($page_url) && $permalink == trailingslashit($page_url)){
				// set flag
				$return = true; break;
			}			
		}
		// return 
		if($return)	return $content;
		
		// get post object
		$mgm_post = mgm_get_post($post->ID);				
	}		
	
	// message code	
	if($user->ID){// logged in user
		$message_code = mgm_post_is_purchasable() ? 'private_text_purchasable' : 'private_text_no_access';
	}else{// logged out user
		$message_code = mgm_post_is_purchasable() ? 'private_text_purchasable_login' : 'private_text';
	}
	// protected_message	
	$protected_message = sprintf('<span class="mgm_private_no_access">%s</span>',mgm_private_text_tags(mgm_stripslashes_deep($mgm_system->get_template($message_code, array(), true))));			
	// filter message
	$protected_message = mgm_replace_message_tags($protected_message);
	
	// check
	switch($level){
		case 'full': // full protection	
			// check redirect condition
			//Skip content protection if manually protected:(To honour private tags)
			//Double check as the next line was removed before
			if(!mgm_is_manually_protected($content)) {
				if (!mgm_check_redirect_condition($mgm_system)) {
					$content = $protected_message;
				}else{
				// default
					$content = mgm_no_access_redirect($mgm_system);
				}
			}					
		break;
		case 'partly':// partly protection		
			//Skip content protection if manually protected:(To honour private tags)
			//Double check as the next line was removed before		
			if(!mgm_is_manually_protected($content)) {				
				// how many words to allow
				$public_content_words = intval($mgm_system->setting['public_content_words']); 									
				// apply if only more than 0				
				if($public_content_words>0) {// #125 iss / issue#: 510
					// check redirect condition
					if (!mgm_check_redirect_condition($mgm_system)) {	// redirect if set
						// on type
						switch($type){
							case 'excerpt':							
								// default
								//old code
								//$content = mgm_words_from_content($content, $public_content_words) ;
								// add protect
								//if($public_content_words < 50) $content.= $protected_message;
								
								if(preg_match('#<span class="mgm_private_no_access">(.*?)<\/span\>#s', $content,$match)) {									
									$prev_message = $match[0];
									$content = preg_replace('#<span class="mgm_private_no_access">(.*?)<\/span\>#s','' ,$content);								
									$content = strip_tags($content);
									$content = mgm_words_from_content($content, $public_content_words);
									if($public_content_words < 50)
										$content .= $prev_message;
								}else {
									$content = strip_tags($content);
									$content = mgm_words_from_content($content, $public_content_words);
									if($public_content_words < 50) 
										$content.= $protected_message;		
								}
							break;
							case 'content':							
								//old code												
								//$content = mgm_words_from_content($content, $public_content_words) . $protected_message;
								//issue #: 450
								if(preg_match('#<span class="mgm_private_no_access">(.*?)<\/span\>#s', $content,$match)) {																		
									$prev_message = $match[0];
									$content = preg_replace('#<span class="mgm_private_no_access">(.*?)<\/span\>#s','' ,$content);								
									$content = strip_tags($content);
									$content = mgm_words_from_content($content, $public_content_words);
									$content .= $prev_message;
								}else {								
									$content = strip_tags($content);
									$content = mgm_words_from_content($content, $public_content_words) . $protected_message;		
								}
							break;					
						}
					}else{
					// default
						$content = mgm_no_access_redirect($mgm_system);
					}	
				}
			}
		break;
		case 'none':// no protection, trim all private tags, honor [private] tags			
			// just check purchasable, other wise trim			
			if(!mgm_post_is_purchasable()){							
				// remove tags 
				$content = str_replace(array('[private]','[/private]','[private_or]','[/private_or]','[private_and]','[/private_and]'),'',$content);
			}			
		break;	
		default:	
			// disable protection		
			$content = str_replace(array('[private]','[/private]','[private_or]','[/private_or]','[private_and]','[/private_and]'),'',$content);
		break;		
	}	
	
	// if purchasable and not manually protected
	/*if(mgm_post_is_purchasable() && !mgm_is_manually_protected($content) ){			
		// get button
		$return = mgm_get_post_purchase_button($post->ID, false);
		// wrap with css class
		$content .= '<span class="mgm_private_no_access">' . $return . '</span>';
	}*/
	//issue#: 450
	if(mgm_post_is_purchasable() && !mgm_is_buynow_form_included($content)) {		
		// get button
		$return = mgm_get_post_purchase_button($post->ID, (mgm_is_manually_protected($content)? false: true));
		// wrap with css class
		$content .= '<span class="mgm_private_no_access">' . $return . '</span>';
	}
			
	// return	
	return $content;
}

// check already protected
function mgm_is_manually_protected($content){
	// init	
	$is_protected = false;	
	// check
	if(preg_match("/\[private\](.*)\[\/private\]/", $content)){
		$is_protected = true;
	}elseif(preg_match("/\[private_or\](.*)\[\/private_or\]/", $content)){
		$is_protected = true;
	}elseif(preg_match("/\[private_and\](.*)\[\/private_and\]/", $content)){
		$is_protected = true;
	}elseif(preg_match('#<span class="mgm_private_no_access">(.*?)<\/span\>#s', $content)){		
		$is_protected = true;
	}
	// return
	return $is_protected;	
}
//check buy now form already included
function mgm_is_buynow_form_included($content) {
	return preg_match('/id="mgm_buypost_form"/', $content);
}

// payment messages
function mgm_replace_payment_message_tags($content){
	// system
	//$mgm_system = get_option('mgm_system');
	$mgm_system = mgm_get_class('system');
		
	// current module
	$module   = mgm_request_var('module');	
	// object 
	$module_object = NULL;	
	
	// check 
	if($module){
		// module object
		$module_object = mgm_get_module($module, 'payment');
	}
	
	// double check
	if(	is_object($module_object) ){
		// status and message
		if (!isset($_GET['status']) || $_GET['status'] == 'success') {	
			$payment_status_title   = ($module_object->setting['success_title'] ? $module_object->setting['success_title'] : $mgm_system->get_template('payment_success_title', array(), true));
			$payment_status_message = ($module_object->setting['success_message'] ? $module_object->setting['success_message'] : $mgm_system->get_template('payment_success_message', array(), true));
		} else if (!isset($_GET['status']) || $_GET['status'] == 'cancel') {	
			$payment_status_title   = __('Transaction cancelled','mgm');
			$payment_status_message = __('You have cancelled the transaction.','mgm');
		} else {	
			$payment_status_title   = ($module_object->setting['failed_title'] ? $module_object->setting['failed_title'] : $mgm_system->get_template('payment_failed_title', array(), true));
			$payment_status_message = ($module_object->setting['failed_message'] ? $module_object->setting['failed_message'] : $mgm_system->get_template('payment_failed_message', array(), true));
		}
		
		// set errors 
		if (isset($_GET['errors'])) {
			$errors = explode('|', $_GET['errors']);
			$payment_status_message .= '<p><h3>' . __('Messages', 'mgm') . '</h3>';
			$payment_status_message .= '<div><ul>';
			foreach ($errors as $error) {
				$payment_status_message .= '<li>' . $error . '</li>';
			}
			$payment_status_message .= '</ul>
			</div></p>';
		}		
		// redirect_to post
		if(isset($_GET['post_redirect'])){
			$payment_status_message .= __('<b>You will be redirected to the Post Purchased, please click <a href="'.$_GET['post_redirect'].'"> here </a> if you are not redirected.</b>','mgm');
			$payment_status_message .="<script language=\"Javascript\">var t = setTimeout ( \"window.location='".$_GET['post_redirect']."'\", 5000 ); </script>";
		}
		// loop tags
		foreach(array('payment_status_title','payment_status_message') as $tag){
			// set
			$content = str_replace('[['.$tag.']]',mgm_stripslashes_deep(${$tag}),$content);
		}		
	}else{
		// loop tags and clean tags
		foreach(array('payment_status_title','payment_status_message') as $tag){
			// set
			$content = str_replace('[['.$tag.']]','',$content);
		}	
	}	
	// return
	return $content;
}
// footer credits
function mgm_footer_credits(){		
	// affiliate id
	if($affid = get_option('mgm_affiliate_id')){
		// mgm url
		$affiliate_url = 'http://www.magicmembers.com/?affid='.$affid;
		
		// content
		$content ='<div class="mgm_aff_footer"><div class="mgm_aff_link">[powered_by]</div><div class="mgm_aff_clearfix"></div></div>';
				  
		// apply filter		  
		$content = apply_filters('mgm_powered_by', $content);
		
		// place link
		$content = str_replace('[powered_by]',sprintf(__('Powered by Magic Members <a href="%s" target="_blank">Membership Software</a>','mgm'),$affiliate_url), $content);
											
		// print		  
		echo $content;
	}
}
// print 
function mgm_print_footer_credits(){
	// remove action 
	remove_action('wp_footer','mgm_footer_credits');
	// print
	mgm_footer_credits();
}
// loads payment related js/css files
function mgm_load_payment_jsfiles($content) {		
	$id = get_the_ID();		
	if(is_numeric($id)) {
		$mgm_post = mgm_get_post($id);				
		if(isset($mgm_post->purchasable) && $mgm_post->purchasable == 'Y' && !wp_script_is('jquery.metadata')) {						
			$incfiles = '';
			$css_files	= array();
			$css_files[] = MGM_ASSETS_URL . 'css/mgm_cc_fields.css';		
			$css_link_format = '<link rel="stylesheet" href="%s" type="text/css" media="all" />';
			// add
			foreach($css_files as $css_file){
				$incfiles .= sprintf($css_link_format, $css_file)."\n";
			}	
			$js_files = array();
			// jquery from wp distribution
			//$js_files[] = includes_url( '/js/jquery/jquery.js');
			// custom
			if(!wp_script_is('mgm-jquery-validate'))
				if(!mgm_is_script_already_included('jquery.validate.pack.js')) {
					$js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.validate.pack.js';
					$mgm_scripts[] = 'jquery.validate.pack.js';
				}
			wp_enqueue_script('jquery.metadata', MGM_ASSETS_URL . 'js/jquery/jquery.metadata.js');
			if(!wp_script_is('mgm-jquery-metadata'))	
				if(!mgm_is_script_already_included('jquery.metadata.js')) {
					$js_files[] = MGM_ASSETS_URL . 'js/jquery/jquery.metadata.js';
					$mgm_scripts[] = 'jquery.metadata.js';
				}
			if(!wp_script_is('mgm-helpers'))
				$js_files[] = MGM_ASSETS_URL . 'js/helpers.js';
			$js_script_format = '<script type="text/javascript" src="%s"></script>';
			if($js_files)
				foreach($js_files as $js_file){
					$incfiles .= sprintf($js_script_format, $js_file)."\n";
				}
			$content .= $incfiles;
			unset($js_files);
			unset($css_files);
			unset($incfiles);
		}
	}
	return $content;	
}
// update password reset email body
function mgm_modify_retrieve_password_emailbody($message) {		
	global $wpdb, $current_site;	
	$system = mgm_get_class('system');
	$blogname = is_multisite() ? $current_site->site_name : wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by_email(trim($_POST['user_login']));	
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_userdatabylogin($login);
	}
	$user_login = $user_data->user_login;	
	//just fetch the key from db as it is already updated
	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
	
	//$passwordlink = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
	$passwordlink =  mgm_get_custom_url('login', false, array('action' => 'rp', 'key' => $key, 'login' => rawurlencode($user_login) ));
	// subject
	$body = $system->get_template('retrieve_password_email_template_body', array('blogname'=>$blogname,
																				'siteurl' => network_site_url(),
																				'username' => $user_login,
																				'passwordlink' => $passwordlink 
																				),true);
	return $body;
}
// update password reset email subject
function mgm_modify_retrieve_password_emailsubject() {
	global $current_site;
	$system = mgm_get_class('system');
	$blogname = is_multisite() ? $current_site->site_name : wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
	// subject
	$subject = $system->get_template('retrieve_password_email_template_subject', array('blogname'=>$blogname), true);
	return $subject;
}
// update lost password reset email body
function mgm_modify_lost_password_emailbody($body, $new_password) {		
	global $current_site;	
	$system = mgm_get_class('system');
	$username = $_GET['login']; 
	$blogname = is_multisite() ? $current_site->site_name : wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);	
	// subject
	$body = $system->get_template('lost_password_email_template_body', array(	'blogname'=> $blogname,
																				'loginurl' => (mgm_get_custom_url('login')),
																				'username' => $username,
																				'password' => $new_password 
																				),true);
	if(empty($body)) {
		$body  = sprintf(__('Username: %s'), $username) . "\r\n";
		$body .= sprintf(__('Password: %s'), $new_password) . "\r\n";
		$body .= (mgm_get_custom_url('login')) . "\r\n";
	
		if ( is_multisite() )
			$blogname = $current_site->site_name;
		else
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	}
	
	return $body;
}
// update lost password reset email subject
function mgm_modify_lost_password_emailsubject() {
	global $current_site;
	$system = mgm_get_class('system');
	$blogname = is_multisite() ? $current_site->site_name : wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
	// subject
	$subject = $system->get_template('lost_password_email_template_subject', array('blogname'=>$blogname), true);
	if(empty($subject)) {
		$subject = sprintf( __('[%s] Your new password','mgm'), $blogname );
	}
	return $subject;
}
// pack purschase button 
function mgm_get_postpack_purchase_button($postpack_id=NULL){
	global $post;		
	// get current post id, the page where post pack were listed, used to redirect		
	$postpack_post_id = get_the_ID();
	
	// system
	$mgm_system = mgm_get_class('system'); 
	// get payment modules
	$a_payment_modules = $mgm_system->get_active_modules('payment');
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
	
	// check
	if (count($payment_modules)>0) {	
		// in transactions url set
		if(isset($mgm_system->setting['transactions_url']) && !empty($mgm_system->setting['transactions_url'])){		
			// base url
			$baseurl = $mgm_system->setting['transactions_url'];		
		}else{
			// base url
			$baseurl = mgm_home_url('transactions');
		}
		// post url
		$post_payment_url = add_query_arg(array('method'=>'payment_purchase'), $baseurl);
		// coupon_fields
		$coupon_fields = mgm_get_partial_fields(array('on_postpurchase'=>true),'mgm_postpurchase_field');	
		// button 
		$button_code = '<input type="submit" name="btnsubmit" class="button" value="'.__('Buy Now','mgm').'">';
		// filter
		$button_code = apply_filters('post_purchase_button_html', $button_code);
		// button
		$button = '<div style="width:100%;">
					 <form name="mgm_buypostpack_form" id="mgm_buypostpack_form" class="mgm_form" method="post" action="'.$post_payment_url.'">
						' . $coupon_fields . '
						' . $button_code . '
						<input type="hidden" name="postpack_id" value="'.$postpack_id.'">
						<input type="hidden" name="postpack_post_id" value="'.$postpack_post_id.'">
					 </form>
				   </div>  ';		
		
	}else{
		$button = '<div style="margin-bottom:5px;width:100%;color:red;font-weight:bold">'. __('No Payment Gateway available.', 'mgm').'</div>';
	}
	
	// return 
	return $button;
}
// return buy button
function mgm_get_post_purchase_button($post_id=NULL,$show_message=true){
	// current user
	$current_user = wp_get_current_user();
	// get system
	$mgm_system = mgm_get_class('system');
	// get current post id		
	if(!$post_id) $post_id = get_the_ID();
	
	// echo 'post_id:'.$post_id;
	// if user logged in
	if ($current_user->ID > 0) {
		// get active payment modules
		$a_payment_modules = $mgm_system->get_active_modules('payment');
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
		// def return
		$return = (!$show_message) ? '' : '<div style="margin-bottom:5px;width:100%;">' . mgm_stripslashes_deep($mgm_system->get_template('private_text_purchasable', array(), true)) . '</div>';		
		// some module active
		if (count($payment_modules)>0) {
			// in transactions url set
			if(isset($mgm_system->setting['transactions_url']) && !empty($mgm_system->setting['transactions_url'])){		
				// base url
				$baseurl = $mgm_system->setting['transactions_url'];		
			}else{
				// base url
				$baseurl = mgm_home_url('transactions'); 
			}			
			// post url
			$post_payment_url = add_query_arg(array('method'=>'payment_purchase'), $baseurl);
			// coupon_fields
			$coupon_fields = mgm_get_partial_fields(array('on_postpurchase'=>true),'mgm_postpurchase_field');			
			// button 
			$button_code = '<input type="submit" name="btnsubmit" class="button" value="'.__('Buy Now','mgm').'">';
			// filter
			$button_code = apply_filters('post_purchase_button_html', $button_code);
			// button
			$button = '<div style="width:100%;">
						<form name="mgm_buypost_form" id="mgm_buypost_form" class="mgm_form" method="post" action="'.$post_payment_url.'">
							' . $coupon_fields . '
							' . $button_code . '
							<input type="hidden" name="post_id" value="'.$post_id.'">
					    </form>
					  </div>  ';		
			// return 
			$return .= $button;
		}else{
			$return .= '<div style="margin-bottom:5px;width:100%; color:red; font-weight:bold">' . __('No Payment Gateway available.', 'mgm') . '</div>';;
		}							
	} else {
		// only when show message
		$return = (!$show_message) ? '' : '<div style="margin-bottom:5px;width:100%;">' . mgm_private_text_tags(mgm_stripslashes_deep($mgm_system->get_template('private_text_purchasable_login', array(), true))) . '</div>';			
	}
	
	// return 
	return $return;
}
//user profile page:
function mgm_user_profile_form() {
	global $wpdb;
	// get mgm_system
	$mgm_system = mgm_get_class('system');
	
	// current user
	$current_user = wp_get_current_user();
	
	// current or voew
	if($current_user->ID){
		// current
		$user = mgm_get_userdata($current_user->ID);
	}else{
		// get
		if(isset($_GET['username'])){
			$user = get_userdatabylogin($_GET['username']);			
		}
	}
	
	// if no user
	if(!$user->ID || is_super_admin($user->ID)){
		return mgm_login_form(); exit;
	}
	
	// mgm member
	$mgm_member = mgm_get_member($user->ID);
	
	// edit mode, on for current user
	$edit_mode = ($current_user->ID == $user->ID) ? true : false;	
	
	// form action	
	$form_action = get_permalink();	
	// reset	
	if($form_action == null) {
		$form_action = mgm_get_url(); 
		$form_action = str_replace(array('&updated=true', '?updated=true'),'', $form_action);
	}
	
	// get default fields
	$profile_fields = mgm_get_config('default_profile_fields', array());
	// get active custom fields on profile page
	$cf_profile_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_profile'=>true)));
	
	$cf_noton_profile = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_profile'=> false)));
	// error_html
	$error_html = '<link rel="stylesheet" href="'. MGM_ASSETS_URL . 'css/mgm_messages.css' .'" type="text/css" media="all" />';
	// update
	if($edit_mode){
		// updated
		if ( isset($_POST['method']) && $_POST['method'] == 'update_user' ) {
			
			// user lib
			require_once( ABSPATH . WPINC . '/registration.php');
			// callback
			do_action('personal_options_update', $current_user->ID);	
			// not multisite, duplicate email allowed ?	
			if ( !is_multisite() ) {
				// save
				$errors = mgm_user_profile_update($current_user->ID);
			}else {
			// multi site
				// get user
				$user = get_userdata( $current_user->ID );
				// update here:
				// Update the email address, if present. duplicate check
				if ( $user->user_login && isset( $_POST[ 'user_email' ] ) && is_email( $_POST[ 'user_email' ] ) && $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $user->user_login ) ) )
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $_POST[ 'user_email' ], $user->user_login ) );
				
				// edit 
				if ( !isset( $errors ) || ( isset( $errors ) && is_object( $errors ) && false == $errors->get_error_codes() ) )
					$errors = mgm_user_profile_update($current_user->ID);
			}
			// trap erros
			if ( !is_wp_error( $errors ) ) {
				// redirect							
				mgm_redirect(add_query_arg( array('updated' => 'true'), $form_action));				
			}	
			// errors
			if(isset($errors) && !is_numeric($errors)) {
				/* Not working on some servers: issue 381
				ob_start();
				mgm_set_errors($errors);
				$error_html .= ob_get_clean();
				*/
				$error_html .= mgm_set_errors($errors, true);
			}	
		}
	}	
	
	// updated
	if ($edit_mode && isset($_GET['updated']) ){
		$error_html  .= '<div class="mgm_message_success">';
		$message     = apply_filters('mgm_profile_edit_message', __('User updated.', 'mgm'));
		$error_html .= '<p><strong>'.$message.'</strong></p></div>';
	}
	
	// 	get row row template
	$form_row_template = $mgm_system->get_template('profile_form_row_template');
	
	// get template row filter, mgm_profile_form_row_template for edit, mgm_profile_form_row_template_view for public view
	$form_row_template = apply_filters('mgm_profile_form_row_template'.(!$edit_mode ? '_view': ''), $form_row_template);		
	
	// auto generate form template
	// form_template
	$form_template = '';
	// captured 
	$fields_captured = array();
	// get field_groups
	$field_groups = mgm_get_config('profile_field_groups', array());
	
	// loop groups
	foreach($field_groups as $group=>$group_fields){
		if($group == 'Photo') {
			$photo_exists = false;
			foreach($cf_profile_page as $photo){				
				if($photo['name'] == 'photo') {
					$photo_exists = true;
					break;
				}
			}
			if(!$photo_exists) continue;
		}
		// group
		$form_template .= sprintf('<h3>%s</h3>',$group);
		// loop to create form template
		foreach($group_fields as $group_field){
			// skip password
			//if(!$edit_mode && $group_field == 'password') continue;		
			if(!$edit_mode && in_array($group_field, array('password','password_conf'))) continue;		
			// set not found
			$captured = false;
			// first check if in custom fields
			foreach($cf_profile_page as $field){
				// skip password in non edit mode				
				if($field['name'] == $group_field){
					// set found
					$captured = true;
					// skip password
					//if(!$edit_mode && $field['name'] == 'password') continue;	
					if(!$edit_mode && in_array($field['name'],array('password','password_conf'))) continue;	
					// store for no repeat
					$fields_captured[] = $field['name'];
					// field wrapper
					$wrapper_ph = sprintf('[user_field_wrapper_%s]',$field['name']);	
					// field label
					$label_ph = sprintf('[user_field_label_%s]',$field['name']);		
					// field/html element
					$element_ph = sprintf('[user_field_element_%s]',$field['name']);
					// set element name
					$form_template .= str_replace(array('[user_field_wrapper]','[user_field_label]','[user_field_element]'),array($wrapper_ph,$label_ph,$element_ph),$form_row_template);
					// break;
					break;
				}
			}
			// if not captured
			if(!$captured){	
				
				$continue = false;
				foreach($cf_noton_profile as $cffield){			
					if($cffield['name'] == $group_field) {
						$continue = true;				
						break;
					}
				}
				if($continue) continue;
							
				// field wrapper
				$wrapper_ph = sprintf('[user_field_wrapper_%s]',$profile_fields[$group_field]['name']);						
				// field label
				$label_ph = sprintf('[user_field_label_%s]',$profile_fields[$group_field]['name']);		
				// field/html element
				$element_ph = sprintf('[user_field_element_%s]',$profile_fields[$group_field]['name']);
				// set element name
				$form_template .= str_replace(array('[user_field_wrapper]','[user_field_label]','[user_field_element]'),array($wrapper_ph,$label_ph,$element_ph),$form_row_template);
			}
		}
	}
	
	// other
	$other_header = false;
	// loop to create form template
	foreach($cf_profile_page as $field){
		// skip password in non edit mode
		//if(!$edit_mode && $field['name'] == 'password') continue;		
		if(!$edit_mode && in_array($field['name'],array('password','password_conf'))) continue;		
		// skip captured
		if(in_array($field['name'],$fields_captured)) continue;		
		// header
		if(!$other_header){
			// rest
			$form_template .= sprintf('<h3>%s</h3>',__('Others','mgm'));
			$other_header = true;
		}
		// field wrapper
		$wrapper_ph = sprintf('[user_field_wrapper_%s]',$field['name']);
		// field label
		$label_ph = sprintf('[user_field_label_%s]',$field['name']);		
		// field/html element
		$element_ph = sprintf('[user_field_element_%s]',$field['name']);
		// set element name
		$form_template .= str_replace(array('[user_field_wrapper]','[user_field_label]','[user_field_element]'),array($wrapper_ph,$label_ph,$element_ph),$form_row_template);		
	}
	
	// get template filter, mgm_profile_form_template for edit, mgm_profile_form_template_view for public view
	$form_template = apply_filters('mgm_profile_form_template'.(!$edit_mode ? '_view': ''), $form_template);
	
	// now replace and create the fields
	$form_html = $form_template;
	
	// get mgm_form_fields generator
	$form_fields = &new mgm_form_fields(array('wordpres_form'=>false));
	
	$arr_images = array();
	// loop custom fields to replace form labels/elements
	foreach($cf_profile_page as $field){
		
		// skip password in non edit mode
		//if(!$edit_mode && $field['name'] == 'password') continue;	
		if(!$edit_mode && in_array($field['name'],array('password','password_conf'))) continue;	
		
		if($edit_mode && $field['type'] == 'image')
			if(!in_array($field['name'], $arr_images ))	
				$arr_images[] = $field['name'];
		
		// field wrapper
		$wrapper_ph = sprintf('[user_field_wrapper_%s]',$field['name']);		
		// field label
		$label_ph = sprintf('[user_field_label_%s]', $field['name']);
		// field/html element
		$element_ph = sprintf('[user_field_element_%s]',$field['name']);
		
		// edit mode
		if($edit_mode){	
			// for username 
			if($field['name'] =='username'){
				$field['label'] = sprintf('%s (<em>%s</em>)',mgm_stripslashes_deep($field['label']),__('Username not changable','mgm'));
			}elseif($field['name'] =='password'){
				$field['label'] = sprintf('%s (<em>%s</em>)',mgm_stripslashes_deep($field['label']),__('Leave blank if don\'t wish to update','mgm'));
			}
		}else{
			// for display_name 
			if($field['name'] == 'display_name'){
				$field['label'] = __('Display Name','mgm');
			}
		}	
		
		// replace wrapper
		$form_html = str_replace($wrapper_ph, $field['name'].'_box', $form_html);
		
		// replace label
		$form_html = str_replace($label_ph, mgm_stripslashes_deep($field['label']), $form_html);
		
		// selected value
		if(isset($user->$profile_fields[$field['name']]['name'])){ // wp alias
			$value = $user->$profile_fields[$field['name']]['name'];
		}else if(isset($mgm_member->custom_fields->$field['name'])){// custom field
			$value = $mgm_member->custom_fields->$field['name'];
		}else if(isset($user->$field['name'])){// object var	
			$value = $user->$field['name'];
		}else{// none
			$value = '';
		}	
		
		// dont set value for password
		//if($field['name'] == 'password') $value = '';
		if(in_array($field['name'],array('password','password_conf'))) $value = '';
		
		// disable username
		if($field['name'] == 'username') $field['attributes']['readonly'] = true;
		
		// nickname
		if($field['name'] == 'nickname') $field['attributes']['required'] = true;
		
		// edit mode
		if($edit_mode){
		// replace element
			$form_html = str_replace($element_ph,$form_fields->get_field_element($field,'mgm_profile_field',$value),$form_html);
		}else{
		// view		
			// country
			if($field['name'] == 'country') {
				$value = mgm_country_from_code($value);
			}elseif ($field['name'] == 'photo' && !empty($value)) {
				$value = sprintf('<img src="%s" alt="%s" >', $value, basename($value) );
			}
			// replace element	
			$form_html = str_replace($element_ph,$value,$form_html);
		}
	}	
	
	// loop default fields to replace form elements
	foreach($profile_fields as $field_key=>$field){
		// skip password in non edit mode
		//if(!$edit_mode && $field['name'] == 'user_password') continue;	
		if(!$edit_mode && in_array($field['name'],array('user_password','user_password_conf'))) continue;	
		$continue = false;
		foreach($cf_noton_profile as $cffield){			
			if($cffield['name'] == $field['name']) {
				$continue = true;				
				break;
			}
		}
		if($continue) continue;		
		
		// field wrapper
		$wrapper_ph = sprintf('[user_field_wrapper_%s]',$field['name']);	
		// field label
		$label_ph = sprintf('[user_field_label_%s]', $field['name']);
		// field/html element
		$element_ph = sprintf('[user_field_element_%s]', $field['name']);
		
		// edit mode
		if($edit_mode){	
			// for username 
			if($field['name'] =='user_login'){
				$field['label'] = sprintf('%s (<em>%s</em>)',mgm_stripslashes_deep($field['label']),__('Username not changable','mgm'));
			}elseif($field['name'] =='user_password'){
				$field['label'] = sprintf('%s (<em>%s</em>)',mgm_stripslashes_deep($field['label']),__('Leave blank if don\'t wish to update','mgm'));
			}
		}else{
			// for display_name 
			if($field['name'] == 'display_name'){
				$field['label'] = __('Display Name','mgm');
			}
		}
		
		// replace wrapper
		$form_html = str_replace($wrapper_ph, $field['name'].'_box', $form_html);
			
		// replace label
		$form_html = str_replace($label_ph, mgm_stripslashes_deep($field['label']), $form_html);
		
		// selected value
		if(isset($user->$field['name'])){ // wp alias
			$value = $user->$field['name'];
		}else if(isset($mgm_member->custom_fields->$field_key)){// custom field
			$value = $mgm_member->custom_fields->$field_key;		
		}else{// none
			$value = '';
		}	
		
		// dont set value for password
		//if($field['name'] == 'user_password') $value = '';	
		if(in_array($field['name'],array('user_password','user_password_conf'))) $value = '';	
		
		// edit mode
		if($edit_mode){
		// replace element			
			$form_html = str_replace($element_ph,$form_fields->get_field_element($field,'mgm_profile_field',$value),$form_html);
		}else{				
			// country
			if($field_key == 'country'){
				$value = mgm_country_from_code($value);
			}	
			// set		
			$form_html = str_replace($element_ph,$value,$form_html);
		}
	}	
	// attach scripts	
	$form_html .= mgm_attach_scripts(true, array());
	
	// range
	$yearRange = mgm_get_calendar_year_range();
	
	// append script
	$form_html .= '<script language="javascript">jQuery(document).ready(function(){try{mgm_date_picker(".mgm_date",false,{yearRange:"'.$yearRange.'"});}catch(x){}});</script>';
		
	//include scripts for image upload:
	if(!empty($arr_images)) {		
		$form_html .= mgm_upload_script_js('profileform', $arr_images);
	}
	
	// buttun
	$button_html = '';
	// button on edit
	if($edit_mode){
		// default
		$button_html = '<p><input type="submit" name="wp-submit" id="wp-submit" value="' . __('Update &raquo;','mgm') . '" /></p>';
		// apply button filter
		$button_html = apply_filters('mgm_profile_form_button', $button_html);
	}
	
	// output form	
	$html = '<div class="mgm_prifile_form">
				' . $error_html . '
				<form class="mgm_form" name="profileform" id="profileform"  action="' . $form_action . '" method="post">	               
				   ' . $form_html . '
				   ' . $button_html . '	
				   <input type="hidden" name="method" value="update_user">
				</form>
			 </div>';	
	// filter
	$html = apply_filters('mgm_user_profile_form_html', $html, $current_user);
		
	// return
	return $html;
}
// mgm_get_user_display_names
function mgm_get_user_display_names(){
	// current user
	$current_user = wp_get_current_user();
	// init
	$display_names = array();
	// set 
	$display_names['display_username'] = $current_user->user_login;
	$display_names['display_nickname'] = $current_user->nickname;
	// first name
	if ( !empty($current_user->first_name) )
		$display_names['display_firstname'] = $current_user->first_name;
	if ( !empty($current_user->last_name) )
		$display_names['display_lastname'] = $current_user->last_name;
	if ( !empty($current_user->first_name) && !empty($current_user->last_name) ) {
		$display_names['display_firstlast'] = mgm_str_concat($current_user->first_name, $current_user->last_name);
		$display_names['display_lastfirst'] = mgm_str_concat($current_user->last_name, $current_user->first_name);
	}
	// set
	if ( !in_array( $current_user->display_name, array_values($display_names) ) ) // Only add this if it isn't duplicated elsewhere
		$display_names = array_merge(array( 'display_displayname' => $current_user->display_name ),  $display_names);
		
	$display_names = array_map( 'trim', $display_names );
	$display_names = array_unique( $display_names );
	
	// return
	return $display_names;
}

//validate and save profile data
function mgm_user_profile_update( $user_id ) {
	global $wpdb;
	
	// get user
	if ( $user_id > 0 ) {		
		$user_data = get_userdata( $user_id );
	} 	
	
	// error
	if(!$user_data->ID) 
		return $user_id;
		
	// set aside member object
	//$mgm_member = $user_data->mgm_member;	
	$mgm_member = mgm_get_member($user_id);	
		
	// create empty user
	$user = new stdClass;	
	// set id
	$user->ID = $user_data->ID;
		
	// sanitize user login
	if ( isset( $_POST['user_login'] ) )
		$user->user_login = sanitize_user($_POST['user_login'], true);
	
	// asnitize email and copy	
	if ( isset( $_POST['user_email'] ))
		$user->user_email = sanitize_text_field( $_POST['user_email'] );
	
	// urls
	if ( isset( $_POST['mgm_profile_field']['url'] ) ) {
		if ( empty ( $_POST['mgm_profile_field']['url'] ) || $_POST['mgm_profile_field']['url'] == 'http://' ) {
			$user->user_url = '';
		} else {
			$user->user_url = esc_url_raw( $_POST['mgm_profile_field']['url'] );
			$user->user_url = preg_match('/^(https?|ftps?|mailto|news|irc|gopher|nntp|feed|telnet):/is', $user->user_url) ? $user->user_url : 'http://'.$user->user_url;
		}
	}
	if ( isset( $_POST['mgm_profile_field']['first_name'] ) )
		$user->first_name = sanitize_text_field( $_POST['mgm_profile_field']['first_name'] );
	if ( isset( $_POST['mgm_profile_field']['last_name'] ) )
		$user->last_name = sanitize_text_field( $_POST['mgm_profile_field']['last_name'] );
	if ( isset( $_POST['mgm_profile_field']['nickname'] ) )
		$user->nickname = sanitize_text_field( $_POST['mgm_profile_field']['nickname'] );
	if ( isset( $_POST['mgm_profile_field']['display_name'] ) )
		$user->display_name = sanitize_text_field( $_POST['mgm_profile_field']['display_name'] );	
	if ( isset( $_POST['mgm_profile_field']['description'] ) )
		$user->description = trim( $_POST['mgm_profile_field']['description'] );		
	
	// init errors
	$errors = new WP_Error();	
	
	// check user login
	if ( isset( $_POST['user_login'] ) && !validate_username( $_POST['user_login'] ) )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.','mgm' ));
	
	// user login duplicate
	if ( ( $owner_id = username_exists( $user->user_login ) ) && $owner_id != $user->ID )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.','mgm' ));	
	
	// nickname
	if (!isset( $_POST['mgm_profile_field']['nickname'] ) || ( isset( $_POST['mgm_profile_field']['nickname'] ) && empty( $_POST['mgm_profile_field']['nickname'] )))
		$errors->add( 'nickname', __( '<strong>ERROR</strong>: You must provide a Nick Name.','mgm' ));
	
	// email
	if ( empty( $user->user_email ) ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please enter an e-mail address.','mgm' ), array( 'form-field' => 'email' ) );
	} elseif ( !is_email( $user->user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The e-mail address isn&#8217;t correct.','mgm' ), array( 'form-field' => 'email' ) );
	} elseif ( ( $owner_id = email_exists($user->user_email) ) && $owner_id != $user->ID ) {
		$errors->add( 'email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.','mgm'), array( 'form-field' => 'email' ) );
	}		
	
	// password:
	$pass1 = $pass2 = '';
	if ( isset( $_POST['user_password'] ))
		$pass1 =  sanitize_text_field( $_POST['user_password']);
	if ( isset( $_POST['user_password_conf'] ))
		$pass2 =  sanitize_text_field( $_POST['user_password_conf'] );
	/* checking the password has been typed twice */	
	do_action_ref_array( 'check_passwords', array ( $user->user_login, & $pass1, & $pass2 ));
	
	if ( empty($pass1) && !empty($pass2) )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: You entered your new password only once.','mgm' ), array( 'form-field' => 'pass1' ) );
	elseif ( !empty($pass1) && empty($pass2) )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: You entered your new password only once.','mgm' ), array( 'form-field' => 'pass2' ) );

	/* Check for "\" in password */
	if ( false !== strpos( stripslashes($pass1), "\\" ) )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Passwords may not contain the character "\\".','mgm' ), array( 'form-field' => 'pass1' ) );

	/* checking the password has been typed twice the same */
	if ( $pass1 != $pass2 )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter the same password in the two password fields.','mgm' ), array( 'form-field' => 'pass1' ) );	
	
	// set
	if(!empty($pass1))
		$user->user_pass = $mgm_member->user_password = $pass1;	
	
	// get default fields
	$profile_fields = mgm_get_config('default_profile_fields', array());
	// get active custom fields on profile page
	$cf_profile_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_profile'=>true)));
	
	// loop
	foreach($cf_profile_page as $field){		
		// skip default fields, validated already
		if(in_array($field['name'], array('username','email','password','password_conf'))) continue;			
		// skip html
		if($field['type'] == 'html' || $field['type'] == 'label') continue;							
		// check register and required		
		if((bool)$field['attributes']['required'] === true){		
			// error
			$error_codes = $errors->get_error_codes();
			// validate other				
			if ( (!isset($_POST['mgm_profile_field'][$field['name']])) || (empty($_POST['mgm_profile_field'][$field['name']])) ) {
				$errors->add($field['name'], __('<strong>ERROR</strong>: You must provide a '.mgm_stripslashes_deep($field['label']).'.','mgm'));
			}			
		}
	}	
	// Allow plugins to return their own errors.
	do_action_ref_array('user_profile_update_errors', array ( &$errors, $update, &$user ) );
	
	// error 
	if ( $errors->get_error_codes() )
		return $errors;	
	
	//start saving		
	$user_id = wp_update_user( get_object_vars( $user ) );	 
	//update custom values:		
	if (isset($_POST['mgm_profile_field'])) {			
		// loop fields
		foreach($cf_profile_page as $field){
			// skip html
			if($field['type'] == 'html' || $field['type'] == 'label') continue;
			// set					
			if(isset($_POST['mgm_profile_field'][ $field['name'] ])) {
				$mgm_member->custom_fields->$field['name'] = $_POST['mgm_profile_field'][ $field['name'] ];
			}elseif(isset($_POST[$field['name']])) {				
				$mgm_member->custom_fields->$field['name'] = $_POST[$field['name']];
			}
		}			
	}
	// update //TODO Check integrity			
	// update_user_option($user_id, 'mgm_member', $mgm_member, true);
	$mgm_member->save();
	// return id
	return $user_id;
}
// exclude default
function mgm_exclude_default_pages(){
	// user
	$user = wp_get_current_user();
	// system
	$mgm_system = mgm_get_class('system');
	// init
	$hide_posts = array();
	// get all pages
	$pages = get_pages();		
	// count
	if(count($pages)){
		// user logged in
		if($user->ID){
			$hide_urls = array('register','login','lostpassword');
		}else{
			$hide_urls = array('profile','membership_contents','membership_details');
		}
		// other
		$hide_urls[] = 'transactions';
		// permalink
		foreach($pages as $page){
			// permalink
			$permalink = trailingslashit(get_permalink($page->ID));
			// pages to hide
			foreach($hide_urls as $u){
				// match
				if(trailingslashit($mgm_system->setting[$u.'_url']) == $permalink){
					// hide
					$hide_posts[] = $page->ID;							
				}
			}			
		}
	}			
		
	// return 
	return $hide_posts;
}
// unchecked
function mgm_replace_message_tags($message,$user_id=NULL) {
	// get user
	if(!$user_id){
		// cusrrent user
		$current_user = wp_get_current_user();
		// set 
		$user_id = $current_user->ID;
	}		
	// int
	$logged_in = (isset($current_user) && $current_user->ID>0) ? true : false;
	// user
	if ($user_id > 0) {			
		// get user
		$user         = get_userdata($user_id);
		// mgm member
		$mgm_member   = mgm_get_member($user_id); 
		// set
		$username     = $user->user_login;
		$name         = mgm_str_concat($user->first_name, $user->last_name);
		$email        = $user->user_email;
		$url          = $user->user_url;
		$display_name = $user->display_name;
		$first_name   = $user->first_name;
		$last_name    = $user->last_name;
		$description  = $user->description;
		$nickname     = $user->nickname;
				
		// get active custom fields
		$custom_fields = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true,'on_profile'=> true,'on_public_profile'=> true)));
		// init
		$custom_field_tags = array();
		// loop
		foreach($custom_fields as $custom_field){
			// if already set skip it
			if(!isset(${$custom_field['name']}) || (isset(${$custom_field['name']}) && empty(${$custom_field['name']}))){
				// check
				if(isset($mgm_member->custom_fields->$custom_field['name'])){
					// skip password always
					if($custom_field['name']=='password') continue;
					// value
					$value = $mgm_member->custom_fields->$custom_field['name'];
					// country
					if($custom_field['name']=='country') $value = mgm_country_from_code($value);
					// set
					$custom_field_tags[$custom_field['name']] = $value ;
				}
			}	
		}		
	}else{
		// get active custom fields
		$custom_fields = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true,'on_profile'=> true,'on_public_profile'=> true)));
		// init
		$custom_field_tags = array();
		// loop
		foreach($custom_fields as $custom_field){
			// set
			$custom_field_tags[$custom_field['name']] = '';
		}
	}	

	/*
	 * [[purchase_cost]] = Cost and currency of a purchasable post
	 * [[login_register]] = Login or register form
	 * [[login_register_links]] = Links for login and register
	 * [[login_link]] = Login link only
	 * [[register_link]] = Register link only
	 * [[membership_types]] = A list of membership levels that can see this post/page
	 * [[duration]] = number of days that the user will have access for
	 * [[username]] = username
	 * [[name]] = name / username
	 * [[register]] = register form
	 */
    // post
	$post_id    = get_the_ID();
	// vars
	$mgm_system = mgm_get_class('system');
	$currency   = $mgm_system->setting['currency'];
	$mgm_post   = mgm_get_post($post_id);  
	$duration   = $mgm_post->get_access_duration();
	if (!$duration) $duration = __('unlimited', 'mgm');	
	$purchase_cost = $mgm_post->purchase_cost . $currency;	
	
	// these function calls are called repeadtedly as filter is used in multiple places
	// call only when tag present in message
	
	// [login_register_links]
	if(preg_match('/[[login_register_links]]/',$message)){
		$login_register_links = (!$logged_in ? mgm_get_login_register_links():'');
	}
	// [login_link]
	if(preg_match('/[[login_link]]/',$message)){
		$login_link = (!$logged_in ? mgm_get_login_link():'');
	}	
	// [register_link]	
	if(preg_match('/[[register_link]]/',$message)){	
		$register_link = (!$logged_in ? mgm_get_register_link():'');
	}
	// [login_register]
	if(preg_match('/[[login_register]]/',$message)){
		$login_register = (!$logged_in ? mgm_login_form(__('Register','mgm')):'');
	}
	// [register]
	if(preg_match('/[[register]]/',$message)){
		$register = (!$logged_in ? mgm_user_register_form():'');
	}
	// membership type
	if (!$membership_types = $mgm_post->get_access_membership_types()) {
		// purchasble
		if (mgm_post_is_purchasable($post_id)) {
			$membership_types = 'Purchasable Only';
		} else {
		// access 
			$membership_types = 'No access';
		}
	}else{
		// get object
		$mgm_membership_types = mgm_get_class('membership_types');
		// init array
		$ms_types_array = array();
		// loop
		foreach($membership_types as $membership_type){
			// set
			$ms_types_array[] = $mgm_membership_types->membership_types[ $membership_type ];
		}
		// reset					
		$membership_types = implode(', ', $ms_types_array);
		// unset
		unset($ms_types_array);			
	}
	
	// loop defined
	$tags = array('purchase_cost','login_register','login_register_links','login_link','register_link','membership_types',
				  'duration','register','username','name','email','url','display_name','first_name','last_name',
				  'description','nickname');				  
	// loop
	foreach($tags as $tag){
		// check
		if(!isset(${$tag})) ${$tag} = '';
		// set
		$message = str_replace('[['.$tag.']]', ${$tag}, $message);
	}	
			
	// custom_field_tags
	if(is_array($custom_field_tags)){
		// loop
		foreach($custom_field_tags as $tag=>$value){
			// check
			if(!isset($value)) $value = '';					
			// set
			$message = str_replace('[['.$tag.']]', $value, $message);
		}	
	}
	// return
	return $message;
}
//remove manually included scripts from $wp_scripts object
function mgm_filter_scripts() { 
	
	global $mgm_scripts, $wp_scripts;	//mgm_scripts is the array to hold mgm scripts loaded at runtime.	
	if(is_array($wp_scripts->registered) && is_array($mgm_scripts)) {
		$mgm_scripts = array_unique($mgm_scripts);				
		foreach ($wp_scripts->registered as $key => $obj) {			
			$file = basename($obj->src);
			if(in_array($file, $mgm_scripts)) {	//This will prevent library scripts from loading multiple times							
				wp_deregister_script( $key );				
			}
		}
	}
	//specifically remove jquery files:
	//incomplete	
	//return;
	$arr_jquery = array();
	$arr_exceptions = array('jquery.ajaxfileupload.js',
							'jquery-ui-1.7.3.min.js',
							'jquery.form.js',
							'jquery.scrollTo-min.js',
							'jquery.validate.pack.js',
							'jquery.corner.js');
	foreach ($wp_scripts->registered as $key => $obj) {
		$file = basename($obj->src);
		//if(preg_match('/jquery./', $file) || preg_match('/jquery-/', $file)) {
		if(preg_match('/jquery./', $file) || preg_match('/jquery-/', $file)) {
			if(in_array($file, $arr_jquery) && !in_array($file, $arr_exceptions)) {							
				wp_deregister_script( $key );
			}else
				$arr_jquery[] = $file;
		}
	}
}
// return content_type
function mgm_get_mail_content_type(){	
		
	return mgm_get_class('system')->setting['email_content_type'];
}
// return defautl content_type
function mgm_get_mail_default_content_type(){
	
	return 'text/plain';
}
// core/hooks/hooks/content_hooks.php
// end of file