<?php
/**
 * Magic Members admin settings module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_settings extends mgm_controller{
 	
	// construct
	function mgm_settings()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();
		// load template view
		$this->load->template('settings/index', array('data'=>$data));		
	}
	
	// general
	function general(){				
		// local
		extract($_POST);
				
		// update
		if(isset($settings_update) && !empty($settings_update)){
			// get system object	
			$system = mgm_get_class('system');			
			// update if set
			foreach($system->setting as $k => $v){
				// set default
				if($k == 'reminder_days_incremental'){
					$_POST[$k] = isset($_POST[$k]) ? $_POST[$k] : 'N';
				}elseif($k == 'use_ssl_paymentpage'){
					$_POST[$k] = isset($_POST[$k]) ? $_POST[$k] : 'N';
				}
				// set var
				if(isset($_POST[$k])){
					$system->setting[$k] = addslashes($_POST[$k]);		
				}
			}
			// enable_autologin
			if(!isset($_POST['enable_autologin']))
				$system->setting['enable_autologin'] = 'N';								
				
			if(!isset($_POST['aws_enable_s3']))
				$system->setting['aws_enable_s3'] = 'N';
			//remove this after putting a object merge:	
			$system->setting['enable_post_url_redirection'] = !isset($_POST['enable_post_url_redirection']) ? 'N' : $_POST['enable_post_url_redirection'] ; 										
			// update
			// update_option('mgm_system', $system);
			$system->save();
			
			// affiliate
			if($_POST['use_affiliate_link'] == 'Y' && $_POST['affiliate_id']){
				update_option('mgm_affiliate_id', intval($_POST['affiliate_id']));
			}else{
				delete_option('mgm_affiliate_id');
			}					
			
			// message
			echo json_encode(array('status'=>'success','message'=>__('General settings successfully updated.','mgm')));			
			// return
			return;
		}
		
		// data
		$data = array();
		// system
		$data['system'] = mgm_get_class('system');
		// load template view
		$this->load->template('settings/general', array('data'=>$data));		
	}
	
	// posts
	function posts(){	
		global $wpdb;	
		// local
		extract($_POST);
				
		// update
		if(isset($post_setup_save) && !empty($post_setup_save)){	
			// init updatd 
			$updated=0;					
			// get system object	
			$mgm_system = mgm_get_class('system');
			// content protection
			$content_protection = $mgm_system->setting['content_protection'];
			// membership types
			if(is_array($access_membership_types)){	
				$membership_types = json_encode($access_membership_types);
			}else{
				$membership_types = json_encode(array());
			}
			// init posts
			$wp_posts = array();
			// posts
			if($posts){
				$wp_posts = array_merge($wp_posts, $posts);
			}
			// pages
			if($pages){
				$wp_posts = array_merge($wp_posts, $pages);
			}			
			// add direct urls
			if($direct_urls){				
				// loop
				foreach($direct_urls as $direct_url_id => $direct_url){		
					// affected
					$affected = false;
					// update
					if((int)$direct_url_id>0){
						/* this will not happen now *************************************
						// delete
						if(empty($direct_url)){					
							$wpdb->query(sprintf("DELETE FROM `%s` WHERE `id`='%d'", TBL_MGM_POST_PROTECTED_URL, $direct_url_id));
						}else{
						// update
							// data
							$sql_data = array('url'=>$direct_url);
							// assign access types to selected urls only
							if(is_array($direct_url_ids)){
								// check
								if(in_array($direct_url_id, $direct_url_ids)){
									// set
									$sql_data['membership_types'] = $membership_types;
								}
							}							
							// check duplicate
							if(!mgm_is_duplicate(TBL_MGM_POST_PROTECTED_URL, array('url'), "id <> `{$direct_url_id}`", array('url' => $direct_url))){
								// update all
								$affected = $wpdb->update(TBL_MGM_POST_PROTECTED_URL, $sql_data, array('id'=>$direct_url_id));
							}else{
								// just update access
								// unset url 
								unset($sql_data['url']);
								// check
								if(isset($sql_data['membership_types'])){
									// update
									$affected = $wpdb->update(TBL_MGM_POST_PROTECTED_URL, $sql_data, array('id'=>$direct_url_id));
								}
							}
						}****************/
					}else{
					// insert
						if(!empty($direct_url)){
							// check duplicate
							if(!mgm_is_duplicate(TBL_MGM_POST_PROTECTED_URL, array('url'), '', array('url' => $direct_url))){
								// add
								$affected = $wpdb->insert(TBL_MGM_POST_PROTECTED_URL, array('url'=>$direct_url,'membership_types'=>$membership_types));
							}	
						}	
					}
					// update counter
					if($affected) $updated++;
				} 
			}		
			
			// check
			if($wp_posts){
				// loop
				foreach($wp_posts as $post_id){				
					// if access set
					if(is_array($access_membership_types)){	
						// get object
						$mgm_post_object = mgm_get_post($post_id);
						// set
						$mgm_post_object->access_membership_types = $access_membership_types;
						// save meta
						// update_post_meta($post_id, '_mgm_post', $mgm_post_object);
						$mgm_post_object->save();
						// unset
						unset($mgm_post_object);
						// check duplicate
						if(!mgm_is_duplicate(TBL_MGM_POST_PROTECTED_URL, array('post_id'), '', array('post_id' => $post_id))){
							// add
							$affected = $wpdb->insert(TBL_MGM_POST_PROTECTED_URL, array('url'=>get_permalink($post_id),'post_id'=>$post_id,'membership_types'=>$membership_types));
						}else{
							$affected = $wpdb->update(TBL_MGM_POST_PROTECTED_URL, array('membership_types'=>$membership_types), array('post_id'=>$post_id));
						}
					}
					
					// make private
					if (mgm_protect_content($content_protection)) {
						// get post
						$wp_post = wp_get_single_post($post_id);
						// double check
						if(preg_match('/\[private\](.*)\[\/private\]/', $wp_post->post_content) == FALSE){												
							// set content
							$post_content = sprintf('[private]%s[/private]', $wp_post->post_content);
							// update
							wp_update_post(array('post_content'=>$post_content,'ID'=>$wp_post->ID));	
						}
					}
					// update counter
					$updated++;
				}					
			}
							
			// message
			if($updated){
				echo json_encode(array('status'=>'success','message'=>__(sprintf('Post protection successfully updated. %d Post/Page(s) updated.', $updated), 'mgm')));
			}else{
				echo json_encode(array('status'=>'error','message'=>__(sprintf('Post protection failed. %d Post/Page(s) selected.', $updated), 'mgm')));
			}				
			// return
			return;
		}
		// data
		$data = array();
		// member types
		$arr_membershiptypes = array();
		// loop
		foreach (mgm_get_class('membership_types')->membership_types as $code => $name){
			$arr_membershiptypes[ $code ] = mgm_stripslashes_deep($name); 
		}
		// set	
		$data['membership_types'] = $arr_membershiptypes;	
		// posts
		$data['posts'] = mgm_field_values($wpdb->posts, 'ID', 'post_title', "AND (post_content NOT LIKE '%[private]%' OR post_content LIKE '[private]%') AND post_type = 'post' AND post_status = 'publish'");	
		// pages
		$data['pages'] = mgm_field_values($wpdb->posts, 'ID', 'post_title', "AND (post_content NOT LIKE '%[private]%' OR post_content LIKE '[private]%') AND post_type = 'page' AND post_status = 'publish'");	
		// posts access
		$data['posts_access'] = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE `post_id` IS NOT NULL ORDER BY id ASC",TBL_MGM_POST_PROTECTED_URL));
		// direct urls access
		$data['direct_urls_access'] = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE `post_id` IS NULL ORDER BY id ASC",TBL_MGM_POST_PROTECTED_URL));
		// load template view
		$this->load->template('settings/posts', array('data'=>$data));	
	}	
	
	// post posts_access_list
	function post_posts_access_list(){
		global $wpdb;
		// init
		$data = array();
		// page urls
		$data['posts_access'] = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE `post_id` IS NOT NULL ORDER BY id ASC",TBL_MGM_POST_PROTECTED_URL));
		// load template view
		$this->load->template('settings/posts/posts_access', array('data'=>$data));	
	}
	
	// post direct_urls_access
	function post_direct_urls_access(){
		global $wpdb;
		// init
		$data = array();
		// page urls
		$data['direct_urls_access'] = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE `post_id` IS NULL ORDER BY id ASC",TBL_MGM_POST_PROTECTED_URL));
		// load template view
		$this->load->template('settings/posts/direct_urls_access', array('data'=>$data));	
	}
	
	// post_access_update
	function post_settings_delete(){
		global $wpdb;	
		extract($_POST);		
		// check
		$post_id = $wpdb->get_var("SELECT `post_id` FROM `".TBL_MGM_POST_PROTECTED_URL . "` WHERE id = '{$id}'");		
		// if post
		if((int)$post_id>0){
		// update content
			// get content
			$wp_post = wp_get_single_post($post_id);
			// update
			wp_update_post(array('post_content'=>preg_replace('/\[\/?private\]/','',$wp_post->post_content),'ID'=>$wp_post->ID));
		}
		// sql
		$sql = "DELETE FROM `" . TBL_MGM_POST_PROTECTED_URL . "` WHERE id = '{$id}'"; 		
		// delete
	    if ($wpdb->query($sql)) {    	    
			$message = __('Successfully deleted post settings: ', 'mgm');
			$status  = 'success';
	    }else{
			$message = __('Error while deleting post settings: ', 'mgm');
			$status  = 'error';
		}
		// return response
		echo json_encode(array('status'=>$status, 'message'=>$message));
	}
	
	// messages
	function messages(){	
		global $wpdb;	
		// local
		extract($_POST);
		// update
		if(isset($msgs_update) && !empty($msgs_update)){
			// get system object	
			$system = mgm_get_class('system');
			// update if set
			foreach($_POST['setting'] as $k => $v){		
				// set var
				if(isset($v)){
					// set template
					$system->set_template($k, $v);// save
					// copy to custom fields	
					if($k == 'subs_intro'){
						mgm_get_class('member_custom_fields')->update_field_value('subscription_introduction',$v);	
					}
					// tos
					if($k == 'tos'){
						mgm_get_class('member_custom_fields')->update_field_value('terms_conditions',$v);	
					}
				}
			}		
			// _update_modules
			if(isset($apply_update_to_modules) && $apply_update_to_modules == 'Y'){
				// update
				$this->_update_modules();
			}
			// response
			echo json_encode(array('status'=>'success','message'=>__('Message templates successfully updated.','mgm')));			
			// return
			return;
		}	
		// data
		$data = array();
		// system
		$data['system'] = mgm_get_class('system');		
		// load template view
		$this->load->template('settings/messages', array('data'=>$data));		
	}
	
	// emails
	function emails(){	
		global $wpdb;	
		// local
		extract($_POST);
		// update
		if(isset($msgs_update) && !empty($msgs_update)){
			// get system object	
			$system = mgm_get_class('system');
			// update if set
			foreach($_POST['setting'] as $k => $v){		
				// set var
				if(isset($v)){
					$system->set_template($k, $v);		
				}
			}						
			// response
			echo json_encode(array('status'=>'success','message'=>__('Email templates successfully updated.','mgm')));
			// return
			return;
		}	
		// data
		$data = array();
		// system
		$data['system'] = mgm_get_class('system');		
		// load template view
		$this->load->template('settings/emails', array('data'=>$data));		
	}
	
	// autoresponders
	function autoresponders(){		
		global $wpdb;
		// make local
		extract($_REQUEST);		
		// update
		if(isset($update) && !empty($update)){			
			// get module
			$module_object = mgm_get_module($active_module, 'autoresponder');				
			// settings update
			$module_object->settings_update();	
			// enable and activate module
			$module_object->enable(true);
			// return
			return;
		}
		
		// data
		$data = array();
		// get available
		$data['available_modules'] = mgm_get_available_modules('autoresponder');
		// loop
		foreach($data['available_modules'] as $module_name):				
			// get object
			$module_object = mgm_get_module('mgm_'.$module_name, 'autoresponder');				
			// get html
			$data['module'][$module_name]['html'] = $module_object->settings_box();					
		endforeach;		
		// load template view
		$this->load->template('settings/autoresponders', array('data'=>$data));	
	}	
	
	// restapi 
	function restapi(){
		global $wpdb;
		// local
		extract($_POST);
				
		// update
		if(isset($settings_update) && !empty($settings_update)){
			// get system object	
			$system = mgm_get_class('system');					
			// set data
			$system->setting['rest_server_enabled']    = $rest_server_enabled;
			$system->setting['rest_output_formats']    = $rest_output_formats;
			$system->setting['rest_input_methods']     = $rest_input_methods;
			$system->setting['rest_consumption_limit'] = (int)$rest_consumption_limit;
			// save
			$system->save();					
			// message
			echo json_encode(array('status'=>'success','message'=>__('Rest API settings successfully updated.','mgm')));			
			// return
			return;
		}
		
		// data
		$data = array();		
		// system
		$data['system'] = mgm_get_class('system');
		// load template view
		$this->load->template('settings/restapi', array('data'=>$data));	
	}
	
	// restapi_levels
	function restapi_levels(){
		global $wpdb;
		// data
		$data = array();
		// get list of levels
		$data['levels'] = $wpdb->get_results("SELECT id,level,name,permissions,limits FROM `".TBL_MGM_REST_API_LEVEL."` ORDER BY level ASC");
		// load template view
		$this->load->template('settings/restapi/levels', array('data'=>$data));
	}
	
	// restapi_keys
	function restapi_keys(){
		global $wpdb;
		// data
		$data = array();
		// get list of keys
		$data['keys'] = $wpdb->get_results("SELECT id,level,api_key,create_dt FROM `".TBL_MGM_REST_API_KEY."` ORDER BY create_dt DESC");
		// load template view
		$this->load->template('settings/restapi/keys', array('data'=>$data));
	}
	
	// PRIVATE ---------------------------------------------------------------------
	// update modules
	function _update_modules(){
		// get modules
		$modules = mgm_get_available_modules('payment');
		// loop
		foreach($modules as $module):			
			// instance	
			$module_object = mgm_get_module('mgm_'.$module, 'payment');						
			// update message	
			$module_object->_setup_callback_messages(array(), true); // update from global template			
			// update option
			// update_option($module_object->code, $module_object);
			$module_object->save();
		endforeach;	
	}		
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_settings.php