<?php
/** 
 * Objects merge/update
 */ 
 
 // system object updates
 $mgm_system = new mgm_system();
 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 // login url
 if(!isset($mgm_system_cached->setting['login_url'])){
 	$mgm_system_cached->setting['login_url'] = $mgm_system->setting['login_url'];
 }
 // profile url
 if(!isset($mgm_system_cached->setting['lostpassword_url'])){
 	$mgm_system_cached->setting['lostpassword_url'] = $mgm_system->setting['lostpassword_url'];
 }
  
 // update
 update_option('mgm_system', $mgm_system_cached);
 
 // read 