<?php
/** 
 * Objects merge/update
 */ 
 // saved object
 $mgm_system_cached = mgm_get_option('system');
 
 // set new vars
 if(!isset($mgm_system_cached->setting['thumbnail_image_width'])){
 	$mgm_system_cached->setting['thumbnail_image_width'] = '32'; 	
 }
  if(!isset($mgm_system_cached->setting['thumbnail_image_height'])){
 	$mgm_system_cached->setting['thumbnail_image_height'] = '32'; 	
 }
  if(!isset($mgm_system_cached->setting['medium_image_width'])){
 	$mgm_system_cached->setting['medium_image_width'] = '120'; 	
 }
  if(!isset($mgm_system_cached->setting['medium_image_height'])){
 	$mgm_system_cached->setting['medium_image_height'] = '120'; 	
 }
  if(!isset($mgm_system_cached->setting['image_size_mb'])){
 	$mgm_system_cached->setting['image_size_mb'] = '2'; 	
 }	
 // update
 update_option('mgm_system', $mgm_system_cached);
 
 // ends