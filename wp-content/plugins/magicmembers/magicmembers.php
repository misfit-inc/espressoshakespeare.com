<?php

/*

 Plugin Name: Magic Members 

 Plugin URI: http://www.magicmembers.com/

 Description: Magic Members is a premium Wordpress Membership Plugin that turn your WordPress blog into a powerful, fully automated membership site.

 Author: Magical Media Group

 Author URI: http://www.magicalmediagroup.com/

 Version: 1.7.16

 Build: 2.2.5

 Distribution: 11/08/2011

 */ 

 // buffer for ajax

 if(((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]  == 'XMLHttpRequest') || isset($_FILES)) && !headers_sent()) ob_start(); 

 // versioned core: for loading different versions from single installation 

 $core = 'core'; 

 // reset

 if($version = get_option('mgm_core_version')) $core = 'core-'.$version; 

 // load init class 

 $mgm_init_cls = include_once( $core . '/mgm_init.php'); 

 // init

 $mgm_init = new $mgm_init_cls;

 // setup

 $mgm_init->setup();

 // end

?>