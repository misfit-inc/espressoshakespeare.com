<?php
/**
 * Magic Members schedular class
 * extends object to save options to database
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_schedular extends mgm_object{
	// var
	var $events    = array();
	var $schedules = array('hourly'=>array(), 'twicedaily'=>array(), 'daily'=>array(), 'quarterhourly' => array());
	
	// construct
	function __construct(){
		// php4
		$this->mgm_schedular();
	}
	
	// construct
	function mgm_schedular(){
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
		$this->name        = 'Schedular Lib';
		// description
		$this->description = 'Schedular Lib';
		// init
		$this->events = array();
	}
	
	// run reminder mailer
	function run($recurrence='daily'){						
		// loop
		foreach($this->schedules[$recurrence] as $callback){				
			// trigger
			if(method_exists($this,$callback)){										
				// run
				call_user_func(array($this,$callback));
				// log executed
				$curr_date = mgm_get_current_datetime();
				//$this->events[$recurrence][$callback] = time();	
				$this->events[$recurrence][$callback] = $curr_date['timestamp'];						
			}		
		}		
		// update option 
		// update_option('mgm_schedular', $this);
		$this->save();
	}
	
	// add_schedule
	function add_schedule($recurrence='daily', $callback){		
		// set array
		if(!is_array($this->schedules[$recurrence])) {			
			$this->schedules[$recurrence] = array();
		}
			
		// push
		if(!in_array($callback, $this->schedules[$recurrence])) {			
			array_push($this->schedules[$recurrence], $callback);
		}
	}
	
	// reminder
	function reminder_mailer(){		
		// check
		$this->_check_expiring_memberships();		
	}
	
	// reset
	function reset_expiration(){		
		// reset
		$this->_reset_ongoing_memberships();
	}
	
	// check expiring memberships
	function _check_expiring_memberships(){
		global $wpdb;
		// system 
		$mgm_system = mgm_get_class('system');		
		// packs
		$packs      = mgm_get_class('subscription_packs');
	
		// days_to_start	
		$data['days_to_start'] = intval($mgm_system->setting['reminder_days_to_start']);			
		// if greater
		if($data['days_to_start'] > 0 ){				
			// settings
			$data['days_incremental'] = $mgm_system->setting['reminder_days_incremental'];
			$data['days_incremental_ranges'] = preg_split('/[,;]/', $mgm_system->setting['reminder_days_incremental_ranges']);			
			// users
			//$users = $wpdb->get_results("SELECT ID,user_email,display_name FROM " . $wpdb->users . " WHERE ID <> 1");
			$users = mgm_get_all_userids(array('ID','user_email','display_name'), 'get_results');
			// found
			if($users){		
				$curr_date = mgm_get_current_datetime();		
				// template
				$data['template_subject'] 	= mgm_stripslashes_deep($mgm_system->get_template('reminder_email_template_subject', array(), true));
				$data['template_body'] 		= mgm_stripslashes_deep($mgm_system->get_template('reminder_email_template_body', array(), true));				
				//$data['current_date'] 		= time();																		
				$data['current_date'] 		= $curr_date['timestamp'];																		
				$data['subscription_types'] = mgm_get_class('membership_types')->membership_types;	
				// loop
				foreach($users as $user) {	
					// get member
					$mgm_member  = mgm_get_member($user->ID);															
					$this->_check_member_object($user, $mgm_member, $packs, $data);					
					//check other memberships as well 
					if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types) ) {
						foreach ($mgm_member->other_membership_types as $key => $val) {
							$val = mgm_convert_array_to_memberobj($val, $user->ID);
							if(isset($val->membership_type) && !empty($val->membership_type) && !in_array($val->membership_type, array('guest'))) { //skip if default value								
								$this->_check_member_object($user, $val, $packs, $data);
							}
						}
					}				
				}
			}
		}	
	}	
	
	// reset ongoing memberships
	function _reset_ongoing_memberships(){
		global $wpdb;
		
		// packs
		$packs      = mgm_get_class('subscription_packs');				
		// users	
		//$users = $wpdb->get_results("SELECT ID,user_email,display_name FROM " . $wpdb->users . " WHERE ID <> 1");
		$users = mgm_get_all_userids(array('ID','user_email','display_name'), 'get_results');
		// found
		if($users){			
			
			// current date: timezone formatted
			$curr_date = mgm_get_current_datetime('Y-m-d');
			//$data['current_date'] 			= date('Y-m-d');
			//$data['current_date_timestamp'] = time();	
			$data['current_date'] 			= $curr_date['date'];	
			$data['current_date_timestamp'] = $curr_date['timestamp'];
					
								
			$data['duration_types'] 		= array('d'=>'DAY','m'=>'MONTH','y'=>'YEAR');				
			// loop
			foreach($users as $user) {				
				// get member
				$mgm_member  = mgm_get_member($user->ID);				
				$this->_reset_mgm_member_objects($user, $mgm_member, $packs, $data);				
				//check other memberships as well 
				if(isset($mgm_member->other_membership_types) && is_array($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types) ) {
					foreach ($mgm_member->other_membership_types as $key => $val) {
						$val = mgm_convert_array_to_memberobj($val, $user->ID);
						if(isset($val->membership_type) && !empty($val->membership_type) && !in_array($val->membership_type, array('guest'))) { //skip if default value							
							$this->_reset_mgm_member_objects($user, $val, $packs, $data, true);
						}
					}
				}
			}
		}			
	}
	
	// reminder mail
	function _reminder_mail($user, $data){
		// format date
		$expire_date_fmt = date('m-d-Y',strtotime($data['expire_date']));
		// mail body
		$body = str_replace(array('[name]','[expire_date]','[subscription_type]'), array($user->display_name, $expire_date_fmt, $data['subscription_type']), $data['template_body']);	
		// send mail
		mgm_mail($user->user_email, $data['template_subject'], $body);		
	}
	
	//recursively check each member object of a user:
	function _check_member_object($user, $mgm_member, $packs, $data) {			
		// only check for Active members
		if($mgm_member->status != MGM_STATUS_ACTIVE) return;
		
		// check pack		
		$subs_pack = null;						
		if($mgm_member->pack_id){
			$subs_pack = $packs->get_pack($mgm_member->pack_id);
		}/*else{
			$subs_pack = $packs->validate_pack($mgm_member->amount, $mgm_member->duration, $mgm_member->duration_type, $mgm_member->membership_type);
		}*/
		
		if(empty($subs_pack)){
			$subs_pack = $packs->validate_pack($mgm_member->amount, $mgm_member->duration, $mgm_member->duration_type, $mgm_member->membership_type);
		}
		
		
		// check on going
		if(isset($subs_pack['id'])){
			//issue#: 478
			$num_cycles = (isset($mgm_member->active_num_cycles) && !empty($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : $subs_pack['num_cycles'] ;
			// ongoing / lifetime
			if($num_cycles == 0 ) {			
				// never send mail
				return;	
			}//allow onetime subscriptions
			elseif($num_cycles > 1 || $mgm_member->duration_type == 'l'){				
//				if(($subs_pack['num_cycles'] - 1 ) <= $mgm_member->rebilled ){					
//					return;
//				}				
				//if already unsubscribed 
				if(isset($mgm_member->status_reset_as) && in_array($mgm_member->status_reset_as, array(MGM_STATUS_AWAITING_CANCEL,MGM_STATUS_CANCELLED) )){
					if(empty($mgm_member->expire_date))
						$mgm_member->expire_date = $mgm_member->status_reset_on;
					//let it send
				}//send email at the end of the subscription
				elseif($mgm_member->duration_type == 'l' || (!isset($mgm_member->rebilled) || ($mgm_member->rebilled <= ($num_cycles - 1 )))){					
					return;
				}
			}
		}		
		// expire		
		$expire_date = $mgm_member->expire_date;				
		$date_diff   = strtotime($expire_date) - $data['current_date'];				
		$days        = floor($date_diff/(60*60*24));	
		
		$arr_email = array();
		$arr_email['expire_date'] 		= $expire_date;					
		$arr_email['subscription_type'] = mgm_stripslashes_deep($data['subscription_types'][ $mgm_member->membership_type ]);
		$arr_email['template_subject'] 	= $data['template_subject'];						
		$arr_email['template_body'] 	= $data['template_body'];					
					
		// days match					 
		if($days == $data['days_to_start']){					
			// send mail			
			$this->_reminder_mail($user, $arr_email);								
		}else{
			// incremental
			if($data['days_incremental'] == 'Y'){
				if(is_array($data['days_incremental_ranges'])){					
					foreach($data['days_incremental_ranges'] as $range){
						// get int
						$range = intval($range);									
						// if days match
						if(($range > 0) && ($days == $range)){							
							// send mail							
							$this->_reminder_mail($user, $arr_email);																								
						}
					}
				}
			}
		}
		return;//ends	
	}
	//check each mgm_member objects
	function _reset_mgm_member_objects($user, $mgm_member, $packs, $data, $other_purchases = false) {		
		
		// MARK status reset for manual pay upgrade
		if(!is_null($mgm_member->status_reset_on)) {			
			// date match
			if( $mgm_member->status_reset_on == $data['current_date']) {				
				// manual pay
				if($mgm_member->payment_info->module == 'mgm_manualpay'){						
					// set as pending again
					$mgm_member->status = MGM_STATUS_PENDING;
					
					// update member	
					//save multiple level subscription
					if($other_purchases)
						mgm_save_another_membership_fields($mgm_member, $user->ID);
					else	
						// update_user_option($user->ID, 'mgm_member', $mgm_member, true);
						$mgm_member->save();
					
					// recapture
					if($other_purchases)
						$mgm_member = mgm_get_member_another_purchase($user->ID, $mgm_member->membership_type);
					else 	
						$mgm_member = mgm_get_member($user->ID);
				}else {					
				// other 
					// $mgm_member->status_reset_as = MGM_STATUS_CANCELLED
					// set as cancelled
					$mgm_member->status = $mgm_member->status_reset_as;
					// expire date
					if($mgm_member->status_reset_as == MGM_STATUS_CANCELLED){
						$mgm_member->expire_date = $data['current_date'];
						//reassign expiry membership pack if exists: issue#: 535
						$mgm_member = apply_filters('mgm_reassign_member_subscription', $user->ID, $mgm_member, 'CANCEL', true);						
					}
					
					// update member		
					if($other_purchases)
						mgm_save_another_membership_fields($mgm_member, $user->ID);
					else	
						// update_user_option($user->ID, 'mgm_member', $mgm_member, true);
						$mgm_member->save();
					
					// recapture
					if($other_purchases)
						$mgm_member = mgm_get_member_another_purchase($user->ID, $mgm_member->membership_type);
					else 	
						$mgm_member = mgm_get_member($user->ID);
				}
			}	
		} 					
		// only check for Active members
		if($mgm_member->status != MGM_STATUS_ACTIVE) return;
		
		// find expire date	
		$expire_date = $mgm_member->expire_date;
		//active lifetime user:
		if(empty($expire_date) && $mgm_member->duration_type == 'l')
			return;				
		
		$date_diff   = strtotime($expire_date) - $data['current_date_timestamp'];				
		$days        = floor($date_diff/(60*60*24));	
		
		// days match, support for expired check, negative days		
		//commented the below line because the membership was expiring ine day early.
		//if($days <= 1) {			
		if($days <= 0) {			
			// check pack
			$subs_pack = null;								
			if($mgm_member->pack_id){
				$subs_pack = $packs->get_pack($mgm_member->pack_id);
			}/*else{
				$subs_pack = $packs->validate_pack($mgm_member->amount, $mgm_member->duration, $mgm_member->duration_type, $mgm_member->membership_type);
			}*/
			if(empty($subs_pack)){
				$subs_pack = $packs->validate_pack($mgm_member->amount, $mgm_member->duration, $mgm_member->duration_type, $mgm_member->membership_type);
			}				
			// ok
			if(isset($subs_pack['id'])) {
				//issue#: 478
				$num_cycles = (isset($mgm_member->active_num_cycles) && !empty($mgm_member->active_num_cycles)) ? $mgm_member->active_num_cycles : $subs_pack['num_cycles'] ;
				// ongoing
				//if($subs_pack['num_cycles']==0 || (int)$mgm_member->rebilled <100){							
				//issue #: 418
				if((	$num_cycles == 0 ||  //Ongoing 
						$num_cycles > 1 ) && // fixed cycles 
						(int)$mgm_member->rebilled < 100) {	
											
					//make sure scheduler considers it after expiry date only:
					if( abs($date_diff/(60*60*24)) >= 1 && $days < 0) {	//This will check the next day after expiry date to consider payments happened on expiry date as well 																	
						//mark the user status as Pending:
						// set expired status
						if($num_cycles > 1 && $mgm_member->rebilled == $num_cycles ) {
							$mgm_member->status = MGM_STATUS_EXPIRED;	
							// set status
							$mgm_member->status_str = __('Membership expired','mgm');
							//reassign expiry membership pack if exists: issue#: 535
							$mgm_member = apply_filters('mgm_reassign_member_subscription', $user->ID, $mgm_member, 'EXPIRE', true);
							
						}else {
							$mgm_member->status = MGM_STATUS_NULL;	
							// set status
							$mgm_member->status_str = __('Last payment was refunded or denied','mgm');																					
						}
						// update member							
						if($other_purchases)
							mgm_save_another_membership_fields($mgm_member, $user->ID);
						else {	
							// update_user_option($user->ID, 'mgm_member', $mgm_member, true);	
							$mgm_member->save();
						}
					}					
					
				}elseif( $num_cycles == 1 ) { //one-time billing 										
					// not ongoing							
					// set expired status
					$mgm_member->status = MGM_STATUS_EXPIRED;
					//reassign expiry membership pack if exists: issue#: 535	
					// set status
					$mgm_member->status_str = __('Membership expired','mgm');
					//reassign expiry membership pack if exists: issue#: 535
					$mgm_member = apply_filters('mgm_reassign_member_subscription', $user->ID, $mgm_member, 'EXPIRE', true);							
					// update member		
					if($other_purchases)
						mgm_save_another_membership_fields($mgm_member, $user->ID);
					else	
						// update_user_option($user->ID, 'mgm_member', $mgm_member, true);		
						$mgm_member->save();
					//remove role from user:
					mgm_remove_userroles($user->ID, $mgm_member);						
				}							
			}else{					
			// pack not found manual update, expire users if days negative
				if($subs_pack === false && $days <= 0){						
					// set expired status
					$mgm_member->status = MGM_STATUS_EXPIRED;	
					// set status
					$mgm_member->status_str = __('Membership expired','mgm');	
					// update member		
					if($other_purchases)
						mgm_save_another_membership_fields($mgm_member, $user->ID);
					else	
						// update_user_option($user->ID, 'mgm_member', $mgm_member, true);
						$mgm_member->save();
						
					//remove role from user:
					mgm_remove_userroles($user->ID, $mgm_member);	
				}					
			}									
		}
		
		return;
	}
	/**
	 * Check and update dataplus transactions
	 *
	 */
	function epoch_dataplus_transactions() {
		$current_date = mgm_get_current_datetime('Y-m-d H:i:s');
		mgm_log('CHECKING EPOCH DATAPLUS TRANSACTIONs:' .$current_date['date']);
		
		$epoch = mgm_get_module('epoch', 'payment');
		
		//check and update transactions:
		$epoch->update_dataplus_transactions();
		
		//check and update transactions:
		$epoch->update_dataplus_cancellations();
	}

	// fix
	function apply_fix($old_obj){
		// to be copied vars
		$vars = array('events','schedules');
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
		$vars = array('events','schedules');
		// set
		foreach($vars as $var){
			// var
			$this->options[$var] = $this->{$var};
		}	
	}
}
// core/libs/classes/mgm_schedular.php