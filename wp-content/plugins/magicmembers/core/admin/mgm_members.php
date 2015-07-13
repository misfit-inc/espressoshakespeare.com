<?php
/**
 * Magic Members admin members module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_members extends mgm_controller{
 	
	// construct
	function mgm_members()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();		
		// load template view
		$this->load->template('members/index', array('data'=>$data));		
	}
	
	// members
	function members(){		
		global $wpdb;
		// data
		$data = array();							
		// load template view
		$this->load->template('members/member/index', array('data'=>$data));	
	}
	
	// member_list
	function member_list(){		
		global $wpdb;	
		// pager
		$pager = new mgm_pager();
		// data
		$data = array();	
		// search_fields
		$data['search_fields']= array(''=>'Select', 'username'=>'Username','id'=>'User ID', 'email'=>'User Email', 'membership_type'=>'Membership Type',
									  'reg_date'=>'Registration Date','last_payment'=>'Last Payment', 'expire_date'=>'Expiration Date', 'fee'=>'Fee', 
									  'status'=>'Status');
		// sort fields							  
		$data['sort_fields'] = array('username'=>'Username', 'id'=>'User ID', 'email'=>'User Email'	, /*'membership_type'=>'Membership Type', */
		                             'reg_date'=>'Registration Date');	
		
		// filter
		$sql_filter='';
		$data['search_field_name']  = '';
		$data['search_field_value'] = '';
		if(isset($_POST['search_field_name'])){
			//issue#: 219
			$data['search_field_name'] 	= $search_field_name  = $_POST['search_field_name'];
			$search_field_value = $wpdb->escape($_POST['search_field_value']);				
			$data['search_field_value'] = htmlentities($_POST['search_field_value']);
			switch($search_field_name){
				case 'username':
					//issue#: 347(LIKE SEARCH)
					$sql_filter = " AND user_login LIKE '%{$search_field_value}%'";			
				break;	
				case 'id':
					$sql_filter = " AND ID='".intval($search_field_value)."'";	
				break;
				case 'email':
					//issue#: 347(LIKE SEARCH)
					$sql_filter = " AND user_email LIKE '%{$search_field_value}%'";			
				break;	
				case 'membership_type':
					$members    = mgm_get_members_with('membership_type', $search_field_value);
					$members_in = (count($members)==0) ? 0 : (implode(',', $members));
					$sql_filter = " AND ID IN ({$members_in})";			
				break;	
				case 'reg_date':					
					$sql_filter = " AND DATE_FORMAT(user_registered,'%Y-%m-%d')='".date('Y-m-d', strtotime($search_field_value))."'";
				break;	
				case 'last_payment':
					$members    = mgm_get_members_with('last_pay_date', $search_field_value);
					$members_in = (count($members)==0) ? 0 : (implode(',', $members));
					$sql_filter = " AND ID IN ({$members_in})";
				break;
				case 'expire_date':
					$members    = mgm_get_members_with('expire_date', $search_field_value);
					$members_in = (count($members)==0) ? 0 : (implode(',', $members));
					$sql_filter = " AND ID IN ({$members_in})";
				break;
				case 'fee':
					$members    = mgm_get_members_with('amount', $search_field_value);
					$members_in = (count($members)==0) ? 0 : (implode(',', $members));
					$sql_filter = " AND ID IN ({$members_in})";
				break;
				case 'status':
					$members    = mgm_get_members_with('status', $search_field_value);
					$members_in = (count($members)==0) ? 0 : (implode(',', $members));
					$sql_filter = " AND ID IN ({$members_in})";
				break;
			}
		}
		
		// order
		$sql_order='';
		$data['sort_field'] = '';
		$data['sort_type'] 	= '';
		if(isset($_POST['sort_field_name'])){
			//issue#: 219
			$data['sort_field'] = $sort_field_name = $_POST['sort_field_name'];
			$data['sort_type'] = $sort_type       = $_POST['sort_type'];
			switch($sort_field_name){
				case 'username':
					$sql_order_by = "user_login";
				break;
				case 'id':
					$sql_order_by = "ID";
				break;
				case 'email':
					$sql_order_by = "user_email";
				break;
				case 'membership_type':
				break;
				case 'reg_date':
					//$sql_filter = "user_registered";
					$sql_order_by = "user_registered";
				break;
			}			
			// set
			if($sql_order_by){
				$sql_order ="ORDER BY {$sql_order_by} {$sort_type}";
			}
		}
		
		// page len	
		$pagelen         = intval($_REQUEST['pagelen']);
		$data['pagelen'] = ($pagelen == 0) ? 15 : $pagelen;
		
		// LIMIT 0, {$data['pagelen']}	
		// get members		
		$sql   = "SELECT SQL_CALC_FOUND_ROWS * FROM " . $wpdb->users . " WHERE ID != 1 {$sql_filter} {$sql_order} " . $pager->get_query_limit($data['pagelen']);	
		$users = $wpdb->get_results($sql);				
		
		// get page links
		$data['page_links'] = $pager->get_pager_links('admin.php?page=mgm/admin/members&method=member_list&pagelen='.$pagelen);	
		// log
		// mgm_log($sql);
		$data['users'] = $users;		
			
		// load template view
		$this->load->template('members/member/list', array('data'=>$data));
	}
	
	// member_update
	function member_update(){		
		global $wpdb;
		extract($_POST);
		// system
		$system = mgm_get_class('system');		
		// save
		if(isset($update_member_info)){
			$success = 0;
			foreach ($_POST['ps'] as $k=>$user_id) {
				// member
				$mgm_member = clone $this->_get_member_object($user_id);
				if(empty($mgm_member)) continue;
				$previous_membership = clone $mgm_member;				
				//$mgm_member= mgm_get_member($user_id);									
				// status
				if (isset($_POST['update_opt']) && in_array('status',$_POST['update_opt'])) {	
					// set				
					$pending_status = $mgm_member->status;
					$mgm_member->status = $_POST['upd_status'];
					// active for manualpay
					if($mgm_member->status==MGM_STATUS_ACTIVE){
						// for manual pay
						if($mgm_member->payment_info->module == 'mgm_manualpay'){
							// MARK status reset for manual pay upgrade
							$mgm_member->status_reset_on = NULL;
							unset($mgm_member->status_reset_on);
							// mark as paid
							$mgm_member->status_str = __('Last payment was successful','mgm');
							//send user notification: issue#: 537
							if($pending_status == MGM_STATUS_PENDING) {
								$userdata = get_userdata($user_id); 
								$blogname = get_option('blogname');
								// subject
								$subject = $system->get_template('payment_active_email_template_subject', array('blogname'=>$blogname), true);				
								// body	
								$message = $system->get_template('payment_active_email_template_body', array(	'blogname'=>$blogname, 
																												'name'=>mgm_stripslashes_deep($userdata->display_name), 
				                                                                              					'email'=>$userdata->user_email,
																							  					'admin_email'=>$system->setting['admin_email']), true);	
								mgm_mail($userdata->user_email, $subject, $message); //send an email to the buyer
								unset($userdata);
								unset($message);															  
							}
						}
					}
				}
				// membership_type
				if (isset($_POST['update_opt']) && in_array('membership_type',$_POST['update_opt'])) {	
					$mgm_member->membership_type = $_POST['upd_membership_type'];
				}
				// expire_date
				if (isset($_POST['update_opt']) && in_array('expire_date',$_POST['update_opt'])) {	
					//lifetime
					if($mgm_member->duration_type != 'l') {
						// expire
						$mgm_member->expire_date = date('Y-m-d', strtotime($_POST['upd_expire_date']));
						// duration
						if (empty($mgm_member->duration)) {
							list($expire_month,$expire_day,$expire_year) = explode('/',$_POST['upd_expire_date']);						
							$mgm_member->duration_type = 'd';
							$mgm_member->duration =  ceil((mktime(0,0,0,$expire_month,$expire_day,$expire_year) - time()) / 86400);
						}
					}
				}
				// hide_old_content
				if (isset($_POST['update_opt']) && in_array('hide_old_content',$_POST['update_opt'])) {	
					$mgm_member->hide_old_content = $_POST['upd_hide_old_content'];
				}
				// pack_id
				if (isset($_POST['update_opt']) && in_array('pack_key',$_POST['update_opt'])) {	
					// getpack	
					$subs_pack = mgm_decode_package($_POST['upd_pack_key']);
					// if trial on		
					if ($subs_pack['trial_on']) {
						$mgm_member->trial_on            = $subs_pack['trial_on'];
						$mgm_member->trial_cost          = $subs_pack['trial_cost'];
						$mgm_member->trial_duration      = $subs_pack['trial_duration'];
						$mgm_member->trial_duration_type = $subs_pack['trial_duration_type'];
						$mgm_member->trial_num_cycles    = $subs_pack['trial_num_cycles'];
					}
					// duration
					$mgm_member->duration        = $subs_pack['duration'];
					$mgm_member->duration_type   = strtolower($subs_pack['duration_type']);
					$mgm_member->amount          = $subs_pack['cost'];
					$mgm_member->currency        = $system->setting['currency'];
					$mgm_member->membership_type = $subs_pack['membership_type'];	
					$mgm_member->pack_id         = $subs_pack['pack_id'];	
					// status
					$mgm_member->status     = MGM_STATUS_ACTIVE;
					$mgm_member->status_str = __('Last payment was successful','mgm');					
					
					// old type match and join date update
					//$old_membership_type = mgm_get_user_membership_type($user_id, 'code');
					//update if new subscription  OR guest user 
					if (strtolower($previous_membership->membership_type) == 'guest' || isset($_POST['insert_new_level']) ) {
						$mgm_member->join_date = time(); // type join date as different var						
					}
					
					// old content hide
					$mgm_member->hide_old_content = $subs_pack['hide_old_content']; 
					
					// time
					$time = time();				
					$mgm_member->last_pay_date = date('Y-m-d', $time);				
					// expire					
					if ($mgm_member->expire_date && $mgm_member->last_pay_date != date('Y-m-d', $time)) {
						// expiry
						$expiry = strtotime($mgm_member->expire_date);
						// greater
						if ($expiry > 0) {
							// time check
							if ($expiry > $time) {
								// update
								$time = $expiry;
							}
						}
					}
					
					// duration types expanded
					$duration_types = array('d'=>'DAY','m'=>'MONTH','y'=>'YEAR');
					// time
					if($mgm_member->duration_type != 'l') {
						$time = strtotime("+{$mgm_member->duration} {$duration_types[$mgm_member->duration_type]}", $time);							
						// formatted
						$time_str = date('Y-m-d', $time);				
						// date extended				
						if (!$mgm_member->expire_date || strtotime($time_str) > strtotime($mgm_member->expire_date) 
							|| isset($_POST['insert_new_level'])) {//This is to make sure that expire date is not copied from the selected members if any
							$mgm_member->expire_date = $time_str;										
						}
					}
					
					//if lifetime:
					if($subs_pack['duration_type'] == 'l' && $mgm_member->status == MGM_STATUS_ACTIVE) {
						$mgm_member->expire_date = '';
						if(isset($mgm_member->status_reset_on))
							unset($mgm_member->status_reset_on);
						if(isset($mgm_member->status_reset_as))
							unset($mgm_member->status_reset_as);	
					}
				}
				// update
				// success	
				if($this->_save_member_object($user_id, $mgm_member, $previous_membership))
					$success++;					
				// reset object
				unset($mgm_member);
				unset($previous_membership);
							
			}		
			// saved
			if ($success) {
				$message = sprintf(__('Successfully updated %d %s', 'mgm'), $success, ($success>1? 'members':'member'));
				$status  = 'success';
			}else{
				$message = __('Error while updating members', 'mgm');
				$status  = 'error';
			}
			// return response
			echo json_encode(array('status'=>$status, 'message'=>$message));exit();
		}	
		// data
		$data = array();	
		$data['enable_multiple_level_purchase']	 = (isset($system->setting['enable_multiple_level_purchase']) && $system->setting['enable_multiple_level_purchase'] == 'Y') ? true : false; 					
		// load template view
		$this->load->template('members/member/update', array('data'=>$data));	
	}
	
	// member_export
	function member_export() {		
		global $wpdb;
		if(!WP_DEBUG)
			error_reporting(0);
		extract($_POST);
		// save
		if(isset($export_member_info)){
			$success =0;
			// default
			if(!isset($_POST['bk_membership_type'])){
				$membership_type = 'all';
			}else{
				$membership_type = $_POST['bk_membership_type'];
			}
			// date
			$date_start = $_POST['bk_date_start'];	
			$date_end   = $_POST['bk_date_end'];
			$query = "";
			if($date_start){
				$date_start = strtotime($date_start);
				if($date_end){
					$date_end = strtotime($date_end);
					//issue#" 492
					//$query    =" AND (UNIX_TIMESTAMP(date_format(user_registered, '%Y-%m-%d')) BETWEEN '{$date_start}' AND '{$date_end}')";
					$query    =" AND UNIX_TIMESTAMP(user_registered) >= '{$date_start}' AND UNIX_TIMESTAMP(date_format(user_registered, '%Y-%m-%d')) <= '{$date_end}'";
				}else{
					$query    =" AND UNIX_TIMESTAMP(user_registered) >= '{$date_start}'";
				}
			}else if($date_end){
				$date_end = strtotime($date_end);
				$query    = " AND UNIX_TIMESTAMP(date_format(user_registered, '%Y-%m-%d')) <= '{$date_end}' ";
			}
			// all users	
			$sql = 'SELECT ID, user_login, user_email, user_registered, display_name FROM `' . $wpdb->users . '` 
			        WHERE ID <> 1 '.$query.' ORDER BY `user_registered` ASC';		
			// users
			$users = $wpdb->get_results($sql);			
			// filter
			$users_filtered = array();
			// date
			$current_date = time();			
			// loop
			foreach ($users as $user) {
				$user_copy = clone $user;
				// member
				$mgm_member = mgm_get_member($user->ID);					
				//check search parameters:
				if($this->_get_membership_details($mgm_member, $bk_msexp_dur_unit, $bk_msexp_dur, $membership_type, $current_date )) {
					
					if(method_exists($mgm_member,'merge_fields')){					
						$user = $mgm_member->merge_fields($user);
					}					
					// format dates
					$user->user_registered = date(MGM_DATE_FORMAT_SHORT, strtotime($user->user_registered));	
					$user->last_pay_date   = (int)$user->last_pay_date>0 ? date(MGM_DATE_FORMAT_SHORT, strtotime($user->last_pay_date)) : 'N/A';	
					$user->expire_date     = (!empty($user->expire_date)) ? date(MGM_DATE_FORMAT_SHORT, strtotime($user->expire_date)) : 'N/A';		
					$user->join_date       = (int)$user->join_date>0 ? date(MGM_DATE_FORMAT_SHORT, $user->join_date) : 'N/A';		
					
					// cache
					$users_filtered[]      = $user;						
				}
				//consider multiple memberships as well:
				if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
					foreach ($mgm_member->other_membership_types as $key => $memtypes) {
						$memtypes = mgm_convert_array_to_memberobj($memtypes, $user->ID);
						//check search parameters:
						if($this->_get_membership_details($memtypes, $bk_msexp_dur_unit, $bk_msexp_dur, $membership_type, $current_date )) {
							$user_mem = clone $user_copy;	
							//add custom fields as well:
							if(!empty($mgm_member->custom_fields)) {
								foreach ($mgm_member->custom_fields as $index => $val)
									$user_mem->{$index} = $val;
							}
							
							if(method_exists($memtypes,'merge_fields')){		
								$user_mem = $memtypes->merge_fields($user_mem);	
							}else {
								$data = mgm_object2array($memtypes);
								if(isset($memtypes->payment_info) && count($memtypes->payment_info) > 0) {
									foreach ($memtypes->payment_info as $index => $val)
										$data['payment_info_' . $index] = str_replace('mgm_', '', $val);
								}
								
								foreach ($data as $index => $val)
									$user_mem->$index = $val;
							}
							// format dates
							$user_mem->user_registered = date(MGM_DATE_FORMAT_SHORT, strtotime($user_mem->user_registered));	
							$user_mem->last_pay_date   = (int)$memtypes->last_pay_date>0 ? date(MGM_DATE_FORMAT_SHORT, strtotime($memtypes->last_pay_date)) : 'N/A';	
							$user_mem->expire_date     = (!empty($memtypes->expire_date)) ? date(MGM_DATE_FORMAT_SHORT, strtotime($memtypes->expire_date)) : 'N/A';		
							$user_mem->join_date       = (int)$memtypes->join_date>0 ? date(MGM_DATE_FORMAT_SHORT, $memtypes->join_date) : 'N/A';		
							$user_mem->user_password   = $mgm_member->user_password;		
							$user_mem->rss_token   	   = $mgm_member->rss_token;		
							
							// cache
							$users_filtered[]      = $user_mem;	
							unset($user_mem);
						}
					}
				}
				// duration
				/*				
				if($bk_msexp_dur_unit && $bk_msexp_dur) {
					// expire					
					$expire_date = $mgm_member->expire_date;				
					$date_diff   = strtotime($expire_date) - $current_date;				
					$days        = floor($date_diff/(60*60*24));
					// days
					switch($bk_msexp_dur){
						case 'month':
							$bkmsexp_days=$bkmsexp_dur_unit*30;
						break;
						case 'week':
							$bkmsexp_days=$bkmsexp_dur_unit*7;
						break;
						case 'day':
							$bkmsexp_days=$bkmsexp_dur_unit;
						break;
					}					
					// skip if range matches
					if(($days<=0) || $days>$bkmsexp_days){
						continue;
					}				
				}// end expire
				
				// membership_type
				if ($membership_type != 'all' ){
					if($mgm_member->membership_type != $membership_type){
						continue;
					}
				}*/
				
				// merge
				/*if(method_exists($mgm_member,'merge_fields')){					
					$user = $mgm_member->merge_fields($user);	
				}	
				// format dates
				$user->user_registered = date(MGM_DATE_FORMAT_SHORT, strtotime($user->user_registered));	
				$user->last_pay_date   = (int)$user->last_pay_date>0 ? date(MGM_DATE_FORMAT_SHORT, strtotime($user->last_pay_date)) : 'N/A';	
				$user->expire_date     = (!empty($user->expire_date)) ? date(MGM_DATE_FORMAT_SHORT, strtotime($user->expire_date)) : 'N/A';		
				$user->join_date       = (int)$user->join_date>0 ? date(MGM_DATE_FORMAT_SHORT, $user->join_date) : 'N/A';		
				
				// cache
				$users_filtered[]      = $user;*/
				
			}// end for	
			
			// mgm_log('FILTERED USERS : '.print_r($users_filtered, true));
			
			// mgm_log(print_r($user_list,true));	
			$filename= mgm_create_xls_file($users_filtered);			
			// src
			$src = MGM_FILES_EXPORT_URL . $filename;
			chmod($src, 0777);
			// success
			$success = count($users_filtered);
			// exported
			if ($success) {
				$message = sprintf(__('Successfully exported %d %s.', 'mgm'), $success, ($success>1 ? 'users' : 'user'));
				$status  = 'success';
			}else{
				$message = __('Error while exporting users.', 'mgm');
				$status  = 'error';
			}
			// return response
			echo json_encode(array('status'=>$status, 'message'=>$message, 'src'=>$src));
			exit();
		}	
		// data
		$data = array();							
		// load template view
		$this->load->template('members/member/export', array('data'=>$data));	
	}
	
	// get details
	function _get_membership_details($mgm_member, $bk_msexp_dur_unit, $bk_msexp_dur, $membership_type, $current_date ) {
		if($bk_msexp_dur_unit && $bk_msexp_dur){
			// expire					
			$expire_date = $mgm_member->expire_date;				
			$date_diff   = strtotime($expire_date) - $current_date;				
			$days        = floor($date_diff/(60*60*24));
			// days
			switch($bk_msexp_dur){
				case 'month':
					$bkmsexp_days=$bk_msexp_dur_unit*30;
				break;
				case 'week':
					$bkmsexp_days=$bk_msexp_dur_unit*7;
				break;
				case 'day':
					$bkmsexp_days=$bk_msexp_dur_unit;
				break;
			}					
			// skip if range matches
			if(($days<=0) || $days > $bkmsexp_days){
				return false;
			}						
		}// end expire
		// membership_type
		if ($membership_type != 'all' ){
			if($mgm_member->membership_type != $membership_type){
				return false;
			}
		}	
		//OK
		return true;	
	}
	//delete an mgm member
	//Note: this will delete user from db only. Admin will need to manually delete from recurring subscription/autoresponder if any
	function member_delete() {
		global $current_user,$wpdb;
		
		$message = array();
		$status  = 'error';
		if(isset($current_user->ID) && $current_user->ID != 0 && is_numeric($current_user->ID) ) {
			extract($_POST);
			if(isset($submit_delete) && $submit_delete == 1 ) {
				
				if(!current_user_can( 'delete_users' )) {
					$message[] = __('You can&#8217;t delete users.', 'mgm');
				}else {					
					if(isset($ps) && !empty($ps)) {						
						$deleted = 0;
						foreach ($ps as $uid) {							
							//delete user
							$uid = $wpdb->escape($uid);
							if((int)($uid)>0) {	
								// get user							
								$user = new WP_User($uid);		
								// check						
								if(isset($user->ID) && $user->ID != 0 && (int)$user->ID>0 ) {	
									// permission								
									if(current_user_can('delete_user', $user->ID) && $current_user->ID != $user->ID ){
										// multisite
										if ( is_multisite() ) {
											if(wpmu_delete_user($user->ID)) $deleted++;	
										}else{
										// general
											if(wp_delete_user($user->ID)) $deleted++;
										}										 
									}	
								}
								// unset
								unset($user);								
							}
						}
						// message
						$s = ($deleted > 1) ? 's' : '';
						// set
						if(!$deleted) {
							$message[] = __('Error while deleting user'.$s.'.', 'mgm');
						}elseif ($deleted && count($ps) != $deleted) {
							$message[] = __('Partially deleted users.', 'mgm');
						}else {
							$message[] = __('Successfully deleted user'.$s.'.', 'mgm');
							$status = 'success';
						}
					}
				}
			}
		}
		
		// return response
		echo json_encode(array('status'=>$status, 'message'=>!empty($message) ? implode('<br/>', $message):''));
		exit();
	}
	
	// subscription_options
	function subscription_options(){		
		// data
		$data = array();		
		// load template view
		$this->load->template('members/subscription/options', array('data'=>$data));	
	}
	
	// subscription_packages_list, 
	function subscription_packages_list(){
		// data
		$data = array();		
		// roles
		$wproles = new WP_Roles();
		$roles   = array_reverse($wproles->role_names);	
		$system = mgm_get_class('system');
		$payment_modules = $system->get_active_modules('payment');
		// check if any module supports trial setup i.e. paypal authorizenet
		$supports_trial = false;
		foreach($payment_modules as $payment_module){
			if(mgm_get_module($payment_module,'payment')->supports_trial =='Y'){
				$supports_trial = true;
				break;
			}
		}
		// membership_types
		$membership_types = mgm_get_class('membership_types')->membership_types;
		// subscription_packs
		$subscription_packs = mgm_get_class('subscription_packs')->packs;
		// log
		// mgm_log('log:'.print_r($subscription_packs,true));	
		// packages
		foreach($membership_types as $type_code=>$type){				
			// package
			$membership_package = '';
			// pack data
			$pack_data = array();
			// roles
			$pack_data['roles'] = $roles;
			// supports_trial
			$pack_data['supports_trial'] = $supports_trial;
			// payment_modules
			$pack_data['payment_modules'] = $payment_modules;			
			// loop
			foreach($subscription_packs as  $i=>$pack){				
				// show when match type
				if ($pack['membership_type'] != $type_code) continue;				
				// set
				$pack_data['pack_ctr'] = $i+1;				
				// default values
				$pack['num_cycles'] = (isset($pack['num_cycles'])) ? $pack['num_cycles'] : 1 ;
				$pack['role']       = (isset($pack['role'])) ? $pack['role'] : 'subscriber';
				$pack['default']    = (int)(isset($pack['default']) ? $pack['default'] : 0);
				// set pack
				$pack_data['pack']  = $pack;		
				// get html
				$membership_package .= ($this->load->template('members/subscription/package', array('data'=>$pack_data), true));
			}
			// get html
			$data['membership'][$type_code] = $membership_package;
		}
		// membership types
		$data['membership_types'] = $membership_types;
		// active modules
		$data['payment_modules'] = $payment_modules;
		//check free module is enabled
		$data['free_module_enabled'] = in_array('mgm_free', $payment_modules) && (mgm_get_module('mgm_free')->enabled=='Y') ? 1 : 0;
		$data['enable_multiple_level_purchase'] = $system->setting['enable_multiple_level_purchase'];
		// load template view
		$this->load->template('members/subscription/packages_list', array('data'=>$data));	
	}
	
	// subscription_package : single for new pack
	function subscription_package(){
		global $wpdb;	
		extract($_POST);
		// roles
		$wproles = new WP_Roles();
		$roles   = array_reverse($wproles->role_names);	
		$payment_modules = mgm_get_class('system')->get_active_modules('payment');		
		// check if any module supports trial setup i.e. paypal authorizenet
		$supports_trial = false;
		foreach($payment_modules as $payment_module){
			if(mgm_get_module($payment_module,'payment')->supports_trial =='Y'){
				$supports_trial = true;
				break;
			}
		}		
		// get object
		$packs_obj = mgm_get_class('subscription_packs');
		// create empty pack
		$new_pack = $packs_obj->add_pack($type);		
		// pack data
		$pack_data = array();
		// roles
		$pack_data['roles'] = $roles;
		// supports_trial
		$pack_data['supports_trial']  = $supports_trial;
		// payment_modules
		$pack_data['payment_modules'] = $payment_modules;		
		// set
		$pack_data['pack_ctr'] = key($new_pack);	
		// pack 
		$pack = current($new_pack);			
		// def values
		$pack['num_cycles'] = (isset($pack['num_cycles'])) ? $pack['num_cycles'] : 1 ;
		$pack['role']       = (isset($pack['role'])) ? $pack['role'] : 'subscriber';
		$pack['default']    = (int)(isset($pack['default']) ? $pack['default'] : 0);	
		// pack
		$pack_data['pack']  = $pack;		
				
		// load template view
		$this->load->template('members/subscription/package', array('data'=>$pack_data));	
	}	
	
	// subscription_packages_update
	function subscription_packages_update(){
		// get object
		$packs_obj = mgm_get_class('subscription_packs');
		
		$obj_role = new mgm_roles();
		// init
		$_packs = array();
		
		$arr_new_role_users  = array();
		$arr_users_main_role = array();
		
		// loop
		foreach($_POST['packs'] as $pack) {
			// set modules
			if(!isset($pack['modules'])){
				$pack['modules'] = array();
			}			
			//check role changed:
			$prev_pack = $packs_obj->get_pack($pack['id']);
			if(isset($prev_pack['role']) && isset($pack['role']) && $prev_pack['role'] != $pack['role']) {
				//find users with the package:
				if(!isset($uid_all))
					$uid_all = mgm_get_all_userids();
				$arr_users = mgm_get_users_with_package($pack['id'], $uid_all); 				
				if(!empty($arr_users)) {					
					foreach ($arr_users as $uid) {						
						//add role to old users
						$user = new WP_User($uid);	
						if(in_array($user->roles[0], array($prev_pack['role'])))
							$arr_users_main_role[$uid] = $pack['role'];
						else 
							$arr_users_main_role[$uid] = $user->roles[0];
						//add new role:
						$obj_role->add_user_role($uid, $pack['role'],false, false );
						$arr_new_role_users[] = $uid;	
					}
				}
			}
			
			if($pack['duration_type'] == 'l')//lifetime:
				$pack['duration'] = $pack['num_cycles'] = 1;	
			
			//update active on pages:
			foreach ($packs_obj->get_active_options() as $option => $val) {
				if(!isset($pack['active'][$option]) || empty($pack['active'][$option]) )
					$pack['active'][$option] = 0; 
			}
						
			// set
			$_packs[] = $pack;
		}				
		// save
		$packs_obj->set_packs($_packs);
		// save to database
		// update_option('mgm_subscription_packs', $packs_obj);
		$packs_obj->save();
		
		//remove excess roles from user if updated role	
		if(count($arr_new_role_users) > 0) {
			$arr_new_role_users = array_unique($arr_new_role_users);			
			foreach ($arr_new_role_users as $uid) {
				mgm_remove_excess_user_roles($uid);
				//highlight role:
				if(isset($arr_users_main_role[$uid])) {					
					$obj_role->highlight_role($uid, $arr_users_main_role[$uid]);
					
				}
			}
		}
		// message
		$message = sprintf(__('Successfully updated %d membership packages.', 'mgm'), count($_packs));
		$status  = 'success';
		// return response		
		echo json_encode(array('status'=>$status, 'message'=>$message));		
	}
	
	// subscription_package_delete
	function subscription_package_delete(){
		// extract
		extract($_POST);
		// get object
		$packs_obj = mgm_get_class('subscription_packs');		
		// empty
		$packs = array();
		// flag
		$deleted = false;
		// loop
		foreach($packs_obj->packs as $i=>$pack){
			// match
			if(isset($pack['id'])){
			// new version 2.0 branch
				if($pack['id']==$id){
					$deleted = true;
					continue; 
				}	
			}else{
			// old 1.0 branch without pack_id	
				if($i==$index){
					$deleted = true;
					continue; 
				}	
			}			
			// filter				
			$packs[] = $pack;	
		}	
		
		// update only when deleted
		if($deleted){
			// set 
			$packs_obj->set_packs($packs);		
			// update
			// update_option('mgm_subscription_packs', $packs_obj);
			$packs_obj->save();
			// message
			$message = __(sprintf('Successfully deleted membership package #%d.', $id), 'mgm');
			$status  = 'success';
		}else{
			$message = __(sprintf('Error while removing membership package #%d. The package not found.', $id), 'mgm');
			$status  = 'error';
		}		
		
		// return response
		echo json_encode(array('status'=>$status, 'message'=>$message));exit();
	}
	
	// membership_types_list
	function membership_types_list(){
		// data
		$data = array();	
		// get obj
		$data['mgm_membership_types'] = mgm_get_class('membership_types');	
		// load template view
		$this->load->template('members/membership/types_list', array('data'=>$data));	
	}
	
	// membership_type_update
	function membership_type_update(){
		global $wpdb;	
		extract($_POST);
		$message = '';
		$status = '';
		// new account 
		if(isset($new_membership_type) && !empty($new_membership_type)){
			// new type
			$new_membership_type = trim($new_membership_type);
			// allowed only
			if(strtolower($new_membership_type) != 'none'){
				// set
				$mgm_membership_types = mgm_get_class('membership_types');
				$success              = $mgm_membership_types->set_membership_type($new_membership_type);				
				// update
				if($success){
					// add redirect url
					// url
					$redirect_url = (isset($new_login_redirect_url)) ? $new_login_redirect_url : '';
					$logout_redirect_url = (isset($new_logout_redirect_url)) ? $new_logout_redirect_url : '';
					$new_type_code = $mgm_membership_types->get_type_code($new_membership_type);
					// set url
					$mgm_membership_types->set_login_redirect($new_type_code, $redirect_url);	
					$mgm_membership_types->set_logout_redirect($new_type_code, $logout_redirect_url);				
					// update
					// update_option('mgm_membership_types', $mgm_membership_types);
					$mgm_membership_types->save();
					// message
					$message = sprintf(__('Successfully created new membership type: %s.', 'mgm'), mgm_stripslashes_deep($new_membership_type));
					$status  = 'success';
				}else{
					$message = sprintf(__('Error while creating new membership type: %s. Duplicate type.', 'mgm'), mgm_stripslashes_deep($new_membership_type));
					$status  = 'error';
				}				
			}else{
				$message = sprintf(__('Error while creating new membership type: %s. Not allowed.', 'mgm'), mgm_stripslashes_deep($new_membership_type));
				$status  = 'error';
			}	
		}
		
		// delete/move account
		//mgm_log(' delete/move account');
		if(isset($remove_membership_type) && count($remove_membership_type)>0){			
			// get object
			$mgm_membership_types = mgm_get_class('membership_types');
			// users 
			$users = $this->_get_all_users();
			// how many removed
			$removed = 0;
			
			// loop			
			foreach($remove_membership_type as $type_code){				
				// unset
				$mgm_membership_types->unset_membership_type($type_code);							
				// move
				if(isset($move_membership_type_to[$type_code]) && $move_membership_type_to[$type_code] != 'none'){
					//$target_membership_type = $move_membership_type_to[$type_code];
					foreach($users as $user){
						// get
						$mgm_member = mgm_get_member($user->ID);
						//if users with same membershiptype as that of selected
						if($mgm_member->membership_type == $type_code) {							
							// set
							$mgm_member->membership_type = $move_membership_type_to[$type_code];
							// update_user_option($user->ID, 'mgm_member', $mgm_member, true);	
							$mgm_member->save();						
						}else {							
							//check if any multiple levels exist:
							if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && count($mgm_member->other_membership_types) > 0) {
								foreach ($mgm_member->other_membership_types as $key => $memtypes) {
									//make sure its an object:
									$memtypes = mgm_convert_array_to_memberobj($memtypes, $user->ID);
									if($memtypes->membership_type == $type_code) {
										$memtypes->membership_type = $move_membership_type_to[$type_code];	
										mgm_save_another_membership_fields($memtypes, $user->ID, $key);										
										break;
									}
								}
							}
						}
						unset($mgm_member);
					}
				}
			
				// remove packs
				
				$subscription_packs = mgm_get_class('subscription_packs');				
				// empty
				$packs = array();
				// set
				foreach( $subscription_packs->packs as $i=>$pack){
					// if membership_type is same as being deleted
					if($pack['membership_type']==$type_code) {						
						continue; // skip
					}					
					// filtered
					$packs[] = $pack;	
				}	
				// set 				
				$subscription_packs->set_packs($packs);		
				// update
				// update_option('mgm_subscription_packs', $subscription_packs);
				$subscription_packs->save();	
				
				// removed
				$removed++;
			}			
			//ends remove pack:
			
			// update
			// update_option('mgm_membership_types', $mgm_membership_types);
			$mgm_membership_types->save();			
			// message
			$message .= ((!empty($message)) ? '<br>' : '' ) . sprintf(__('Successfully removed %d membership type(s).', 'mgm'), $removed);
			// set status
			$status  = 'success';
		}
		
		// update redirects
		// if(isset($update_login_redirect_url) && count($update_login_redirect_url)>0){					
			// get object
			$mgm_membership_types = mgm_get_class('membership_types');
			// loop types
			foreach($mgm_membership_types->get_membership_types() as $type_code=>$type_name){
				// skip new type, in edit otherwise overwritten
				if(isset($new_type_code) && !empty($new_type_code)){
					if($new_type_code == $type_code){
						continue;
					}
				}
				// url
				$redirect_url = (isset($login_redirect_url[$type_code])) ? $login_redirect_url[$type_code] : '';
				$redirect_url_logout = (isset($logout_redirect_url[$type_code])) ? $logout_redirect_url[$type_code] : '';
				// set
				$mgm_membership_types->set_login_redirect($type_code, $redirect_url);
				$mgm_membership_types->set_logout_redirect($type_code, $redirect_url_logout);						
			}
			// update
			// update_option('mgm_membership_types', $mgm_membership_types);	
			$mgm_membership_types->save();		
		//}			
		// return response		
		echo json_encode(array('status'=>$status, 'message'=>$message));
		exit();
	}		
	
	// coupons tab
	function coupons(){	
		global $wpdb;	
		// data
		$data = array();			
		// load template view
		$this->load->template('members/coupon/index', array('data'=>$data));	
	}
	
	// coupon_list
	function coupon_list(){
		global $wpdb;	
		// data
		$data = array();	
		// coupons		
	    $data['coupons'] = $wpdb->get_results('SELECT * FROM `' . TBL_MGM_COUPON . '` ORDER BY name');	
		// load template view
		$this->load->template('members/coupon/list', array('data'=>$data));		
	}
	
	// coupon_add
	function coupon_add(){	
		global $wpdb;	
		extract($_POST);
		
		// save
		if(isset($save_coupon)){
			// check duplicate
			if(mgm_is_duplicate(TBL_MGM_COUPON,array('name'))){
				$message = sprintf(__('Error while creating new coupon: %s, same code exists!', 'mgm'),  $name);
				$status  = 'error';
			}else{
				// fields
				$sql_fields[] = "name='{$name}'";
				$sql_fields[] = "description='{$description}'";
				$sql_fields[] = "value='{$value}'";
				$sql_fields[] = "create_dt=NOW()";			
				// use limit
				if(isset($use_limit) && is_numeric($use_limit)){
					$sql_fields[] = "use_limit='{$use_limit}'";
				}
				// expire_dt
				if(isset($expire_dt) && strtotime($expire_dt)>0){
					$sql_fields[] = "expire_dt='".date('Y-m-d h:i:s', strtotime($expire_dt))."'";
				}
				// sql
				$sql = "INSERT INTO `" . TBL_MGM_COUPON . "` SET ".implode(',', $sql_fields);	
				// saved
				if ($wpdb->query($sql)) {
					$message = sprintf(__('Successfully created new coupon: %s', 'mgm'),  $name);
					$status  = 'success';
				}else{
					$message = sprintf(__('Error while creating new coupon: %s', 'mgm'),  $name);
					$status  = 'error';
				}
			}	
			// return response
			echo json_encode(array('status'=>$status, 'message'=>$message));
			exit();
		}	
		
		// data
		$data = array();
		// currency
		$data['currency'] = mgm_get_class('system')->setting['currency'];	
		// load template view
		$this->load->template('members/coupon/add', array('data'=>$data));		
	}	
	
	// coupon_edit
	function coupon_edit(){	
		global $wpdb;	
		extract($_POST);
		
		// save
		if(isset($save_coupon)){
			// check duplicate
			if(mgm_is_duplicate(TBL_MGM_COUPON,array('name'),"id <> '{$id}'")){
				$message = sprintf(__('Error while updating coupon: %s, same code exists!', 'mgm'),  $name);
				$status  = 'error';
			}else{
				// fields
				$sql_fields[] = "name='{$name}'";
				$sql_fields[] = "description='{$description}'";
				$sql_fields[] = "value='{$value}'";					
				// use limit
				if(isset($use_limit) && is_numeric($use_limit)){
					$sql_fields[] = "use_limit='{$use_limit}'";
				}else{
					$sql_fields[] = "use_limit=NULL";
				}
				// expire_dt
				if(isset($expire_dt) && strtotime($expire_dt)>0){
					$sql_fields[] = "expire_dt='".date('Y-m-d h:i:s', strtotime($expire_dt))."'";
				}else{
					$sql_fields[] = "expire_dt=NULL";
				}
				// sql
				$sql = "UPDATE `" . TBL_MGM_COUPON . "` SET ".implode(',', $sql_fields)." WHERE id='{$id}' ";		
				// saved
				if ($wpdb->query($sql)) {
					$message = sprintf(__('Successfully updated coupon: %s', 'mgm'),  $name);
					$status  = 'success';
				}else{
					$message = sprintf(__('Error while updating coupon: %s', 'mgm'),  $name);
					$status  = 'error';
				}
			}	
			// return response
			echo json_encode(array('status'=>$status, 'message'=>$message));
			exit();
		}	
		
		// data
		$data = array();
		// coupon
		$data['coupon'] = $wpdb->get_row("SELECT * FROM ".TBL_MGM_COUPON." WHERE id='{$id}'");
		// currency
		$data['currency'] = mgm_get_class('system')->setting['currency'];	
		// load template view
		$this->load->template('members/coupon/edit', array('data'=>$data));		
	}	
	
	// coupon_delete 
	function coupon_delete(){
		global $wpdb;	
		extract($_POST);		
		// sql
		$sql = "DELETE FROM `" . TBL_MGM_COUPON . "` WHERE id = '{$id}'";
 		
		// delete
	    if ($wpdb->query($sql)) {    	    
			$message = __('Successfully deleted coupon: ', 'mgm');
			$status  = 'success';
	    }else{
			$message = __('Error while deleting coupon: ', 'mgm');
			$status  = 'error';
		}
		// return response
		echo json_encode(array('status'=>$status, 'message'=>$message));
	}
	
	// coupon_users
	function coupon_users(){
		global $wpdb;	
		extract($_POST);
		// data
		$data = array();
		// coupon
		$data['coupon'] = $wpdb->get_row("SELECT * FROM ".TBL_MGM_COUPON." WHERE id='{$id}'");
		// users
		$data['users'] = $this->_get_all_users();
		// load template view
		$this->load->template('members/coupon/users', array('data'=>$data));
	}
	
	// get all users
	function _get_all_users(){
		//global $wpdb;	
		//$sql = "SELECT ID FROM `{$wpdb->users}` WHERE ID <> 1";
		//return $wpdb->get_results($sql);		
		return mgm_get_all_userids(array('ID'), 'get_results');
	}
	
	// roles/roles_capabilities tab	
	function roles_capabilities(){	
		global $wpdb;	
		// data
		$data = array();			
		// load template view
		$this->load->template('members/roles_capabilities/index', array('data'=>$data));	
	}
	// roles/roles_capabilities listing
	function roles_capabilities_list() {				
		global $current_user;
		$objrole = new mgm_roles();
		$data['roles'] = $objrole->get_roles();	
		$data['admin_role'] = $objrole->admin_role;	
		$data['role_type'] 	= $objrole->role_type;
		// load template view
		$this->load->template('members/roles_capabilities/list', array('data'=>$data));	
	}
	
	// roles/roles_capabilities listing
	function roles_capabilities_list_others() {				
		global $current_user;
		$objrole = new mgm_roles();
		$objrole->role_type = 'others';
		//remove:
		//$objrole->add_capability();
		
		$data['roles'] 			= $objrole->get_roles();	
		$data['admin_role'] 	= $objrole->admin_role;	
		$data['role_type'] 		= $objrole->role_type;	
		foreach ($objrole->default_roles as $default)
			$arr_default[] = array('role' => $default, 'name' => ucfirst($default)); 	
		$data['default_roles'] 	= $arr_default; 		
		// load template view
		$this->load->template('members/roles_capabilities/list', array('data'=>$data));	
	}
	// roles/roles_capabilities listing
	function roles_capabilities_list_default() {				
		global $current_user;
		$objrole = new mgm_roles();
		$objrole->role_type = 'default';
		//remove:
		//$objrole->add_capability();
		
		$data['roles'] 			= $objrole->get_roles();	
		$data['admin_role'] 	= $objrole->admin_role;	
		$data['role_type'] 		= $objrole->role_type;	
		foreach ($objrole->default_roles as $default)
			$arr_default[] = array('role' => $default, 'name' => ucfirst($default)); 	
		$data['default_roles'] 	= $arr_default; 		
		// load template view
		$this->load->template('members/roles_capabilities/list', array('data'=>$data));	
	}
	//edit roles:
	function roles_capabilities_edit() {	
		global $wpdb, $current_user;		
		if(isset($_POST['save_roles'])) {			
			$objrole = new mgm_roles();
			extract($_POST);			
			$status = 'error';
			$role_type = '';
			$message = array();	
			if(!empty($rolename)) {
				$error = false;
				foreach ($rolename as $role => $value) {
					//added later to consider only the edited role:
					if($role == $selected_role) {
						$value =  trim($wpdb->escape($value));
						if(empty($value)) {
							$message[] = __('Role cannot be blank','mgm');
							$error = true;
						}elseif(!preg_match("/^[A-Za-z0-9_,\s]+$/", $value)) {
							$message[] = __('Role cannot contain special characters.','mgm');
							$error = true;
						}elseif (!$objrole->is_role_unique($value, true, $role)) {
							$message[] = __('Role/capability already exists.','mgm');
							$error = true;
						}
						if(!isset($chk_capability[$role]) || (isset($chk_capability[$role]) && empty($chk_capability[$role]))) {
							$message[] = __('Capability must be selected','mgm');
							$error = true;
						}
						break;	
					}
				}
				if(!$error) {
					//save roles:										
					foreach ($rolename as $role => $value) {
						if($role == $selected_role) {
							$key = $role;
							//save Role name:
							if(!in_array($role, $objrole->default_roles)) {
								//please note: this will return the edited role
								$role = $objrole->edit_role($role, $value);
							}							
							//remove							
							if(!empty($chk_capability[$key])) {
								//save capabilities:
								$arr_previous_caps = $objrole->get_capabilities($role);	
								$arr_new_caps = $chk_capability[$key];
								
								$arr_to_add 	= array_diff($arr_new_caps, $arr_previous_caps);
								$arr_to_remove 	= array_diff($arr_previous_caps, $arr_new_caps);
								
								//add new capabilities:
								if(!empty($arr_to_add)) {
									foreach ($arr_to_add as $cap) {
										$cap =  $wpdb->escape($cap);
										//grant access
										$objrole->update_capability_role($role, $cap, true);
									}
								}
								
								//remove access if any capabilities unchecked
								if(!empty($arr_to_remove)) {
									foreach ($arr_to_remove as $cap) {
										$cap =  $wpdb->escape($cap);
										//remove access
										$objrole->update_capability_role($role, $cap, false);
									}
								}
							}
							break;
						}
					}
					$type = $role_type;//from post
					$message[] = __('Successfully saved the changes.','mgm');
					$status = 'success';	
				}
			}					
			echo json_encode(array('status'=>$status, 'message'=>implode("<br/>",$message),'type' => $type));
			exit();
		}		
	}
	//create new role
	function roles_capabilities_add() {
		global $wpdb, $current_user;
		
		$objrole = new mgm_roles();
		$data = array();		
		$arr_caps = $objrole->get_mgm_default_capabilities();
		foreach ($arr_caps as $key => $c)
			$arr_caps[$key] 	= array('capability' => $c, 'name' => ucfirst(str_replace('_', ' ', $c)) );					
		$data['capabilities'] = $arr_caps; 
		if(isset($_POST['add_roles'])) {
			extract($_POST);			
			$status = 'error';	
			$error 	= false;
			$rolename =  trim($wpdb->escape($rolename));
			if(empty($rolename)) {
				$message[] = __('Role cannot be blank.','mgm');
				$error = true;
			}elseif(!preg_match("/^[A-Za-z0-9_,\s]+$/", $rolename)) {
				$data['rolename'] = $rolename; 
				$message[] = __('Role cannot contain special characters.','mgm');
				$error = true;
			}elseif (!$objrole->is_role_unique($rolename)) {
				$data['rolename'] = $rolename; 
				$message[] = __('Role/capability already exists.','mgm');
				$error = true;
			}
			if(!isset($chk_capability) || (isset($chk_capability) && empty($chk_capability))) {
				$message[] = __('Capability must be selected.','mgm');
				$error = true;
			}else 
				$data['chk_capability'] = $chk_capability;					
			
			if(!$error) {
				//save roles:				
				if(!in_array($rolename, $objrole->default_roles) && !empty($chk_capability)) {					
					if($objrole->add_role($rolename, $chk_capability)) {
						$message[] = __('Successfully added the new role.','mgm');
						$status = 'success';
					}else {
						$message[] = __('Error in creating role.','mgm');
					}					
				}
			}
			
			echo json_encode(array('status'=>$status, 'message'=>implode("<br/>",$message)));
				
			exit();
		}
		$this->load->template('members/roles_capabilities/add', array('data'=>$data));	
	}
	//delete capabilities
	function roles_capabilities_delete() {
		global $wpdb, $current_user, $wp_roles;
		
		$objrole = new mgm_roles();
		$status = '';
		$message = array();
		extract($_POST);		
		if(isset($role)) {
			$role = $wpdb->escape($role);
			$new_role = $wpdb->escape($new_role);
			if($wp_roles->is_role($role)) {				
				if($objrole->remove_role($role, $new_role)) {
					$message[] = __('Successfully deleted the role.','mgm');
					$status = 'success';
				}else {
					$message[] = __('Error in deleting the role.','mgm');
					$status = 'failure';
				}
			}
		}
		
		echo json_encode(array('status'=>$status, 'message'=>implode("<br/>",$message)));
		exit();
	}
	//Move role's users
	 function roles_capabilities_move_users() {
	 	global $wpdb, $current_user, $wp_roles;
		
		$objrole = new mgm_roles();
		$status = '';
		$message = array();
		extract($_POST);		
		if(isset($role)) {
			$role = $wpdb->escape($role);
			$new_role = $wpdb->escape($new_role);
			if($wp_roles->is_role($role)) {				
				if($objrole->move_users($role, $new_role)) {				
					$message[] = __('Successfully moved the users.','mgm');
					$status = 'success';
				}else {
					$message[] = __('Error in moving the role.','mgm');
					$status = 'failure';
				}
			}
		}		
		echo json_encode(array('status'=>$status, 'message'=>implode("<br/>",$message)));
		exit();
	 }
	 //get member object selectively
	 private function _get_member_object($user_id) {	 	
	 	if(isset($_POST['ps_mem'][$user_id]) && !empty($_POST['ps_mem'][$user_id])) {
	 		$mgm_member = mgm_get_member_another_purchase($user_id, $_POST['ps_mem'][$user_id][0]);	 
	 		if(empty($mgm_member))
	 			return new stdClass();	 		
	 	}else 
	 		$mgm_member = mgm_get_member($user_id);
	 		
	 	return $mgm_member;
	 }
	 //save member object
	 private function _save_member_object($user_id, $mgm_member, $previous_membership) {
	 	$pack = mgm_get_class('subscription_packs')->get_pack($mgm_member->pack_id);
	 	if(isset($_POST['update_opt']) && in_array('pack_key',$_POST['update_opt']) && isset($_POST['insert_new_level'])) {	 		 		
	 		if($previous_membership->membership_type == "guest" && $previous_membership->amount == 0) {
	 			//check selected membership already selected:
	 			if($previous_membership->membership_type == $mgm_member->membership_type )
	 				return false;
	 			// update default:
	 			// update_user_option($user_id, 'mgm_member', $mgm_member, true);		
				$mgm_member->save(); 			 			
	 		}else {
	 			$old_subtypes = mgm_get_subscribed_membershiptypes($user_id);
	 			//check selected membership already selected:
	 			if(in_array($mgm_member->membership_type, $old_subtypes ))
	 				return false;
	 				
	 			if(isset($mgm_member->custom_fields))
	 				unset($mgm_member->custom_fields);
	 			if(isset($mgm_member->other_membership_types) || empty($mgm_member->other_membership_types)) {	 				
	 				unset($mgm_member->other_membership_types);	
	 			}
	 			
	 			mgm_save_another_membership_fields($mgm_member, $user_id);	 				 			
	 		}
	 		//assign role:
	 		$change_order = (isset($_POST['highlight_role']) && (isset($_POST['upd_pack_key'])) && $_POST['upd_pack_key'] != '-') ? true : false;
	 		
			$obj_role = new mgm_roles();				
			$obj_role->add_user_role($user_id, $pack['role'], $change_order);								
			
	 	}else {
		 	if(isset($_POST['ps_mem'][$user_id]) && !empty($_POST['ps_mem'][$user_id])) {
		 		
		 		if(isset($mgm_member->custom_fields))
	 				unset($mgm_member->custom_fields);
	 			if(isset($mgm_member->other_membership_types) || empty($mgm_member->other_membership_types)) {	 				
	 				unset($mgm_member->other_membership_types);	
	 			}
	 			
	 			$prev_index = (isset($_POST['ps_mem_index'][$user_id][$previous_membership->membership_type])) ? $_POST['ps_mem_index'][$user_id][$previous_membership->membership_type] : null;	 			
	 			
	 			//uncomment
		 		mgm_save_another_membership_fields($mgm_member, $user_id, $prev_index );
		 	}else {		 		
		 		// update_user_option($user_id, 'mgm_member', $mgm_member, true);	
				$mgm_member->save();
		 	}		 	
		 	if($mgm_member->status == MGM_STATUS_EXPIRED) {
		 		//remove role from user:
				mgm_remove_userroles($user_id, $mgm_member);
		 	}else {		 		
			 	//if($mgm_member->membership_type != $previous_membership->membership_type) {//check this condition
			 		//mgm role object:
			 		$change_order = (isset($_POST['highlight_role']) && (isset($_POST['upd_pack_key'])) && $_POST['upd_pack_key'] != '-') ? true : false;
				 	
					$obj_role = new mgm_roles();
			 		//update role/change order		 						
					$obj_role->add_user_role($user_id, $pack['role'], $change_order);
			 	//}
		 	}
	 	}
	 	 	
	 	return true;
	 }
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_members.php