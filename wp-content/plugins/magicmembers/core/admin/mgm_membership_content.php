<?php
/**
 * Magic Members admin membership content module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_membership_content extends mgm_controller{
 	
	// construct
	function mgm_membership_content()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){		
		// data
		$data = array();
		// membership level
		$data['membership_level'] = mgm_get_user_membership_type();		
		// load template view
		$this->load->template('membership_content', array('data'=>$data));		
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_membership_content.php