<?php
/**
 * Magic Members admin base module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_admin extends mgm_controller{
 	
	// construct
	function __construct(){
		// php4
		$this->mgm_admin();
	}
	
	// php4 construct
	function mgm_admin(){		
		// load parent
		parent::__construct();
	}	
	
	// index
	function index(){
		// data
		$data = array();
		// load template view
		$this->load->template('admin', array('data'=>$data));		
	}
		
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_admin.php 