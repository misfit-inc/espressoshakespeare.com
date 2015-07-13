<?php 
/**
 * Magic Members roles utility class
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_roles{
	public $admin_role			= 'administrator';
	public $basic_role			= 'subscriber';
	public $default_roles 		= array('administrator', 'editor', 'author', 'contributor', 'subscriber');
	public $default_levels 		= array('level_0','level_1', 'level_2','level_3','level_4','level_5','level_6','level_7','level_8','level_9','level_10');
	public $blocked_capabilities = array('install_plugins','delete_plugins', 'delete_users','delete_themes','level_4','level_5','level_6','level_7','level_8','level_9','level_10');
	public $use_db				= true;
	public $add_capability		= false;
	private $arr_custom_capabilities = array();
	public $role_type			= 'mgm';
	
	// construct
	function __construct(){
		// php4 proxy
		$this->mgm_roles();
	}
	
	// php4 construct
	function mgm_roles(){
		// stuff
	}
	
	//fetch roles:
	function get_roles() {
		global $wp_roles, $current_user;
		$wp_roles->use_db = $this->use_db;		
		$arr_roles = array();
		$i = 0;		
		//get only mgm and default roles:		
		$arr_mgmroles 		= $this->_get_mgm_roles();
		$arr_defaultroles 	= $this->_get_default_roles();		
		$arr_capabilities 	= $this->get_all_capabilities(array_merge($arr_mgmroles, $arr_defaultroles));				
		
		foreach ($wp_roles->roles as $role => $content) {
			if($this->role_type == 'mgm') {
				if(!in_array($role, $arr_mgmroles)) continue;
			}elseif ($this->role_type == 'default') {
				if(!in_array($role, $arr_defaultroles)) continue;
			}elseif ($this->role_type == 'others') {
				if(in_array($role, array_merge( $arr_mgmroles, $arr_defaultroles ))) continue;					
			}			
				 			
			$obrole = $wp_roles->get_role($role);
			$arr_roles[$i]['role'] = $role;
			$arr_roles[$i]['name'] = $content['name'];
			$arr_roles[$i]['permitted'] = (in_array($role, $current_user->roles) || in_array($this->admin_role, $current_user->roles ) ) ? 1 : 0;
			$arr_roles[$i]['is_systemrole'] = in_array($role, $this->default_roles ) ? 1 : 0;
			
			if(!empty($arr_capabilities)) {
				$j = 0;
				foreach ($arr_capabilities as $cap => $capcontent) {					
					$capability = is_numeric($cap) ? $capcontent : $cap;					
					$arr_roles[$i]['capabilities'][$j]['blocked'] = in_array($capability, $this->blocked_capabilities) ? 1 : 0 ;						
					$capability_name = ucfirst(str_replace('_', ' ', $capability));
					$arr_roles[$i]['capabilities'][$j]['capability'] = $capability;
					$arr_roles[$i]['capabilities'][$j]['name'] = $capability_name;
					if($obrole->has_cap($capability)) {
						$belongsto = 1;
					}else {
						$belongsto = 0;
					}
					$arr_roles[$i]['capabilities'][$j]['belongsto'] = $belongsto;					
					$j++;
				}
			}
			$i++;
		}		
		return $arr_roles;
	}
	//get full set of capabilities
	function get_all_capabilities($arrRoles = array()) {
		global $wp_roles;
		$wp_roles->use_db = $this->use_db;
		$capabilities = array();		
		foreach($wp_roles->role_objects as $r => $role) {
			if($role->capabilities) {				
				//include only capabilities belongs to roles				
				if(!empty($arrRoles)) {
					if(($this->role_type == 'mgm' || $this->role_type == 'default')) {
						if(!in_array($r, $arrRoles)) continue;
					}elseif($this->role_type == 'others') {
						if(in_array($r, $arrRoles)) continue;
					}					
				}
		      foreach($role->capabilities as $cap => $content) {
		      	$cap = is_numeric($cap) ? $content : $cap;
		        $capabilities[$cap] = $cap;		       
		      }
			}
	    }
	    //remove levels
	    $capabilities = array_diff($capabilities, $this->default_levels);
	    $capabilities = array_unique($capabilities);
	    sort($capabilities);
	    return $capabilities;
	}
	//fetch capability for a role
	function get_capabilities($role) {
		global $wp_roles;
		$arr_return = array(); 
		$wp_roles->use_db = $this->use_db;
		$arr_role = $wp_roles->get_role($role);
		if($arr_caps = $arr_role->capabilities) {			
			foreach ($arr_caps as $key => $value) {
				if(!in_array($key, $this->default_levels))
					$arr_return[] = $key; 
			}
		}
		return $arr_return;
	}
	
	//edit/rename role
	function edit_role($oldRole, $newRole) {
		global $wp_roles;
		$wp_roles->use_db = $this->use_db;
		$new_role = str_replace(" ", "_", strtolower($newRole));
		if(!in_array($oldRole, $this->default_roles )) {
			if( $new_role != $oldRole ) {
				if($wp_roles->is_role($oldRole) && !$wp_roles->is_role($new_role) ) {					
					//check role name is same as before:
					if( $wp_roles->role_names[ $oldRole ] != $newRole) {
						$objold = $wp_roles->get_role($oldRole);
						//create new role with old role's capabilites:
						$wp_roles->add_role($new_role, $newRole, $objold->capabilities );
						//add role to db:
						$arr_roles = get_option('mgm_created_roles');
						if(empty($arr_roles)) $arr_roles = array();
						array_push($arr_roles, $new_role);
						update_option('mgm_created_roles', $arr_roles);	
													
						//update users with new role(delete previous role)
						$this->remove_role( $oldRole, $new_role );
						return $new_role;
					}
				}
			}
		}	
		return $oldRole;	
	}
	//create a new role:
	function add_role($roleName, $capabilities) {
		global $wp_roles;
		$wp_roles->use_db = $this->use_db;
		$roleName = trim($roleName);
		$role = str_replace(" ", "_", strtolower($roleName));		
		if(!in_array($role, $this->default_roles )) {
			if(!$wp_roles->is_role($role) ) {
				if(!empty($capabilities))
					$capabilities = $this->_assign_true_to_keys($capabilities);
				$wp_roles->add_role( $role, $roleName, $capabilities );
				//add role to db:
				$arr_roles = get_option('mgm_created_roles');
				if(empty($arr_roles)) $arr_roles = array();
				array_push($arr_roles, $role);
				update_option('mgm_created_roles', $arr_roles);
				return true;
			}
		}
		return false;
	}
	
	//remove/delete a role:
	function remove_role($roleToRemove, $newRole = '') {
		global $wp_roles;		
		if(empty($newRole)) $newRole = $this->basic_role;
		$wp_roles->use_db = $this->use_db;
		if( !in_array($roleToRemove, $this->default_roles) ) {
			//update users with new role
			$arr_users = $this->_get_user_ids();
			foreach ($arr_users as $uid) {
				$user = new WP_User($uid);
				if(in_array($roleToRemove, $user->roles)) {
					//add new role to the user:
					$user->roles = $this->_assign_true_to_keys($user->roles);					
					$user->add_role($newRole);					
					//remove old role:
					$user->roles = $this->_assign_true_to_keys($user->roles);					
					$user->remove_role($roleToRemove);
				}
			}			
			$wp_roles->remove_role($roleToRemove);
			
			//update mgm packages:			
			if($newRole != '') {
				$update_pack = 0;
				$packs_obj = mgm_get_class('subscription_packs');				
				if(isset($packs_obj->packs) && count($packs_obj->packs) > 0) {
					$pack_count = count($packs_obj->packs);
					for($i = 0;  $i < $pack_count; $i++ ) {
						if(isset($packs_obj->packs[$i]['role']) && $packs_obj->packs[$i]['role'] == $roleToRemove) {
							$packs_obj->packs[$i]['role'] = $newRole;
							$update_pack++;
						}
					}
				}
				if($update_pack > 0) {					
					// update_option('mgm_subscription_packs', $packs_obj);
					$packs_obj->save();
				}
			}			
			
			//update db:
			$arr_roles = get_option('mgm_created_roles');	
			if(empty($arr_roles)) $arr_roles = array();		
			update_option('mgm_created_roles', array_diff($arr_roles, array($roleToRemove)));
			return true;	
		}
		return false;
	}
	//move Role's users to another role:
	function move_users($roleToRemove, $newRole = '') {
		global $wp_roles;		
		if(empty($newRole)) $newRole = $this->basic_role;
		$wp_roles->use_db = $this->use_db;		
		//update users with new role
		$arr_users = $this->_get_user_ids();
		foreach ($arr_users as $uid) {
			$user = new WP_User($uid);
			if(in_array($roleToRemove, $user->roles)) {
				//add new role to the user:
				$user->roles = $this->_assign_true_to_keys($user->roles);					
				$user->add_role($newRole);					
				//remove old role:
				$user->roles = $this->_assign_true_to_keys($user->roles);					
				$user->remove_role($roleToRemove);
			}
		}					
		return true;		
	}
	//add capability:
	function add_capability() {
		if(!$this->add_capability)
			return;
		global $wp_roles;		
		$role = $wp_roles->get_role($this->admin_role);
		$arr_capabilities = $this->get_all_capabilities();
		foreach ($this->arr_custom_capabilities as $cap) {	
			if(!in_array($cap, $arr_capabilities)) {		
				$role->add_cap($cap);
				//update db:
				$arr_caps = get_option('mgm_capabilities');	
				if(!is_array($arr_caps)) $arr_caps = array();		
				update_option('mgm_created_roles', array_push($arr_caps, $cap));	
			}
		}		
	}
	// assign a capability to a role:
	function update_capability_role($role, $capability, $access = true) {
		global $wp_roles;
		$wp_roles->use_db = $this->use_db;
		//give access:
		if( $access ) {
			$wp_roles->add_cap($role, $capability, true);			
		}else {
			$wp_roles->remove_cap($role, $capability);			
		}		
	}
	//get userids - sitewide
	private function _get_user_ids() {		
	    global $wpdb;	    
	    //from cache
		$uids = wp_cache_get('all_user_ids', 'users');	 
		if(!$uids) {	    
			//$uids = $wpdb->get_col('SELECT ID from ' . $wpdb->users);
			$uids = mgm_get_all_userids();
	    	wp_cache_set('all_user_ids', $uids, 'users');
		}	    	    
	    return $uids;
	}
	//flip array and assign true to keys
	private function _assign_true_to_keys($roles) {
		$roles = array_flip($roles);
		foreach ($roles as $key => $value)
			$roles[$key] = true;
		return $roles;	
	}
	//check role is unique
	function is_role_unique($rolename, $edit = false, $prevRole = null) {
		global $wp_roles;			
		$rolename = trim($rolename);	
		$rolename_rep = str_replace(" ", "_", strtolower($rolename));
		foreach ($wp_roles->role_names as $role => $name) {			
			if(!$edit && ($rolename_rep == $role || $rolename == $name) ) {
				return false;			
			}elseif( $edit && $prevRole != $role && ($rolename_rep == $role || $rolename == $name) ) {
				return false;	
			}elseif(in_array($rolename_rep,$this->get_all_capabilities()))
				return false;
		}
		return true;
	}
	//get mgm and default roles
	function _get_mgm_roles() {		
		$arr_mgm_roles = get_option( 'mgm_created_roles' );
		if(!is_array($arr_mgm_roles))
			$arr_mgm_roles = array();		
		return $arr_mgm_roles;
	}
	//default roles
	function _get_default_roles() {
		return $this->default_roles;
	}	
	//default capabilities
	function get_mgm_default_capabilities() {
		$arr_mgmroles 		= $this->_get_mgm_roles();
		$arr_defaultroles 	= $this->_get_default_roles();		
		return $this->get_all_capabilities(array_merge($arr_mgmroles, $arr_defaultroles));
	}
	//assign role to user:
	function add_user_role($user_id, $role, $update_order = true, $remove_role = true) {
		global $wp_roles;	
		$wp_roles->use_db = $this->use_db;
		$user = new WP_User($user_id);
		if(!empty($role)) {			
			if(!in_array($role, $user->roles)) { 				
				$user->roles = $this->_assign_true_to_keys($user->roles);
				$user->add_role($role);							
			}
			//check to remove any unwanted roles
			if($remove_role) {				
				mgm_remove_excess_user_roles($user_id);			
			}
			
			//change role order			
			if($update_order) {				
				//$this->_reverse_roles($user_id);	
				$this->highlight_role($user_id, $role);
			}				
		}		
	}
	//reverse role order:
	private function _reverse_roles($user_id) {
		global $wp_roles;	
		$wp_roles->use_db = $this->use_db;
		$user = new WP_User($user_id);		
		$user->caps = array_reverse($user->caps);
		update_user_meta( $user->ID, $user->cap_key, $user->caps );	
	}
	//replace $remove_role with $default_role;
	function replace_user_role($user_id, $remove_role, $default_role ) {
		global $wp_roles;	
		$wp_roles->use_db = $this->use_db;
		$user = new WP_User($user_id);	
		//remove user role:
		$user->remove_role($remove_role);		
		//add default role:
		$this->add_user_role($user_id, $default_role,false);		
	}
	//to highlight a selected role: set the role's index as 0
	function highlight_role($user_id, $role) {
		global $wp_roles;	
		$wp_roles->use_db = $this->use_db;
		$user = new WP_User($user_id);								
		$caps = array_keys($user->caps);		
		if(!empty($caps) && count($caps) > 0 && in_array($role, $caps)) {			
			$first_role = $caps[0];
			if($first_role == $role)
				return;
			$role_index = array_search($role, $caps);
			$caps[$role_index] = $first_role;			
			$caps[0] = $role;
			$new_cap = array();		
			foreach ($caps as $cap)
				$new_cap[$cap] = true;
				
			$user->caps = $new_cap; 			
			update_user_meta( $user->ID, $user->cap_key, $user->caps );				
		}
	}
	//test function:
	function print_role($user_id) {
		$roles = $this->get_user_role($user_id);
		// mgm_log('PRINTING USER ROLES:');		
		if(!empty($roles)) {			
			mgm_log(mgm_array_dump($roles, true));
		}
	}
	//fetch user role:
	function get_user_role($user_id) {
		global $wp_roles;	
		
		$wp_roles->use_db = $this->use_db;
		$user = new WP_User($user_id);			
		if(!empty($user->roles))
			return $user->roles;
			
		return array();	
	}
	//directly remove role from user
	function remove_userrole($user_id, $role) {
		global $wp_roles;	
		$wp_roles->use_db = $this->use_db;
		$user = new WP_User($user_id);	
		//remove user role:
		$user->remove_role($role);
	}	
}
// core/libs/utilities/mgm_roles.php