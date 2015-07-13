<?php
/**
 * Magic Members api members controllers
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_api_members extends mgm_api_controller{
 	// construct
	function __construct(){
		// php4
		$this->mgm_api_members();
	}
	
	// php4
	function mgm_api_members(){
		// parent
		parent::__construct();
	}
	
	// get method
	// statuses OR statuses_get
	function test_get($id=0){
		// params		
		$params = array('id' => $id);		
		// return
		return array(array('status' => true, 'message'=>'GET Verb request', 'data' => $this->request->data['get'], 'params'=>$params), 200);
		/*// global
		global $wpdb;
		// sanitize;
		$user_id = (int)$id;
		// data
		$data = array('error' => __('No such user.','mgm'));
		// get user data
		if(is_super_admin($user_id)){
			// error
			return array(array('status' => false, 'error' => __('Administrator data is protected.','mgm')), 403);
		}else{
		// get user data
			$userdata = get_userdata($user_id);
			// validate
			if($userdata->ID){
				// unset some
				unset($userdata->user_pass,$userdata->mgm_member);				
				// set
				return array(array('status' => true, 'data' => $userdata), 200);
			}				 
		}	
		// error
		return array(array('status' => false, 'error' => __('Member not found.','mgm')), 404);	*/			
	}
	
	// post
	function test_post(){
		// return
		return array(array('status' => true, 'message'=>'POST Verb request', 'data' => $this->request->data['post']), 200);
	}
	
	// put
	function test_put(){
		// return
		return array(array('status' => true, 'message'=>'PUT Verb request', 'data' => $this->request->data['put']), 200);
	}
	
	// delete
	function test_delete(){
		// return
		return array(array('status' => true, 'message'=>'DELETE Verb request', 'data' => $this->request->data['delete']), 200);
	}
 }
 
 // return name of class 
 return basename(__FILE__,'.php');
// end file /core/api/mgmapi_members.php