<?php
/** 
 * Objects merge/update
 */  
//restore already saved other_membership_types as array:
$settings = mgm_get_class('system')->setting;
//as the patch is for converting already saved other_membership_types objectss into array, and is related to Multiple Membership feature,
//enable the patch only if multiple membership feature is ON
if(isset($settings['enable_multiple_level_purchase']) && $settings['enable_multiple_level_purchase'] == 'Y' ) {
	$arr_users = mgm_get_all_userids();
	if(!empty($arr_users)) {
		$arr_remove = array('ID','id', 'name', 'code', 'description', 'saving','custom_fields', 'other_membership_types', 'setting');
		foreach ($arr_users as $user_id) {
			$mgm_member = mgm_get_member($user_id);
			if(isset($mgm_member->other_membership_types) && !empty($mgm_member->other_membership_types) ) {
				foreach ($mgm_member->other_membership_types as $key => $member_obj) {
					//skip if already an array:
					if(is_array($member_obj)) continue;
					
					//remove unwanted fields:
					foreach ($arr_remove as $remove) {
						if(isset($member_obj->$remove))
							unset($member_obj->$remove);
					}
					//reassign aas array:				
					$mgm_member->other_membership_types[$key] = mgm_convert_memberobj_to_array($member_obj);				
				}
				//save mgm_member object
				//mgm_log('resaved:' . mgm_array_dump($mgm_member, true));			
			}else {
				$mgm_member->other_membership_types = array();
			}
			//save		
			$mgm_member->save();
		}
	}
}
 // end file