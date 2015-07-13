<?php
/**
 * Magic Members autoresponders admin module
 * not used
 *
 * @package MagicMembers
 * @since 2.0
 */

 class mgm_autoresponders extends mgm_controller{
 	
	// construct
	function mgm_autoresponders()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();		
		// load template view
		$this->load->template('autoresponders/index', array('data'=>$data));		
	}		
	
	// lists
	function autoresponders_lists(){
		// data
		$data = array();		
		// get modules
		$data['autoresponders'] = mgm_get_available_modules('autoresponder');		
		// autoresponders
		foreach($data['autoresponders'] as $autoresponder){				
			// get module
			$module_obj = mgm_get_module('mgm_'.$autoresponder,'autoresponder');											
			// get html
			$data['autoresponder'][$autoresponder] = $module_obj->settings();
		}
		// membership types
		$data['membership_types'] = mgm_get_class('membership_types')->membership_types;		
		// load template view
		$this->load->template('autoresponders/lists', array('data'=>$data));	
	}
	
	// module settings
	function module_settings(){		
		// make local
		extract($_REQUEST);				
		// get module
		$module_class = mgm_get_module($module, 'autoresponder');	
		// update
		if(isset($update) && $update=='true'){
			// settings update
			$module_class->settings_update();
		}else{		
			// load settings form
			$module_class->settings();
		}				
	}
	
	/*// autoresponder update
	function autoresponder_update(){
		// get object
		$subscription_packs = mgm_get_class('subscription_packs');
		// set
		$subscription_packs->set_packs($_POST['packs']);
		// save to database
		// update_option('mgm_subscription_packs', $subscription_packs);	
		$subscription_packs->save();	
		// message
		$message = sprintf(__('Successfully updated %d membership packages.', 'mgm'), count($_POST['packs']));
		$status  = 'success';
		// return response		
		echo json_encode(array('status'=>$status, 'message'=>$message));			
		exit();
	}
	
	// autoresponder delete
	function autoresponder_delete(){
		extract($_POST);
		// get object
		$subscription_packs = mgm_get_class('subscription_packs');
		// match
		if(isset($subscription_packs->packs[$index])){
			# empty
			$packs = array();
			foreach( $subscription_packs->packs as $i=>$pack){
				if($i==$index)
					continue;
				
				$packs[] = $pack;	
			}	
			// set 
			$subscription_packs->set_packs($packs);		
			// update
			// update_option('mgm_subscription_packs', $subscription_packs);	
			$subscription_packs->save();		
			// message
			$message = sprintf(__('Successfully updated %d membership packages.', 'mgm'), count($_POST['packs']));
			$status  = 'success';
		}else{
			$message = __('Error while removing pack', 'mgm');
			$status  = 'error';
		}		
		
		// return response		
		echo json_encode(array('status'=>$status, 'message'=>$message));			
		exit();
	}	*/
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_autoresponders.php