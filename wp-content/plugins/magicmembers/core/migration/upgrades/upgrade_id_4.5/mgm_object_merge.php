<?php
/** 
 * Objects merge/update
 */ 
 // read  
$obj_packs = mgm_get_option('subscription_packs');

if(!empty($obj_packs->duration_str) && !in_array('Lifetime', $obj_packs->duration_str )) {
	$obj_packs->duration_str['l'] = 'Lifetime';
	update_option('mgm_subscription_packs', $obj_packs);
}
 // end file