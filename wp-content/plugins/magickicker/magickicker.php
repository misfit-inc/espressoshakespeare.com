<?php

/*

 Plugin Name:Magic Kicker

 Plugin URI: http://www.magicmembers.com/

 Description: Magic Kicker Plugin stops multiple logins from occuring by logging out the user on page load if their ip differs from the one on file

 Author: Magical Media Group

 Author URI: http://www.magicmembers.com/

 Version: 1.2

 Last Updated: 07/08/2011

 */



 // buffer for ajax

 if(((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]  == 'XMLHttpRequest') || isset($_FILES)) && !headers_sent()) ob_start();

 

 // load bootstrap

 require_once('core/mgk_init.php');

 // end of file