<?php
/**
 * Magic Members admin support docs module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_support_docs extends mgm_controller{
 	
	// construct
	function mgm_support_docs()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();
		// load template view
		$this->load->template('support_docs', array('data'=>$data));		
	}
	
	// generalinfo
	function generalinfo(){		
		// data
		$data = array();		
		// load template view
		$this->load->template('generalinfo', array('data'=>$data));			
	}
	
	// troubleshooting
	function troubleshooting(){		
		// data
		$data = array();		
		// load template view
		$this->load->template('troubleshooting', array('data'=>$data));		
	}
	
	// tutorials
	function tutorials(){		
		// data
		$data = array();		
		// load template view
		$this->load->template('tutorials', array('data'=>$data));	
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_support_docs.php