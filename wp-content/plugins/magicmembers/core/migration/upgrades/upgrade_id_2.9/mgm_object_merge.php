<?php
/** 
 * Objects merge/update
 */ 
 
 // system object updates
 $mgm_system = new mgm_system();
 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 // membership_details_url
 if(!isset($mgm_system_cached->setting['membership_details_url'])){
 	$mgm_system_cached->setting['membership_details_url'] = $mgm_system->setting['membership_details_url'];
 }
  
 // update
 update_option('mgm_system', $mgm_system_cached);
 
 // ends