<?php
/**
 * Magic Members admin activation module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_activation extends mgm_controller{
 	
	// construct
	function mgm_activation()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();		
		// load template view
		$this->load->template('activation/index', array('data'=>$data));		
	}
	
	// activate
	function activate(){
		global $wpdb;
		// 	local
		extract($_POST);
		// post
		if(isset($btn_activate)){
			// default
			$status = 'error';
			// check
			if(!empty($email)){							
				// validate
				$message = mgm_get_class('auth')->validate_subscription($email);	
				// check
				if($message===true){
					$status  = 'success';
					$message = __('Your account has been activated.','mgm');
				}
			}else{			
				$message = __('Email is not provided.','mgm');
			}				
			// return response
			echo json_encode(array('status'=>$status, 'message'=>$message));exit();
		}
		
		// data
		$data = array();				
		// load template view
		$this->load->template('activation/activate', array('data'=>$data));	
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_activation.php