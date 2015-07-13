<?php
/**
 * Magic Members admin tools module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_tools extends mgm_controller{
 	
	// construct
	function mgm_tools()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();
		// load template view
		$this->load->template('tools/index', array('data'=>$data));		
	}
	
	// data migrate
	function data_migrate(){				
		global $wpdb;	
		// local
		extract($_POST);
				
		// execute
		if(isset($migrate_execute) && !empty($migrate_execute)){
			// execute
			//ob_end_clean();
			echo $this->_do_data_migrate();			
			//ob_flush();
			// exit
			exit();
		}
		
		// data
		$data = array();
		// system
		$data['system'] 		= mgm_get_class('system');
		$data['filetypes'] 		= array('csv' => 'CSV','xls' => 'XLS');
		$data['import_limit'] 	= 2000;
		// load template view
		$this->load->template('tools/data_migrate', array('data'=>$data));		
	}
	
	// upload process for imports
	function import_file_upload(){		
		// file
		$file_element = 'import_file';
		// init
		$filedata = array();
		// init messages
		$status  = 'error';	
		$message = __('import file upload failed.','mgm');
		// upload check
		if (is_uploaded_file($_FILES[$file_element]['tmp_name'])) {
			// random filename
			$uniquename = substr(microtime(),2,8);
			// paths
			$oldname    = strtolower($_FILES[$file_element]['name']);
			$newname    = preg_replace('/(.*)\.(xml)$/i', $uniquename.'.$2', $oldname);
			$filepath   = MGM_FILES_EXPORT_DIR . $newname;
			// upload
			if(move_uploaded_file($_FILES[$file_element]['tmp_name'], $filepath)){
				// file				
				$import_file  = array('name' => $newname, 'path' => MGM_FILES_EXPORT_DIR . $newname);	
				// status
				$status  = 'success';	
				$message = sprintf(__('Import file [%s] uploaded successfully, please hit the MIGRATE button to start migration.','mgm'),$newname);
			}
		}		
		// send ouput		
		ob_end_clean();	
		echo json_encode(array('status'=>$status,'message'=>$message, 'file'=>$import_file,'post'=>$_POST));
		// end out put			
		ob_flush();
		exit();
	}
	
	// import users upload
	function importusers_file_upload() {
		// file
		$file_element = 'import_users';
		// init
		$filedata = array();
		// init messages
		$status  = 'error';	
		$newname = '';
		$message = __('import file upload failed.','mgm');
		// upload check
		if (is_uploaded_file($_FILES[$file_element]['tmp_name'])) {
			// random filename
			$uniquename = substr(microtime(),2,8);
			// paths
			$oldname    = strtolower($_FILES[$file_element]['name']);
			$newname    = preg_replace('/(.*)\.(csv|xml)$/i', $uniquename.'.$2', $oldname);
			$filepath   = MGM_FILES_EXPORT_DIR . $newname;
			// upload
			if(move_uploaded_file($_FILES[$file_element]['tmp_name'], $filepath)){
				chmod($filepath,0775);
				// file				
				$import_file  = array('name' => $newname, 'path' => $newname);	
				// status
				$status  = 'success';	
				$message = sprintf(__('Import file [%s] uploaded successfully, please hit the Import Users button to start import.','mgm'),$newname);
			}
		}		
		// send ouput		
		ob_end_clean();	
		echo json_encode(array('status'=>$status,'message'=>$message, 'file'=>$import_file,'post'=>$_POST));
		// end out put			
		ob_flush();
		exit();
	}	
	
	// core setup
	function core_setup(){	
		global $wpdb;	
		// local
		extract($_POST);
				
		// execute
		if(isset($core_setup_execute) && !empty($core_setup_execute)){
			// switch
			if($core_setup_execute=='core_switch'){
				// execute
				echo $this->_do_core_switch();
			}else if($core_setup_execute=='core_env'){
			// environment
				echo $this->_do_core_environment();
			}
			// exit
			exit();
		}			
		
		// data
		$data = array();			
		// load template view
		$this->load->template('tools/core_setup', array('data'=>$data));	
	}		
	
	// upgrade
	function upgrade(){	
		global $wpdb;	
		// local
		extract($_POST);				
		// execute
		if(isset($upgrade_execute) && !empty($upgrade_execute)){
			// execute
			echo $this->_do_upgrade();
			// exit
			exit();
		}		
		// data
		$data = array();
		// load template view
		$this->load->template('tools/upgrade', array('data'=>$data));		
	}	
	
	// system_reset
	function system_reset(){		
		global $wpdb;	
		// local
		extract($_POST);
				
		// execute
		if(isset($reset_execute) && !empty($reset_execute)){
			// execute
			echo $this->_do_system_reset();
			// exit
			exit();
		}
		
		// data
		$data = array();
		// load template view
		$this->load->template('tools/system_reset', array('data'=>$data));		
	}
	
	// logs
	function logs(){
		global $wpdb;
		// data
		$data = array();
		// get transaction logs
		$data['transactions_logs'] = $wpdb->get_results("SELECT * FROM `".TBL_MGM_TRANSACTION."` ORDER BY `transaction_dt` DESC LIMIT 0, 20");
		// get api logs
		$data['api_logs'] = $wpdb->get_results("SELECT * FROM `".TBL_MGM_REST_API_LOG."` ORDER BY `create_dt` DESC LIMIT 0, 20");
		// load template view
		$this->load->template('tools/logs', array('data'=>$data));
	}
	
	// PRIVATE -------------------------------------------------------------------
	// do system reset
	function _do_system_reset(){
		global $wpdb, $mgm_init;
		extract($_POST);
		
		// track
		$status   = 'error';
		$message  = __('Reset failed', 'mgm'); 
		$redirect = '';
		// take option
		switch($reset_type){
			case 'settntable':
				// user meta			
				$wpdb->query("DELETE FROM " . $wpdb->usermeta . " WHERE `meta_key` LIKE 'mgm_%' ");
				// post meta				
				$wpdb->query("DELETE FROM " . $wpdb->postmeta . " WHERE `meta_key` LIKE '_mgm_%' ");
				// tables
				$tables = $mgm_init->_get_tables();				
				// loop tables
				foreach( $tables as $table ){
					// do not clear countries table
					if($table == $wpdb->prefix . 'mgm_countries' )
						continue;
					// truncate	 
					$wpdb->query('TRUNCATE ' . $table );
				}
				// set messages
				$status   = 'success';
				$message  = __('Settings and Table reset completed successfully.', 'mgm');			
			case 'settonly':
				// option meta
				$wpdb->query("DELETE FROM " . $wpdb->options . " WHERE `option_name` LIKE 'mgm_%' AND option_name NOT IN('mgm_version','mgm_upgrade_id','mgm_auth')");				
				// set messages
				if($reset_type == 'settonly'){
					$status  = 'success';
					$message = __('Settings reset completed successfully.', 'mgm');
				}
			break;			
			case 'fullreset':
				// plugin basename
				$plugin = rtrim(MGM_PLUGIN_NAME, '/');
				// if active
				if (is_plugin_active($plugin)) {
					// post meta explicitly				
					$wpdb->query("DELETE FROM " . $wpdb->postmeta . " WHERE `meta_key` LIKE '_mgm_%' ");
					// deactivate first
					deactivate_plugins($plugin, true);
					// remove all
					$mgm_init->deactivate(true); 	
					// send deactivation
					mgm_get_class('auth')->notify_deactivation();
					// sleep 2 sec
					sleep(2);
					// redirect
					$status   = 'success';
					$message  = __('Plugin deactivated successfully. You will be redirected to Plugin page.', 'mgm');
					$redirect = 'plugins.php?deactivate=true&plugin_status=active&paged=1'; 
				}else{
					$status  = 'error';
					$message = __('Plugin already deactivated','mgm');
				}				
			break;
		}
					
		// response
		return json_encode(array('status'=>$status, 'message'=>$message, 'redirect'=>$redirect));		
	}
	
	// data migrate: unfinished
	function _do_data_migrate() {
		global $wpdb;	
		// local
		extract($_POST);		
		
		// track
		$status  = 'error';
		$message = __('Migration failed. ', 'mgm'); 
		$export  = array();
		
		// update
		if(isset($migrate_execute) && !empty($migrate_execute)){
			// type
			switch($migrate_type){
				case 'import':
					// import
					if($this->_do_import()){
						// status
						$status  = 'success';
						$message = __('Migration completed successfully.', 'mgm');
					}
				break;
				case 'export':
					// get
					if($file = $this->_do_export()){						// 
						// export
						$export = array('download_url'=>admin_url('admin.php?page=mgm/admin/files&type=download&file='.urlencode($file)));
						// status
						$status = 'success';
						$message = __('Migration completed successfully.', 'mgm');
					}else{
						// error
						$message .= __('Export file creation failed.','mgm');
					}
				break;	
				case 'import_users':			
					$response = $this->_do_import_users();					
					if(isset($response['status']) && $response['status'] == true ) {
						$status = 'success';	
						$message = __('Import completed successfully.', 'mgm');
					}else 
						$message = isset($response['error']) ? $response['error'] : __('Error while importing.', 'mgm');				
				break;
				//check import is completed 
				//If the a lot of records are there, then the server might not respond prperly tp the ajax request
				//So This will check the uploaded files exists or not
				//File exists means import is being done
				case 'import_status':
					$status  = 'success';
					$message = '';	
					$timeout = 5;//in seconds
					$limit = 900;					
					sleep($timeout);				
					if(isset($import_users) && file_exists(MGM_FILES_EXPORT_DIR . $import_users)) {						
						$status  = 'incomplete';						
						$message = '';
						if(isset($retry)) {
							$time_elapsed = $retry * $timeout;
							if($time_elapsed >= $limit) {
								$status  = 'error';
								$message = __('Server returned an empty response. Please check whether the users got imported.');	
							}
						}
					}else {
						$message = __('Import completed successfully.', 'mgm');
					}
					
				break;	
			}			
		}
		// response
		return json_encode(array_merge(array('status'=>$status, 'message'=>$message),$export));		
	}	
	
	// import
	function _do_import(){
		global $wpdb;	
		// local
		extract($_POST);
		// xml
		$xml = simplexml_load_file($import_file);	
		// dump
		// mgm_array_dump($xml);
		return false;	
	}
	
	// import users
	function _do_import_users() {		
		ini_set('html_errors', 0);
		ini_set('log_errors',1);
		ini_set('error_log', MGM_FILES_LOG_DIR . 'error_log.txt');
		ini_set('display_errors', 0);		
		ini_set('memory_limit', '512M');		
		set_time_limit(900); //15 minutes
		extract($_POST);	
		
		//test 
		global $wpdb; 
		$header = array();
		$users = array();
		$response = array('status' => false);
		$continue = false;		
		$file_info = pathinfo(MGM_FILES_EXPORT_DIR . $import_users);								
		
		$row_limit = 2000;
		$user_count = 0;
		//enable forced gc
		if(function_exists('gc_enable'))
			gc_enable();
		
		//mgm_log('IMPORT MEMORY PEAK1: ' . memory_get_peak_usage(true)/(1024*1024));

		switch (strtolower($file_info['extension'])) {
			//CSV 
			case 'csv':				
				$handle = fopen(MGM_FILES_EXPORT_DIR . $import_users,'r');
				while( $data = fgetcsv($handle,null,',')) {
					//get headers:
					if(empty($header)) {
						$header = $data; 
					}else {
						//$user_count++;
						//update rowws for empty cells:	
						$row = array();						
						foreach ($header as $key => $val) {									
							//create an array with header value as index:									
							$row[$val]	 = (!isset($data[$key])) ? '' : trim($data[$key]);
						}
						$users[] = 	$row;
						$row = null;
						unset($row);
						//check limit reached:
						//if(($user_count+1) >= $row_limit)
						//	break;						
					}
					
					$data = null;
					unset($data);					
				}
				@fclose($handle);
				$continue = true;
				break;
				
			//XLS Parsing:	
			case 'xls':
				$obj_xls = new Spreadsheet_Excel_Reader();
				$obj_xls->setOutputEncoding('CP1251');				
				$obj_xls->read(MGM_FILES_EXPORT_DIR . $import_users);
				
				if(!empty($obj_xls->sheets)) {					
					for ($i = 1; $i <= $obj_xls->sheets[0]['numRows']; $i++) {						
						if(empty($header))
							$header = $obj_xls->sheets[0]['cells'][$i];
						else {
							//$user_count++;
							//update rowws for empty cells:	
							$row = array();						
							foreach ($header as $key => $val) {									
								//create an array with header value as index:									
								//$row[$val]	 = (!isset($obj_xls->sheets[0]['cells'][$i][$key]))?null:trim($obj_xls->sheets[0]['cells'][$i][$key]);
								$row[$val]	 = (!isset($obj_xls->sheets[0]['cells'][$i][$key])) ? null : trim($obj_xls->sheets[0]['cells'][$i][$key]);
							}
							$users[] = 	$row;	
							$row = null;
							unset($row);
							//check limit reached:
							//if(($user_count+1) >= $row_limit)
							//	break;						
						}
						
						//check here:	
						$obj_xls->sheets[0]['cells'][$i] = null;
						unset($obj_xls->sheets[0]['cells'][$i]);
						if(function_exists('gc_collect_cycles'))						
							gc_collect_cycles();					
					}
					//reindex header once done:
					$header = array_values($header);
					$continue = true;
				}
				$obj_xls = null;
				unset($obj_xls);
				break;	
			default:
				$response['error'] = __('Please upload CSV/XLS file', 'mgm');	
				break;	
		}	
		
		$file_info = null;
		unset($file_info);	

//		mgm_log('IMPORT MEMORY PEAK2: ' . memory_get_peak_usage(true)/(1024*1024));
		
		//process data:		
		if(!empty($header) && in_array('user_email', $header) && $continue) {	
			
			if(!empty($users)) {
				$row_count = count($users);
				$col_count = count($header);
				$packs_obj = mgm_get_class('mgm_subscription_packs');												
				$arr_user_fields = array(	'first_name', 'last_name', 'user_nicename', 'user_url', 'display_name', 'nickname', 
											'user_firstname', 'user_lastname', 'user_description');
				
				$obj_role = mgm_get_class('mgm_roles');	
				$update_count = 0;	
				//reset user count
				$user_count = 0;
				$arr_new_users = array();	
				$arr_specialchar = array(',','\'','"',"\n\r","\n",'\\','/','$','`','(',')',' '," ");
				$membershiptypes = mgm_get_class('membership_types')->get_membership_types();
				//custom fields
				$cf_register_page = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_register'=>true, 'on_profile'=>true)));
				$cf_count = count($cf_register_page);
				$cf_exclude_names = array('subscription_introduction','subscription_options','terms_conditions','privacy_policy','description','payment_gateways','password_conf','autoresponder');
				$cf_exclude_types = array('html', 'label', 'captcha');
				for($i = 0; $i < $row_count; $i++) {
					//remove N/A
					$users[$i] = str_ireplace('N/A', '', $users[$i]);
					
					$user_count++;
					$update 				= false;
					$new_user				= false;
					$is_membership_update 	= false;
					$is_multiple_membership_update = false;
					$multiple_membership_exists = false;
					$update_role = false;
					$pack 		 = array();
					 
					$id 				=  (isset($users[$i]['ID']) && is_numeric($users[$i]['ID'])) ? $users[$i]['ID'] : ''; 					
					$email 				=  str_replace($arr_specialchar, '', sanitize_email($users[$i]['user_email'])); 
					$user_login 		=  str_replace($arr_specialchar, '', sanitize_user($users[$i]['user_login'])); 
					$user_password 		=  isset($users[$i]['user_password']) ? $users[$i]['user_password'] : ''; 
					$membership_type 	=  str_replace($arr_specialchar, '', $users[$i]['membership_type']); 
					$pack_id 			=  sanitize_user($users[$i]['pack_id']); 																			
										
					if(!is_numeric($id)) {						
						if(!empty($user_login) && !empty($email)) {									
							$arr_user = get_userdatabylogin($user_login);							
							//if update and different email
							if(isset($arr_user->ID) && $arr_user->user_email != $email) {								
								continue;
							}	
							//fresh insert/registration:	
							if(!$arr_user) {								
								$user_password = (!empty($user_password)) ? $user_password : wp_generate_password();
								$user_password = str_replace($arr_specialchar, '', $user_password);															
								$id = wp_create_user( $user_login, $user_password, $email );								
								
								if(is_wp_error($id)) {	
									unset($id);																	
									continue;		
								}						
																
								$arr_new_users[$id]['email'] = $email; 
								$arr_new_users[$id]['user_login'] = $user_login; 
									
								update_user_option( $id, 'default_password_nag', true, true );								
								$new_user				= true;
							}else 
								$id = $arr_user->ID;	
							
							//check here:
							$arr_user = null;
							unset($arr_user);
																
						}else {							
							continue;//skip the record
						}
					}else 	
						$update = true;	
										
					//get User object:
					$user = new WP_user($id);																						
					if(isset($user->ID) && $user->ID > 0 ) {						
						//get mgm object:									
						$mgm_member = mgm_get_member($user->ID);	
						//update custom fields:
						if(!empty($mgm_member)) {							
							//foreach ($mgm_member->custom_fields as $key => $value) {								
							if($cf_count > 0) {
								foreach ($cf_register_page as $field) {	
									$key = $field['name'];							
									//skip unwanted fields
									if(in_array($key, $cf_exclude_names) || in_array($field['type'], $cf_exclude_types)) {										
										continue;
									}
									$val = '';
									if(isset($users[$i][$key]) && !empty($users[$i][$key]) && preg_match('/date/i', $key)) {										
										//validate date
										if(mgm_is_valid_date($users[$i][$key]) && mgm_format_shortdate_to_mysql($users[$i][$key])) {
											//$users[$i][$key] = $mysql_date;
											$val = $users[$i][$key];
										}						
									}
									//email and username custom fields
									elseif($key == 'email') {
										$val = $email;
									}elseif ($key == 'username') {
										$val = $user_login;
									}elseif ($key == 'password') {
										if(empty($user_password)) {											
											continue;
										}
										$val = $user_password;
									}else
										$val = isset($users[$i][$key]) ? $users[$i][$key] : '' ;
										
									//update fields:
									if(!empty($val) || !isset($mgm_member->custom_fields->$key))
										$mgm_member->custom_fields->$key = $val;
									
									unset($val);	
									$field = null;	
									unset($field);		
								}
							}
							
							//update membership: main mgm_member object
							if(!empty($membership_type) && is_numeric($pack_id)) {								
								//pack
								if($pack = $packs_obj->get_pack($pack_id)) {
									$mgm_member->pack_id = $pack_id;								
								}else {
									//error:																		
									continue;
								}	
								
								//membership types:
								$sel_type = '';
								foreach ($membershiptypes as $key => $type) {
									if($membership_type == $key || $membership_type == $type) {
										$sel_type = $key;  
										break;
									}
								}
								if(!empty($sel_type))
									$membership_type = $sel_type;
								else { 									
									continue;
								}
										
								//if($mgm_member->membership_type != $membership_type || strtolower($mgm_member->membership_type) == 'guest' || in_array($pack['membership_type'], array('free', 'trial')) ) {									
								//to distinguish between primary membership and other membership(Y/N)
								if( !isset($users[$i]['other_membership']) || (isset($users[$i]['other_membership']) && $users[$i]['other_membership'] != 'Y' ) ) {									
									$mgm_member->membership_type = $membership_type;
									//update current membership:
									$arr_reponse = $this->_update_member_object($mgm_member, $pack,  $users[$i]);
									if(!$arr_reponse['status']) {
										//skip the row:
										//update errors:
										//$arr_reponse['error']																			
										continue;
									}
									
									$mgm_member = $arr_reponse['mgm_member'];									
									if(strtolower($mgm_member->membership_type) == 'guest')
										$mgm_member->other_membership_types = array();	
									else 
									    $update_role = true;										  																
								}else {									
									//if multiple mgm_member object:
									if(isset($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types)) {										
										$multiple_updated = false;
										foreach ((array) $mgm_member->other_membership_types as $key => $member ) {
											$member = mgm_convert_array_to_memberobj($member, $user->ID);
											if($member->membership_type == $membership_type) {
												$arr_reponse = $this->_update_member_object($member, $pack,  $users[$i]);
												if(!$arr_reponse['status']) {
													//skip the row:
													//update errors:
													//$arr_reponse['error']																										
													continue;
												}	
												//make sure array is saved:
												$arr_reponse['mgm_member'] = mgm_convert_memberobj_to_array($arr_reponse['mgm_member']);											
												$mgm_member->other_membership_types[$key] = $arr_reponse['mgm_member'];
												$multiple_updated = true;
												break;
											}
										}
										//add new to mother_membership_types object:
										if(!$multiple_updated) {
											$arr_reponse = $this->_update_member_object( new stdClass, $pack,  $users[$i]);
											if(!$arr_reponse['status']) {
												//skip the row:
												//update errors:
												//$arr_reponse['error']																								
												continue;
											}
											$arr_reponse['mgm_member'] = mgm_convert_memberobj_to_array($arr_reponse['mgm_member']);											    
											$mgm_member->other_membership_types[] = $arr_reponse['mgm_member'];
											$update_role = true;											
										}
									}									
								}
							}
							
							//update misc fields:
							if(!isset($mgm_member->rss_token) || (isset($mgm_member->rss_token) && empty($mgm_member->rss_token)))
								$mgm_member->rss_token = mgm_create_rss_token();
							//payment type:
							if(!isset($mgm_member->payment_type) || (isset($mgm_member->payment_type) && empty($mgm_member->payment_type)))
								$mgm_member->payment_type = 'subscription';								
							//update password:	
							if(!empty($user_password)) {
								$mgm_member->user_password = $user_password;
								$user_password_md5 = md5($user_password);	
								// db update
								$wpdb->query( sprintf("UPDATE `%s` SET `user_pass` = '%s' WHERE ID = '%d'",
														$wpdb->users, $user_password_md5, $user->ID
														)); 	
								if($new_user)
									$arr_new_users[$id]['user_password'] = $user_password;	
							}							
							//save mgm_member object:
							// update_user_option($id, 'mgm_member', $mgm_member, true);
							$mgm_member->save();														
							//update role:
							if($update_role) {									
						 		//update role/change order		 						
								$obj_role->add_user_role($user->ID, $pack['role']);
							}																																		
						}
						//update other user fields:
						$user_extra = array();
						foreach ($users[$i] as $key => $value) {
							if(in_array($key, $arr_user_fields) && !empty($value)) {
								//$value = str_ireplace('N/A', '', $value);
								$user_extra[$key] = $value;
							}
						}						
						if(!empty($user_extra)) {
							$user_extra['ID'] = $user->ID;
							wp_update_user($user_extra);							
						}											
						$update_count++;
						
						//check here:
						$mgm_member = null;
						unset($mgm_member);
						$user = null;
						unset($user);
						$user_extra = null;
						unset($user_extra);
					}
					
					//check limit reached:
					if($user_count >= $row_limit) {
						if($row_count > $row_limit ) {
							$response['message'] = __("(Import stopped at: $email as limit($row_limit) reached.)",'mgm');
						}
						break;				
					}
					//check here:					
					$users[$i] = null;
					unset($users[$i]);					
					if(function_exists('gc_collect_cycles'))
						gc_collect_cycles();
						
					if(!($i%25)) sleep(1);	
				}

//				mgm_log('IMPORT MEMORY PEAK2.5: ' . memory_get_peak_usage(true)/(1024*1024));
				
				//free unwanted resources
				$users = null;
				unset($users);
				$header = null;
				unset($header);
				$cf_register_page = null;
				unset($cf_register_page);
				$arr_user_fields = null;
				unset($arr_user_fields);
				$cf_exclude_names = null;
				unset($cf_exclude_names);
				$packs_obj = null;
				unset($packs_obj);
				$obj_role = null;
				unset($obj_role);
				unset($user_count);
				if(function_exists('gc_collect_cycles'))
					gc_collect_cycles();
				
				//done importing
				if($update_count) {
					$update_count = null;					
					unset($update_count);
					$response['status'] = true;
					//send admin notification:
					// send to admin 					
					if(!empty($arr_new_users)) {
						$message  = sprintf(__( '(' .count($arr_new_users) . ') New user registration on your blog %s:'), get_option('blogname')) . "<br/><br/>";
						foreach ($arr_new_users as $user_id => $new) {	
							$message .= sprintf(__('Username: %s'), $new['user_login']) . "<br/>";
							$message .= sprintf(__('E-mail: %s'), $new['email']) . "<br/>";
							$message .= "-----------------------------------<br/><br/>";
							unset($new);
							//send email to the user:
							//mgm_new_user_notification($user_id, $new['user_password'],false);
						}
						$arr_new_users = null;
						unset($arr_new_users);
						if(isset($response['message'])) {
							$message .= $response['message'];
							$message .= "-----------------------------------<br/><br/>";
						}
						//admin email:
						@mgm_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);						
						$message = null;
						unset($message);
						if(function_exists('gc_collect_cycles'))
							gc_collect_cycles();
					}										
				}else {
					$response['error'] = __('No users imported', 'mgm');
				}
			}else {
				$response['error'] = __('Empty records', 'mgm');
			}
		}else {
			$response['error'] = __('Error in processing users', 'mgm');			
		}
		//delete uploaded file:					
		if(is_readable(MGM_FILES_EXPORT_DIR . $import_users)) {
			@unlink(MGM_FILES_EXPORT_DIR . $import_users);
		}
		
//		mgm_log('IMPORT MEMORY PEAK3: ' . memory_get_peak_usage(true)/(1024*1024));
//		mgm_log('$response:' . mgm_array_dump($response, true));
		return $response;
	}
	
	//create/update mgm_member ubject
	function _update_member_object($mgm_member, $pack, $data, $insert = true) {		
		$arr_resp = array('status' => true);		
		$duration_types = array('d'=>'DAY','m'=>'MONTH','y'=>'YEAR');
		$arr_status = array(MGM_STATUS_NULL, MGM_STATUS_ACTIVE, MGM_STATUS_EXPIRED, MGM_STATUS_PENDING, 
							MGM_STATUS_TRIAL_EXPIRED, MGM_STATUS_CANCELLED, MGM_STATUS_ERROR, MGM_STATUS_AWAITING_CANCEL);
		// if trial on		
		if ($pack['trial_on']) {
			$mgm_member->trial_on            = (!empty($data['trial_on'])) ? $data['trial_on'] : (isset($mgm_member->trial_on) && $mgm_member->trial_on ? $mgm_member->trial_on : $pack['trial_on']);
			$mgm_member->trial_cost          = (!empty($data['trial_cost'])) ? $data['trial_cost'] : (isset($mgm_member->trial_cost) && $mgm_member->trial_cost ? $mgm_member->trial_cost : $pack['trial_cost']);
			$mgm_member->trial_duration      = (!empty($data['trial_duration'])) ? $data['trial_duration'] : (isset($mgm_member->trial_duration) && $mgm_member->trial_duration ? $mgm_member->trial_duration : $pack['trial_duration']);
			$mgm_member->trial_duration_type = (!empty($data['trial_duration_type'])) ? $data['trial_duration_type'] : (isset($mgm_member->trial_duration_type) && $mgm_member->trial_duration_type ? $mgm_member->trial_duration_type : $pack['trial_duration_type']);
			$mgm_member->trial_num_cycles    = (!empty($data['trial_num_cycles'])) ? $data['trial_num_cycles'] : (isset($mgm_member->trial_num_cycles) ? $mgm_member->trial_num_cycles : $pack['trial_num_cycles']);
		}		
		// duration
		if(!empty($data['duration'])) {			
			if(is_numeric($data['duration'])) {
				$mgm_member->duration        = $data['duration'];				
			}else {				
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Duration', 'mgm');
			}
		}elseif($insert) {			
			$mgm_member->duration        = $pack['duration'];
		}
		//duration type:
		if(!empty($data['duration_type'])) {			
			if(in_array($data['duration_type'], array('d','m','y','l'))){				
				$mgm_member->duration_type   =  $data['duration_type'];
			}else {				
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Duration Type', 'mgm');
			}
		}elseif ($insert) {			
			$mgm_member->duration_type   = $pack['duration_type'];
		}
		//duration type:
		if(!empty($data['amount'])) {			
			if(is_numeric($data['amount'])) {				
				$mgm_member->amount   =  number_format($data['amount'],2,'.','');
			}else {				
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Amount', 'mgm');
			}
		}elseif ($insert) {			
			$mgm_member->amount   = $pack['cost'];
		}											
		//amount:
		if(!empty($data['hide_old_content'])) {
			$mgm_member->hide_old_content   =  $data['hide_old_content'];
		}elseif ($insert) {
			$mgm_member->hide_old_content   = $pack['hide_old_content'];
		}	
		//$mgm_member->currency        = (!empty($data['currency'])) ? $data['currency'] : $system->setting['currency'];		
		$mgm_member->membership_type = $data['membership_type'];	
		//status
		if(!empty($data['status'])) {
			if(in_array($data['status'], $arr_status))
				$mgm_member->status   =  $data['status'];
			else {
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Status', 'mgm');
			}	
		}elseif ($insert) {
			//to prevent updating active/expired user status
			//if(isset($mgm_member->status) && !in_array($mgm_member->status, array(MGM_STATUS_ACTIVE, MGM_STATUS_EXPIRED)))
			$mgm_member->status   = MGM_STATUS_ACTIVE;
		}	
		
		if(!empty($data['status_str'])) {
			$mgm_member->status_str   =  $data['status_str'];
		}elseif ($insert) {
			$mgm_member->status_str   =  __('Last payment was successful','mgm');
		}			
		//join date:
	
		if (!empty($data['join_date'])) {
			if(mgm_is_valid_date($data['join_date']) && $mysql_date = mgm_format_shortdate_to_mysql($data['join_date'])) {
				$mgm_member->join_date = strtotime($mysql_date);
			}else {
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Joining Date', 'mgm');
			}
		}elseif($insert) {
			$mgm_member->join_date = strtotime('now');			
		}
			
		//last pay date:
		if (!empty($data['last_pay_date'])) {
			if(mgm_is_valid_date($data['last_pay_date']) && $mysql_date = mgm_format_shortdate_to_mysql($data['last_pay_date'])) {				
				$mgm_member->last_pay_date = $mysql_date;
			}else {
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Last Pay Date', 'mgm');
			}				
		}elseif($insert) {			
			$mgm_member->last_pay_date = date('Y-m-d');
		}		
			
		//expiry date:		
		if (!empty($data['expire_date'])) {			
			if(mgm_is_valid_date($data['expire_date']) && $mysql_date = mgm_format_shortdate_to_mysql($data['expire_date']))
				$mgm_member->expire_date = $mysql_date;
			else {				
				$arr_resp['status'] = false;
				$arr_resp['error'][] = __('Invalid Last Expiry Date', 'mgm');
			}			
		}elseif($insert) {				
			$time = strtotime('now');			
			//if not lifetime:
			if($pack['duration_type'] != 'l') {
				$time = strtotime("+{$pack['duration']} {$duration_types[$pack['duration_type']]}", $time);							
				// formatted
				$mgm_member->expire_date = date('Y-m-d', $time);					
			}else 
				$mgm_member->expire_date = '';				
		}
		//if lifetime:
		if($pack['duration_type'] == 'l' && $mgm_member->status == MGM_STATUS_ACTIVE) {
			$mgm_member->expire_date = '';
			if(isset($mgm_member->status_reset_on))
				unset($mgm_member->status_reset_on);
			if(isset($mgm_member->status_reset_as))
				unset($mgm_member->status_reset_as);	
		}
		//active number of cycles:
		if(isset($data['active_num_cycles']) && !empty($data['active_num_cycles']))
			$mgm_member->active_num_cycles = $data['active_num_cycles'];
			
		//autoresponder subscription:
		if(isset($data['autoresponder']) && !empty($data['autoresponder'])) {			
			$mgm_member->autoresponder = $data['autoresponder'];	
			$mgm_member->subscribed = 'Y';	
		}
		//payment_info
		//module:
		if(isset($data['payment_info_module']) && !empty($data['payment_info_module'])) {			
			if(!isset($mgm_member->payment_info))
				$mgm_member->payment_info = new stdClass();
			$mgm_member->payment_info->module = $data['payment_info_module'];				
		}
		//subscr_id
		if(isset($data['payment_info_subscr_id']) && !empty($data['payment_info_subscr_id'])) {			
			if(!isset($mgm_member->payment_info))
				$mgm_member->payment_info = new stdClass();
			$mgm_member->payment_info->subscr_id = $data['payment_info_subscr_id'];				
		}
		//txn_type
		if(isset($data['payment_info_txn_type']) && !empty($data['payment_info_txn_type'])) {			
			if(!isset($mgm_member->payment_info))
				$mgm_member->payment_info = new stdClass();
			$mgm_member->payment_info->txn_type = $data['payment_info_txn_type'];				
		}
		//txn_id
		if(isset($data['payment_info_txn_id']) && !empty($data['payment_info_txn_id'])) {			
			if(!isset($mgm_member->payment_info))
				$mgm_member->payment_info = new stdClass();
			$mgm_member->payment_info->txn_id = $data['payment_info_txn_id'];				
		}
		
		if($arr_resp['status']) {			
			$arr_resp['mgm_member'] = $mgm_member;			
		}
		
		//object fields:
//		$mgm_member->code = 'mgm_member';
//		$mgm_member->name = 'Member Lib';
//		$mgm_member->description = 'Member Lib';		
		
		//check this:
		$duration_types = null;
		unset($duration_types);
		$arr_status = null;
		unset($arr_status);
		if(function_exists('gc_collect_cycles'))
			gc_collect_cycles();
		
		return $arr_resp;
	}
	
	// export
	function _do_export(){
		global $wpdb;	
		// local
		extract($_POST);
		// create
		$migrate = & new mgm_migrate();
		// version
		$version = mgm_get_class('auth')->get_product_info('product_version');
		// file
		$filepath = MGM_FILES_EXPORT_DIR.'export-'.$version.'-'.time().'.xml';
		// create
		$status = $migrate->create($filepath);			
		// return 
		return $filepath;
	}
	
	// core switch
	function _do_core_switch(){
		// track
		$status   = 'error';
		$message  = __('Core switch failed', 'mgm'); 
		$redirect = '';
		// response
		return json_encode(array('status'=>$status, 'message'=>$message));		
	}
	
	// core environment
	function _do_core_environment(){
		// local
		extract($_POST);
		// track
		$status   = 'error';
		$message  = __('Core environment setup failed.', 'mgm'); 
		$redirect = '';				
		// update
		if(isset($core_setup_execute) && !empty($core_setup_execute)){
			// update
			update_option('mgm_jqueryui_version', $_POST['jqueryui_version']);
			update_option('mgm_disable_core_jquery', $_POST['disable_core_jquery']);
			
			// track
			$status   = 'success';
			$message  = __('Core environment setup completed successfully.', 'mgm'); 
			$redirect = 'admin.php?page=mgm/admin'; 
		}

		// response
		return json_encode(array('status'=>$status, 'message'=>$message, 'redirect'=>$redirect));
	}
	
	// upgrade execute
	function _do_upgrade(){
		// track
		$status   = 'error';
		$message  = __('Upgrade failed', 'mgm'); 
		$redirect = '';
		// response
		return json_encode(array('status'=>$status, 'message'=>$message));				
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_tools.php