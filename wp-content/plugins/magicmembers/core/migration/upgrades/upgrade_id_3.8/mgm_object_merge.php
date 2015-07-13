<?php
/** 
 * Objects merge/update
 */ 
 // saved object
$mgm_system_cached = mgm_get_option('system');
 
// set new vars
if(!isset($mgm_system_cached->setting['recaptcha_public_key'])){
	$mgm_system_cached->setting['recaptcha_public_key'] = ''; 	
}
if(!isset($mgm_system_cached->setting['recaptcha_private_key'])){
	$mgm_system_cached->setting['recaptcha_private_key'] = ''; 	
}
if(!isset($mgm_system_cached->setting['recaptcha_api_server'])){
	$mgm_system_cached->setting['recaptcha_api_server'] = 'http://www.google.com/recaptcha/api'; 	
}
if(!isset($mgm_system_cached->setting['recaptcha_api_secure_server'])){
	$mgm_system_cached->setting['recaptcha_api_secure_server'] = 'https://www.google.com/recaptcha/api'; 	
}
if(!isset($mgm_system_cached->setting['recaptcha_verify_server'])){
	$mgm_system_cached->setting['recaptcha_verify_server'] = 'www.google.com'; 	
}	
 // update
 update_option('mgm_system', $mgm_system_cached);