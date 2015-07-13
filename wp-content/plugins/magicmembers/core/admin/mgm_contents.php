<?php
/**
 * Magic Members admin contents module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_contents extends mgm_controller{
 	
	// construct
	function mgm_contents()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();
		// load template view
		$this->load->template('contents/index', array('data'=>$data));			
	}																																																																																																																																																																																																									
	
	// access
	function access(){				
		global $wpdb;	
		extract($_POST);
		
		// set 
		if(isset($update) && !empty($update)){
			// get system object	
			$system = mgm_get_class('system');
			// update if set
			foreach($system->setting as $k => $v){				
				// set var
				if(isset($_POST[$k])){
					$system->setting[$k] = addslashes($_POST[$k]);		
				}
			}			
			// update
			// update_option('mgm_system', $system);
			$system->save();
			// update
			$message = __('Content access settings successfully updated.', 'mgm');
			$status  = 'success';
			// return response			
			echo json_encode(array('status'=>$status, 'message'=>$message));				
			exit();
		}
		
		// data
		$data = array();	
		// system
		$data['system'] = mgm_get_class('system');	
		// load template view
		$this->load->template('contents/access', array('data'=>$data));
	}
	
	// download
	function download(){		
		global $wpdb;	
		// data
		$data = array();			
		// load template view
		$this->load->template('contents/download/index', array('data'=>$data));
	}
	
	// download_list
	function download_list(){		
		global $wpdb;	
		// data
		$data = array();	
		// downloads
		$data['downloads'] = $wpdb->get_results('SELECT * FROM `' . TBL_MGM_DOWNLOAD.'` ORDER BY `post_date` ');	
		// load template view
		$this->load->template('contents/download/list', array('data'=>$data));
	}
	
	// download_add
	function download_add(){
		get_currentuserinfo();
		global $wpdb,$current_user;	
		extract($_POST);
		
		// save
		if(isset($submit_download)){
			// set vars
			$members_only = (isset($_POST['members_only'])) ? 'Y' :'N';
			// file
			$filename = (isset($_POST['download_file_new'])) ? $download_file_new : $direct_url;
			$real_filename = $_POST['download_file_new_realname'];
			// code
			$code = uniqid();			
			// sql			
			$sql = "INSERT INTO `" . TBL_MGM_DOWNLOAD . "` SET title='" . $title . "', filename='" . $filename . "', 
					real_filename='" . $real_filename . "',	post_date=NOW(), user_id='" . $current_user->ID . "', 
					members_only='" . $members_only . "', code='".$code."', 
					expire_dt = ".((!empty($expire_dt)) ? ("'".date('Y-m-d', strtotime($expire_dt))."'") : 'NULL');		
			// mgm_log($sql);			
			// saved
			if ($wpdb->query($sql)) {
				// id
				$id = $wpdb->insert_id;	
				// id																																																																																																																																																																																																																																																																																																																																																// save 
				if ($id) {
					// assoc
					if ($link_to_post_id) {
						// loop
						foreach ($link_to_post_id as $post_id) {
							// sql
							$sql = 'INSERT INTO `' . TBL_MGM_DOWNLOAD_POST_ASSOC. '` (download_id, post_id)
									VALUES (' . $id . ', ' . $post_id . ')';
							$wpdb->query($sql);
						}
					}
				}
				// set message																																																																																																																																																																																																																								
				$message = sprintf(__('Successfully created new download: %s', 'mgm'),  $title);
				$status  = 'success';				
			}else{
				$message = sprintf(__('Error while creating new download: %s', 'mgm'),  $title);
				$status  = 'error';
			}
			// return response			
			echo json_encode(array('status'=>$status, 'message'=>$message)); exit();
		}	
		
		// data
		$data = array();
		$post_types = array('post', 'page');
		if(function_exists('get_post_types')) {
			$post_types = get_post_types(array('public' => true));
			if(in_array('attachment',$post_types))
				unset($post_types[ array_search('attachment', $post_types) ]);				
		}
			
		$post_types = implode('\',\'', $post_types);
		// all posts
		$data['posts'] = mgm_field_values( $wpdb->posts, 'ID', 'post_title', "AND post_status = 'publish' AND post_type IN ('$post_types')", 'post_title');			
		// load template view
		$this->load->template('contents/download/add', array('data'=>$data));
	}
	
	// download_edit
	function download_edit(){
		get_currentuserinfo();
		global $wpdb,$current_user;	
		extract($_POST);
		// system 
		$mgm_system = mgm_get_class('system');		
		// save
		if(isset($submit_download)){
			// set vars
			$members_only = (isset($_POST['members_only'])) ? 'Y' :'N';
			// file
			$filename = (isset($_POST['download_file_new'])) ? $download_file_new : $direct_url;
			$real_filename = $_POST['download_file_new_realname'];
			// code
			$code = (empty($code)) ? uniqid() : $code;			
			// sql			
			$sql = "UPDATE `" . TBL_MGM_DOWNLOAD . "` SET
					`title` = '" . $title . "', " . 
					($filename ? "`filename` = '" . $filename . "',":"") . 
					($real_filename ? "`real_filename` = '" . $real_filename . "', ":"") . 
					"`post_date` = NOW(), `user_id` = '" . $current_user->ID . "', 
					`members_only` = '" . $members_only . "', `code` = '" . $code . "', 
					`expire_dt` = ".((!empty($expire_dt)) ? ("'".date('Y-m-d', strtotime($expire_dt))."'") : 'NULL')." 
					WHERE id = '" . (int)$id . "'";	
			// log			
			// mgm_log($sql. print_r($_POST,true));		
			// saved
			if ($wpdb->query($sql)) {
				// clear old
				$sql = 'DELETE FROM `' . TBL_MGM_DOWNLOAD_POST_ASSOC . '` WHERE download_id = ' . $id;
				$wpdb->query($sql);
				// save 				
				if ($link_to_post_id) {
					// loop
					foreach ($link_to_post_id as $post_id) {
						$sql = 'INSERT INTO `' . TBL_MGM_DOWNLOAD_POST_ASSOC. '` (download_id, post_id)
								VALUES (' . $id . ', ' . $post_id . ')';
						$wpdb->query($sql);
					}
				}			
				// set message
				$message = sprintf(__('Successfully updated download: %s', 'mgm'),  $title);
				$status  = 'success';				
			}else{
				$message = sprintf(__('Error while updating download: %s Or nothing updated!', 'mgm'),  $title);
				$status  = 'error';
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}	
		
		// data
		$data = array();
		// download		
		$data['download'] = $wpdb->get_row("SELECT * FROM `" . TBL_MGM_DOWNLOAD . "` WHERE id = '{$id}'");
		// posts		
		$results = $wpdb->get_results("SELECT post_id FROM `" .TBL_MGM_DOWNLOAD_POST_ASSOC . "` WHERE download_id = '{$id}'");
		// store
		foreach ($results as $result) {
			$download_posts[] = $result->post_id;
		}
		// set
		$data['download_posts'] = $download_posts;
		$post_types = array('post', 'page');
		if(function_exists('get_post_types')) {
			$post_types = get_post_types(array('public' => true));
			if(in_array('attachment',$post_types))
				unset($post_types[ array_search('attachment', $post_types) ]);
		}
		$post_types = implode('\',\'', $post_types);
		// all posts
		$data['posts'] = mgm_field_values( $wpdb->posts, 'ID', 'post_title', "AND post_status = 'publish' AND post_type IN ('$post_types')", 'post_title');	
		// hook
		$data['download_hook'] = ($mgm_system->setting['download_hook'] ? $mgm_system->setting['download_hook'] : 'download');				
		$data['download_slug'] = ($mgm_system->setting['download_slug'] ? $mgm_system->setting['download_slug'] : 'download');				
		// load template view
		$this->load->template('contents/download/edit', array('data'=>$data));
	}
	
	// delete
	function download_delete(){
		global $wpdb;	
		extract($_POST);		
		// download		
		$filename = $wpdb->get_var("SELECT filename FROM `" . TBL_MGM_DOWNLOAD . "`	WHERE id = '{$id}'");
		// delete file
		mgm_delete_file(MGM_FILES_DOWNLOAD_DIR . basename($filename));
		// delete		
		$wpdb->query('DELETE FROM `' . TBL_MGM_DOWNLOAD . '`	WHERE id = ' . $id);
		// return response		
		echo json_encode(array('status'=>'success', 'message'=>__('Download deleted Successfully','mgm')));			
		exit();
	}
	
	// download_file_upload
	function download_file_upload(){
		// init
		$download_file = array();
		// init messages
		$status  = 'error';	
		$message = 'file upload failed';
		// upload check
		if (is_uploaded_file($_FILES['download_file']['tmp_name'])) {
			// real name
			$realname = $_FILES['download_file']['name'];  
			// random filename
			$uniquename = substr(microtime(),2,8);
			// paths
			$oldname = strtolower($realname);
			$newname = preg_replace('/(.*)\.(.*)$/i', $uniquename.'.$2', $oldname);
			// keep file name
			// $realname = wp_unique_filename(MGM_FILES_DOWNLOAD_DIR, $realname);	
			// path		
			$filepath = MGM_FILES_DOWNLOAD_DIR . $newname;
			// extended server configurations:
			// should move to htaccess/php.ini
			ini_set('max_execution_time', 	'3600');
			ini_set('upload_max_filesize', 	'1000M');
			ini_set('post_max_size', 		'1000M');			
			// upload
			if(move_uploaded_file($_FILES['download_file']['tmp_name'], $filepath)){	
				// permission
				chmod($filepath, 0755);			
				// set download_file				
				$download_file  = array('file_name' => $newname, 'file_url' => MGM_FILES_DOWNLOAD_URL . $newname, 'real_name' => $realname);					
				// status
				$status  = 'success';	
				$message = 'file uploaded successfully, it will be attached when you save the data.';
			}
		}		

		// send ouput		
		ob_end_clean();	
		echo json_encode(array('status'=>$status,'message'=>__($message,'mgm'), 'download_file'=>$download_file));
		// end out put			
		ob_flush();
		exit();
	}
	
	// page : excludes
	function page(){		
		global $wpdb;	
		extract($_POST);
		// set 
		if(isset($update) && !empty($update)){
			// get system object	
			$system = mgm_get_class('system');
			// update			
			$system->setting['excluded_pages'] = $_POST['excluded_pages'];								
			// update
			// update_option('mgm_system', $system);
			$system->save();
			// update
			$message = __('Content access settings successfully updated.', 'mgm');
			$status  = 'success';
			// return response			
			echo json_encode(array('status'=>$status, 'message'=>$message));				
			exit();
		}
		// data
		$data = array();	
		// all posts
		$data['posts'] = mgm_field_values( $wpdb->posts, 'ID', 'post_title', "AND post_status = 'publish' AND post_type IN ('page')", 'post_title' );	
		// excluded
		$data['excluded_pages'] = mgm_get_class('system')->setting['excluded_pages'];
		// load template view
		$this->load->template('contents/page', array('data'=>$data));
	}
	
	// userfields tab
	function userfields(){		
		global $wpdb;	
		// data
		$data = array();				
		// load template view
		$this->load->template('contents/userfields/index', array('data'=>$data));
	}
	
	// userfields list
	function userfields_list(){
		global $wpdb;	
		// data
		$data = array();	
		// coupons		
	    $data['custom_fields'] = mgm_get_class('member_custom_fields');	
		// log
		// mgm_log(print_r($data['custom_fields'],1));			
		// load template view
		$this->load->template('contents/userfields/list', array('data'=>$data));		
	}
	
	// userfields add
	function userfields_add(){	
		global $wpdb;	
		extract($_POST);
		// get object
		$custom_fields = mgm_get_class('member_custom_fields');	
		// save
		if(isset($save_fields) && !empty($save_fields)){
			// init
			$custom_field  =  array();		
			// set new
			$custom_field['id']           = $custom_fields->next_id;
			// name
			$custom_field['name']         = (isset($name) && !empty($name)) ? mgm_create_slug($name, 50) : mgm_create_slug($label,50);       
			// label
			$custom_field['label']        = __($label,'mgm');
			// type
			$custom_field['type']         = $type;
			// system defined
			$custom_field['system']       = false;// custom added
			// value
			$custom_field['value']        = (isset($value))? $value : '';
			// has options
			$custom_field['options']      = (isset($options)) ? $options : false;
			
			// display
			$display                      = array();			
			// on register page
			$display['on_register']       = (isset($on_register)) ? $on_register : false;	
			// on profile page
			$display['on_profile']        = (isset($on_profile)) ? $on_profile : false;	
			// on payment page
			$display['on_payment']        = (isset($on_payment)) ? $on_payment : false;	
			// on public profile page
			$display['on_public_profile'] = (isset($on_public_profile)) ? $on_public_profile : false;				
			// set 
			$custom_field['display']      = $display;
			
			// attributes
			$attributes                 = array();	
			// required field
			$attributes['required']     = (isset($required)) ? $required : false;
			// read only
			$attributes['readonly']     = (isset($readonly)) ? $readonly : false;	
			// hide label
			$attributes['hide_label']   = (isset($hide_label)) ? $hide_label : false;	
			// set 
			$custom_field['attributes'] = $attributes;
			
			//duplicate check:			
			if($custom_fields->is_duplicate(strtolower($custom_field['name']))) {
				// messgae
				$message = sprintf(__('Sorry, the field name should be unique, please try a different name', 'mgm'));
				$status  = 'error';
			}else {
				// set fields
				$success = $custom_fields->set_custom_field($custom_field);					 								 
				// saved
				if ($success) {
					// update on success
					// update_option('mgm_member_custom_fields',$custom_fields);	
					$custom_fields->save();			
					// message
					$message = sprintf(__('Successfully created new user field: %s', 'mgm'),  mgm_stripslashes_deep($label));
					$status  = 'success';
				}else{
					// messgae
					$message = sprintf(__('Error while creating new user field: %s', 'mgm'),  mgm_stripslashes_deep($label));
					$status  = 'error';
				}
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}	
		
		// data
		$data = array();
		// types
		$data['input_types'] = array('text'=>__('Text Field (Single Line)','mgm'),'textarea'=>__('Text Area (Multi-Line)','mgm'),
		                             'html'=>__('Html','mgm'),'password'=>__('Password','mgm'),'select'=>__('Select (Drop down box)','mgm'),
									 'checkbox'=>__('Checkbox','mgm'),'radio'=>__('Radio','mgm'),'hidden'=>__('Hidden Field (Set value)','mgm'),
									 'label'=>__('Label','mgm'),'image'=>__('Image','mgm'), 'captcha' => __('Captcha', 'mgm'));		
		// load template view
		$this->load->template('contents/userfields/add', array('data'=>$data));		
	}
	
	// userfields edit
	function userfields_edit(){	
		global $wpdb;	
		extract($_POST);
		
		// save
		if(isset($save_fields) && !empty($save_fields)){
			// get object
			$custom_fields = mgm_get_class('member_custom_fields');					
			// init
			$custom_field  = $custom_fields->get_field($id);					
			// name
			$custom_field['name']         = (isset($name) && !empty($name)) ? mgm_create_slug($name, 50) : mgm_create_slug($label,50);        
			// label
			$custom_field['label']        = __($label,'mgm');
			// type
			$custom_field['type']         = $type;
			// system defined
			$custom_field['system']       = $system;// should not update this			
			// value
			$custom_field['value']        = $value;
			// has options
			$custom_field['options']      = (isset($options)) ? $options : false;
						
			// display
			$display                      = array();			
			// on register page
			$display['on_register']       = (isset($on_register)) ? $on_register : false;	
			// on profile page
			$display['on_profile']        = (isset($on_profile)) ? $on_profile : false;	
			// on payment page
			$display['on_payment']        = (isset($on_payment)) ? $on_payment : false;	
			// on public profile page
			$display['on_public_profile'] = (isset($on_public_profile)) ? $on_public_profile : false;	
			// coupon
			if($name == 'coupon'){
				// on upgrade page
				$display['on_upgrade'] = (isset($on_upgrade)) ? $on_upgrade : false;	
				// on extend page
				$display['on_extend']  = (isset($on_extend)) ? $on_extend : false;	
				// on postpurchase page
				$display['on_postpurchase']  = (isset($on_postpurchase)) ? $on_postpurchase : false;	
			}				
			// set 
			$custom_field['display']      = $display;
			
			// attributes
			$attributes                 = array();	
			// required field
			$attributes['required']     = (isset($required)) ? $required : false;
			// read only
			$attributes['readonly']     = (isset($readonly)) ? $readonly : false;	
			// hide label
			$attributes['hide_label']   = (isset($hide_label)) ? $hide_label : false;	
			// set 
			$custom_field['attributes'] = $attributes;
			
			//duplicate check:	
			if($custom_fields->is_duplicate(strtolower($custom_field['name']), $id)) {
				// messgae
				$message = sprintf(__('Sorry, the field name should be unique, please try a different name', 'mgm'));
				$status  = 'error';
			}else {
				// set
				$success = $custom_fields->set_custom_field($custom_field, $id);					 								 
				// saved
				if ($success) {
					// update on success
					// update_option('mgm_member_custom_fields',$custom_fields);	
					$custom_fields->save();	
					// also update template
					// default subscription_introduction
					if($name == 'subscription_introduction'){
						// value
						if(isset($value)){
							mgm_get_class('system')->set_template('subs_intro', $value);	
						}
					}
					// default terms_conditions
					if($name == 'terms_conditions'){
						// value
						if(isset($value)){
							mgm_get_class('system')->set_template('tos', $value);	
						}
					}													
					// message
					$message = sprintf(__('Successfully updated user field: %s', 'mgm'),  mgm_stripslashes_deep($label));
					$status  = 'success';
				}else{
					// messgae
					$message = sprintf(__('Error while updating user field: %s', 'mgm'),  mgm_stripslashes_deep($label));
					$status  = 'error';
				}
			}
			// return response
			
			echo json_encode(array('status'=>$status, 'message'=>$message));
				
			exit();
		}	
		
		// data
		$data = array();
		// types
		$data['input_types'] = array('text'=>__('Text Field (Single Line)','mgm'),'textarea'=>__('Text Area (Multi-Line)','mgm'),
		                             'html'=>__('Html','mgm'),'password'=>__('Password','mgm'),'select'=>__('Select (Drop down box)','mgm'),
									 'checkbox'=>__('Checkbox','mgm'),'radio'=>__('Radio','mgm'),'hidden'=>__('Hidden Field (Set value)','mgm'),
									 'label'=>__('Label','mgm'),'image'=>__('Image','mgm'), 'captcha' => __('Captcha', 'mgm'));
		// get field
		$data['custom_field'] = mgm_get_class('member_custom_fields')->get_field($id);	
		
		// default subscription_introduction
		if($data['custom_field']['name'] == 'subscription_introduction'){
			// no value
			if(empty($data['custom_field']['value'])){
				$data['custom_field']['value'] = mgm_print_template_content('subs_intro');
			}
		}
		// default terms_conditions
		if($data['custom_field']['name'] == 'terms_conditions'){
			// no value
			if(empty($data['custom_field']['value'])){
				$data['custom_field']['value'] = mgm_print_template_content('tos');
			}
		}									 
		// load template view
		$this->load->template('contents/userfields/edit', array('data'=>$data));		
	}
	
	// userfields status_change
	function userfields_status_change(){
		extract($_POST);
		// get object
		$custom_fields = mgm_get_class('member_custom_fields');
		// update
		if($active =='Y'){
			$success = $custom_fields->set_sort_order($id);
			$w       = 'activated';			
		}else{
			$success = $custom_fields->unset_sort_order($id);
			$w       = 'deactivated';
		}		
		// label
		$label = $custom_fields->get_field_attr($id,'label');
		// send status
		if ($success) {
			// update on success
			// update_option('mgm_member_custom_fields',$custom_fields);
			$custom_fields->save();
			// message
			$message = sprintf(__('Successfully %s user field: %s', 'mgm'), $w, mgm_stripslashes_deep($label));
			$status  = 'success';
		}else{
			// message
			$message = sprintf(__('Error while %s user field: %s', 'mgm'), str_replace('ed','ing',$w), mgm_stripslashes_deep($label));
			$status  = 'error';
		}
		// return response
		
		echo json_encode(array('status'=>$status, 'message'=>$message));
			
		exit();	
	}
	
	// userfields sort
	function userfields_sort(){
		extract($_POST);
		// parse
		parse_str($sort_order, $sort);
		// new
		$new_sort_orders = $sort['active_userfield_row'];		
		// object
		$custom_fields = mgm_get_class('member_custom_fields');
		// set sort
		$custom_fields->set_sort_orders($new_sort_orders);
		// update
		// update_option('mgm_member_custom_fields',$custom_fields);
		$custom_fields->save();
		// check
		$message = __('Successfully sorted user fields', 'mgm');
		$status  = 'success';
		// return response
		
		echo json_encode(array('status'=>$status, 'message'=>$message));
			
		exit();	
	}
	
	// userfields delete
	function userfields_delete(){
		extract($_POST);		
		// object
		$custom_fields = mgm_get_class('member_custom_fields');
		// label
		$label = $custom_fields->get_field_attr($id,'label');
		// set sort
		$success = $custom_fields->unset_custom_field($id);
		// success
		if($success) {
			// update on success
			// update_option('mgm_member_custom_fields',$custom_fields);
			$custom_fields->save();
			// message
			$message = sprintf(__('Successfully removed user field: %s', 'mgm'), mgm_stripslashes_deep($label));
			$status  = 'success';
		}else{
			// message
			$message = sprintf(__('Error while removing user field: %s', 'mgm'), mgm_stripslashes_deep($label));
			$status  = 'error';
		}			
		// return response
		
		echo json_encode(array('status'=>$status, 'message'=>$message));
			
		exit();	
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_contents.php 