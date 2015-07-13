<?php
/**
 * Magic Members admin upload/download module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_files extends mgm_controller{
 	
	// construct
	function mgm_files()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){	
		// clean file
		$file = str_replace(array("\\","/"), DIRECTORY_SEPARATOR, urldecode($_GET['file']));		
		// print_r($_GET);
		// type
		$type= $_GET['type'];
		// get type
		switch($type){
			case 'download':
				// buffer
				ob_end_clean();
				// download				
				mgm_force_download($file);
				ob_end_flush();exit();
			break;
			case 'upload':
				echo 'upload';
				exit;
			break;
		}
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_files.php 