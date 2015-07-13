<?php
/**
 * Magic Members admin home module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_home extends mgm_controller{
 	
	// construct
	function mgm_home()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();		
		// load template view
		$this->load->template('dashboard/index', array('data'=>$data));		
	}
	
	// dashboard
	function dashboard(){	
		// data
		$data = array();				
		// load template view
		$this->load->template('dashboard/widgets', array('data'=>$data));	
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_home.php