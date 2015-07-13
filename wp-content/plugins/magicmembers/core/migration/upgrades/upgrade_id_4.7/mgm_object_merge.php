<?php
/** 
 * Objects merge/update
 */ 
 // read  
//update nested shortcode parsing:
$system = mgm_get_class('system');
if(!isset($system->setting['enable_post_url_redirection'])) {
	$system->setting['enable_post_url_redirection'] = 'N';
	$system->save();
}
 // end file