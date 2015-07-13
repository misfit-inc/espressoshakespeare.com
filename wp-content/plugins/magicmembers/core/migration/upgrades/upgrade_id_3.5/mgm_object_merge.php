<?php
/** 
 * Objects merge/update
 */ 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 // enable_autologin
 if(!isset($mgm_system_cached->setting['enable_multiple_level_purchase'])){
 	$mgm_system_cached->setting['enable_multiple_level_purchase'] = 'N'; 	
 }  	
 // update
 update_option('mgm_system', $mgm_system_cached);
 
 // ends