<?php
/**
 * Magic Members membership types
 * extends object to save options to database
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_membership_types extends mgm_object{
	// membership types
	var $membership_types  = array();
	var $login_redirects   = array();
	var $logout_redirects  = array();
	var $capability_orders = array();//TODO order	
	
	// construct
	function __construct($membership_types=false){
		// php4
		$this->mgm_membership_types($membership_types);
	}
	
	// construct
	function mgm_membership_types($membership_types=false){
		// parent
		parent::__construct(); 
		// defaults
		$this->_set_defaults($membership_types);
		// read vars from db
		$this->read();// read and sync			
	}
	
	// defaults
	function _set_defaults($membership_types=false){
		// code
		$this->code        = __CLASS__;
		// name
		$this->name        = 'Membership Types Lib';
		// description
		$this->description = 'Membership Types Lib';
		
		// set from argument
		if(!is_array($membership_types)){
			$membership_types = array('guest'=>'Guest', 'trial'=>'Trial', 'free'=>'Free', 'member'=>'Member');
		}				
		// set
		$this->set_membership_types($membership_types);	
	}
	
	// set multiple
	function set_membership_types($membership_types) {
		if(is_array($membership_types))
			$this->membership_types = $membership_types;
	}
	
	// set single
	function set_membership_type($type) {
		// check duplicate value
		if(!in_array($type, array_values($this->membership_types))){
			// get code			
			$type_code = $this->get_type_code($type);
			// merge to old array
			$this->membership_types = array_merge($this->membership_types, array($type_code => $type)); 
			// treat success
			return true;
		}else{
			// error duplicate
			return false;	
		}	
	}
	
	// unset single
	function unset_membership_type($type_code) {
		// remove
		if(array_search($type_code, array_keys($this->membership_types)) !== false){
			// unset
			unset($this->membership_types[$type_code]);
			// treat success
			return true;
		}
		// trteat error
		return false;
	}
	
	// get code
	function get_type_code($type){
		//return strtolower(preg_replace('/\s+/', '_', $type));
		return strtolower(preg_replace('/\W+/', '_', $type));
	}
	
	// get type
	function get_type_name($type_code){
		// def
		$type_name = ucwords(str_replace('_', ' ', $type_code));
		// search and match
		foreach($this->membership_types as $code=>$name){
			if($code == $type_code){
				$type_name = $name;
			}		
		}	
		// ret
		return $type_name;
	}
	
	// get nicecode
	function get_type_nicecode($type){
		return strtolower(preg_replace('/[\_]\s+/', '-', $type));
	}
	
	// get all types
	function get_membership_types(){
		return $this->membership_types;
	}
	
	// set one login redirect
	function set_login_redirect($type_code, $redirect){
		// set if provided
		if(isset($redirect)){ 
			$this->login_redirects[$type_code] = trim($redirect);	
		}else{
			$this->login_redirects[$type_code] = '';
		}	
	} 
	
	// set multiple login redirects
	function set_login_redirects($redirects){
		// set if provided
		if(is_array($redirects)){			
			$this->login_redirects = array_merge($this->login_redirects,$redirects);
		}	
	} 
	// set one login redirect
	function set_logout_redirect($type_code, $redirect){
		// set if provided
		if(isset($redirect)){ 
			$this->logout_redirects[$type_code] = trim($redirect);	
		}else{
			$this->logout_redirects[$type_code] = '';
		}	
	} 
	
	// set multiple login redirects
	function set_logout_redirects($redirects){
		// set if provided
		if(is_array($redirects)){			
			$this->logout_redirects = array_merge($this->logout_redirects,$redirects);
		}	
	}
	
	// get one login redirect
	function get_login_redirect($type_code){
		// set if provided
		if(isset($this->login_redirects[$type_code])){ 
			return $this->login_redirects[$type_code];	
		}else{
			return '';
		}	
	} 
	
	// get all login redirects
	function get_login_redirects(){
		// set if provided
		if(is_array($this->login_redirects)){ 
			return $this->login_redirects;	
		}else{
			return array();
		}	
	}
	// get one login redirect
	function get_logout_redirect($type_code){
		// set if provided
		if(isset($this->logout_redirects[$type_code])){ 
			return $this->logout_redirects[$type_code];	
		}else{
			return '';
		}	
	}  
	// get all login redirects
	function get_logout_redirects(){
		// set if provided
		if(is_array($this->logout_redirects)){ 
			return $this->logout_redirects;	
		}else {
			return array();
		}	
	}
	
	// fix
	function apply_fix($old_obj){
		// to be copied vars
		$vars = array('membership_types','login_redirects','logout_redirects','capability_orders');
		// set
		foreach($vars as $var){
			// var
			$this->{$var} = (isset( $old_obj->{$var} ) ) ? $old_obj->{$var} : '';
		}				
		// save
		$this->save();	
	}
	
	// prepare save, define the object vars to be saved
	// internally called by object->save()
	function _prepare(){		
		// init array
		$this->options = array();
		// to be saved vars
		$vars = array('membership_types','login_redirects','logout_redirects','capability_orders');
		// set
		foreach($vars as $var){
			// var
			$this->options[$var] = $this->{$var};
		}	
	}
	
	/**
	 * Overridden function:	
	   See the comment below:
	 *
	 * @param string $option_name
	 * @param array $current_value current value for class var(can be default)
	 * @param array $option_value: updated value
	 */
	function _option_merge_callback($option_name, $current_value, $option_value) {		
		//This is to make sure that the default membership_type array doesn;t contain the hardcoded option 'member' incase user deletes it and option array doesn't have it.
		//issue#: 521
		if($option_name == 'membership_types') {
			//This is to copy from options:
			$current_value = array();			
		}
		//update class var
		$this->{$option_name} = mgm_array_merge_recursive_unique($current_value,$option_value);
	}
}
// core/libs/classes/mgm_membership_types.php