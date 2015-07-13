<?php
/**
 * Magic Members admin profile module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_profile extends mgm_controller{
 	
	// construct
	function mgm_profile()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();		
		// load template view
		$this->load->template('profile', array('data'=>$data));		
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_profile.php