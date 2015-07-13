<?php
/** 
 * Objects merge/update
 */ 
 
 // system object updates
 $mgm_system = new mgm_system();
 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 // membership_contents_url
 if(!isset($mgm_system_cached->setting['membership_contents_url'])){
 	$mgm_system_cached->setting['membership_contents_url'] = $mgm_system->setting['membership_contents_url'];
 }
 
 // date_range_lower
 if(!isset($mgm_system_cached->setting['date_range_lower'])){
 	$mgm_system_cached->setting['date_range_lower'] = $mgm_system->setting['date_range_lower'];
 }
 
 // date_range_upper
 if(!isset($mgm_system_cached->setting['date_range_upper'])){
 	$mgm_system_cached->setting['date_range_upper'] = $mgm_system->setting['date_range_upper'];
 }
 
 // date_farmat
 if(!isset($mgm_system_cached->setting['date_farmat'])){
 	$mgm_system_cached->setting['date_farmat'] = $mgm_system->setting['date_farmat'];
 }
 
 // date_farmat_long
 if(!isset($mgm_system_cached->setting['date_farmat_long'])){
 	$mgm_system_cached->setting['date_farmat_long'] = $mgm_system->setting['date_farmat_long'];
 }
 
 // date_farmat_short
 if(!isset($mgm_system_cached->setting['date_farmat_short'])){
 	$mgm_system_cached->setting['date_farmat_short'] = $mgm_system->setting['date_farmat_short'];
 } 
 // update
 update_option('mgm_system', $mgm_system_cached);
 
 // ends