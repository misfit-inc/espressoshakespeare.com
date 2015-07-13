<?php
/** 
 * Objects merge/update
 */ 
 
 // system object updates
 $mgm_system = new mgm_system();
 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 // subject
 if(!isset($mgm_system_cached->setting['reminder_email_template_subject'])){
 	$mgm_system_cached->setting['reminder_email_template_subject'] = $mgm_system->setting['reminder_email_template_subject'];
 }
 
 // body
 if(!isset($mgm_system_cached->setting['reminder_email_template_body'])){
	 $mgm_system_cached->setting['reminder_email_template_body'] = $mgm_system_cached->setting['reminder_email_template'];
	 unset($mgm_system_cached->setting['reminder_email_template']);// unset old
 }
 
 // subject
 if(!isset($mgm_system_cached->setting['registration_email_template_subject'])){
 	$mgm_system_cached->setting['registration_email_template_subject'] = $mgm_system->setting['registration_email_template_subject'];
 }
 
 // body
 if(!isset($mgm_system_cached->setting['registration_email_template_body'])){
	 $mgm_system_cached->setting['registration_email_template_body'] = $mgm_system_cached->setting['registration_email_template'];
	 unset($mgm_system_cached->setting['registration_email_template']); // unset old
 }
  
 // update
 // update_option('mgm_system', $mgm_system_cached);
 
 // read 