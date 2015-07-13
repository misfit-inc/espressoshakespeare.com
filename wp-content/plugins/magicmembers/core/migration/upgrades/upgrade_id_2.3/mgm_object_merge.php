<?php
/** 
 * Objects merge/update
 */ 
 
 // system object updates
 $mgm_system = new mgm_system();
 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 // register url
 if(!isset($mgm_system_cached->setting['register_url'])){
 	$mgm_system_cached->setting['register_url'] = $mgm_system->setting['register_url'];
 }
 // profile url
 if(!isset($mgm_system_cached->setting['profile_url'])){
 	$mgm_system_cached->setting['profile_url'] = $mgm_system->setting['profile_url'];
 }
 // transactions url
 if(!isset($mgm_system_cached->setting['transactions_url'])){
 	$mgm_system_cached->setting['transactions_url'] = $mgm_system->setting['transactions_url'];
 } 
  
 // update
 update_option('mgm_system', $mgm_system_cached);
 
 // read 