<?php
/**
 * Magic Members subscription packages class
 * extends object to save options to database
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_subscription_packs extends mgm_object{
	// packs
	var $packs;
	// duration str, not to save
	var $duration_str = array();
	// next id
	var $next_id = 4;
	
	// construct
	function __construct($packs=false){
		// php4
		$this->mgm_subscription_packs($packs);
	}
	
	// construct
	function mgm_subscription_packs($packs=false){
		// parent
		parent::__construct(); 
		// defaults
		$this->_set_defaults($packs);
		// read vars from db
		$this->read();// read and sync			
	}
	
	// defaults
	function _set_defaults($packs=false){
		// code
		$this->code        = __CLASS__;
		// name
		$this->name        = 'Subscription Packs Lib';
		// description
		$this->description = 'Subscription Packs Lib';
		
		// set from argument
		if(!is_array($packs))
			$packs = $this->base_packs();		
			
		// set
		$this->set_packs($packs);	
		
		// duration
		$this->duration_str = array('d'=>'Day', 'm'=>'Month', 'y'=>'Year','l' => 'Lifetime');
	}
		
	// set multiple
	function set_packs($packs, $merge=false) {
		// merge with old
		if($merge){
			$this->packs = array_merge($this->packs, $packs);
		}else{
		// fresh
			$this->packs = $packs;
		}	
	}
	
	// set single
	function set_pack($pack) {				
		// add to array
		if($pack) array_push($this->packs, $pack);		
	}
	//active on pages: options
	function get_active_options() {
		return array('register' => 1, 'upgrade' => 1, 'extend' => 1 );
	}
	
	// new pack
	function add_pack($type){
		// define empty
		$pack =array(
					'id'                  => $this->next_id,
					'trial_on'            => 0,
					'trial_cost'          => '0.00',
					'trial_duration'      => 0,
					'trial_duration_type' => 'd',
					'trial_num_cycles'    => '0',
					'cost'                => '0.00',
					'duration'            => 3,
					'duration_type'       => 'd',
					'country'             => '',
					'num_cycles'          => '', // 0 = 'Ongoing', 1-99 for recurrence
					'product'             => '',
					'membership_type'     => $type,
					'description'         => ucwords(str_replace('_', ' ', $type)),
					'hide_old_content'    => 0,
					'default'             => 0,
					'active'              => $this->get_active_options(),
					'sort'                => $this->next_id,
					'modules'             => array('mgm_free','mgm_paypal'),
					'allow_renewal'       => 1,
					'move_members_pack'	  => ''	
					);
		// set		
		$this->set_pack($pack);		
		// update next 
		$this->next_id++;		
		// save to database
		// update_option(get_class($this), $this);
		$this->save();
		// return last value
		return array((count($this->packs)) => (end($this->packs)));
	}
	
	// get pack_desc
	function get_pack_desc($pack){		
		// system
		$mgm_system = mgm_get_class('system');
		// template
		$pack_desc_template = $mgm_system->get_template('pack_desc_template', array(), true);
		// check and set
		if(empty($pack_desc_template)){
			$pack_desc_template = __('[membership_type] - [cost] [currency] per [duration] [duration_period] [num_cycles].', 'mgm');
		}
		//lifetime template:
		if($pack['duration_type'] == 'l')
			$pack_desc_template = __('[membership_type] - [cost] [currency] for [membership_type].', 'mgm');
		
		// available tpl vars
		$tpl_vars = array('membership_type', 'cost', 'currency', 'duration', 'duration_period', 'num_cycles', 'trial_cost', 
		                  'trial_duration', 'trial_duration_period', 'description');
		// set some
		$currency        = $mgm_system->setting['currency'];		
		$membership_type = mgm_stripslashes_deep(mgm_get_class('membership_types')->get_type_name($pack['membership_type']));
		$duration_period = strtolower($this->get_pack_duration($pack));
		$num_cycles      = ($pack['num_cycles'] == 0 ) ? ' - Ongoing' : (sprintf(' - for %s %s', $pack['num_cycles'], __((($pack['num_cycles'] == 1 )? 'time' : 'times'),'mgm'))); 		
		$trial_duration_period = $duration_period;
		
		// base 
		$pack_desc       = $pack_desc_template;
		// replace
		foreach($tpl_vars as $var){			
			if(isset(${$var})){
				$pack_desc = str_replace('['.$var.']', ${$var}, $pack_desc);
			}else if(isset($pack[$var])){
				$pack_desc = str_replace('['.$var.']', $pack[$var], $pack_desc);
			}	
		}
		// num cycles
		if ($pack['num_cycles']) {
			$pack_desc = preg_replace("'\[/?\s?if_num_cycles\s?\]'i", '', $pack_desc);
		} else {
			$pack_desc = preg_replace("'\[if_num_cycles\s?\](.*)\[/if_num_cycles\s?\]'i", '', $pack_desc);
		}		
		// trial on
		if ($pack['trial_on']) {
			$pack_desc = preg_replace("'\[/?\s?if_trial_on\s?\]'i", '', $pack_desc);
		} else {
			$pack_desc = preg_replace("'\[if_trial_on\s?\](.*)\[/if_trial_on\s?\]'i", '', $pack_desc);
		}
		// send 
		return $pack_desc;
	}
	
	// get pack_duration
	function get_pack_duration($pack, $trial=false){
		// trial
		if( $trial )
			$duration = ($pack['trial_duration'] > 1) ? ($this->duration_str[strtolower($pack['trial_duration_type'])] . 's') : $this->duration_str[strtolower($pack['trial_duration_type'])];
		else
			$duration =  ($pack['duration'] > 1) ? ($this->duration_str[strtolower($pack['duration_type'])] . 's') : $this->duration_str[strtolower($pack['duration_type'])];

		// return lower
		return strtolower($duration);			
	}
	
	// pack by id
	function get_pack($pack_id, $page = null){
		// default
		$_pack = false;
		// loop
		foreach ($this->packs as $pack) {
			// check	
			if(!is_null($page) && $pack['active'][$page] === 0 ) continue;
					
			// match		
			if ($pack['id'] == $pack_id ) {
				$_pack = $pack; break;
			}
		}
		// return
		return $_pack;
	}
	
	// get packs
	function get_packs($page='all',$sort=true){
		// init
		$_packs = array();
		// orders
		$pack_orders = array();
		// loop and order		
		foreach ($this->packs as $pack) {
			// check pack is active
			// old code:
			// if($active)
			//	if($pack['active'] != 1 ) continue;			
			//issue #: 474
			if($page != 'all')
				if(!$pack['active'][$page]) continue;
			// set
			$pack_orders[] = $pack['sort'];
		}		
		// if one active ?
		if(count($pack_orders)>0){
			// sort
			sort($pack_orders);			
			// sorted
			$sorted = array();
			// loop by order
			foreach($pack_orders as $order){
				// loop packs
				foreach ($this->packs as $pack) {
					// order match
					if($pack['sort'] == $order){
						// duplicate check
						if(!in_array($pack['id'], $sorted)){// #184 duplicate bug
							// sets
							$_packs[] = $pack;							
							// mark as sorted
							$sorted[] = $pack['id'];
						}
					}
				}
			}
		}		
		// return 
		return $_packs;
	}
	
	// validate
	function validate_pack($cost, $duration, $duration_type, $membership_type, $pack_id=NULL){
		// init
		$_pack = false;
		// loop
		foreach ($this->packs as $pack) {			
			// with pack id
			if($pack_id){			
				if ($pack['id'] == $pack_id && $pack['cost'] == $cost && $pack['duration'] == $duration && $pack['duration_type'] == $duration_type && $pack['membership_type'] == $membership_type) {
					$_pack = $pack;
					break;
				}	
			}else{
			// without pack id
				if ($pack['cost'] == $cost && $pack['duration'] == $duration && $pack['duration_type'] == $duration_type && $pack['membership_type'] == $membership_type) {
					$_pack = $pack;
					break;
				}	
			}
		}
		// return
		return $_pack;
	}
	
	// base packs
	function base_packs() {
		// options
		return 
			array(
				array(
					'id'                  => 1,
					'trial_on'            => 0,
					'trial_cost'          => '0.00',
					'trial_duration'      => 0,
					'trial_duration_type' => 'd',
					'cost'                => '0.00',
					'duration'            => 3,
					'duration_type'       => 'd',
					'country'             => '',
					'num_cycles'          => '',
					'product'             => '',
					'membership_type'     => 'trial',
					'description'         => 'Trial Account',
					'hide_old_content'    => 0,
					'default'             => 0,
					'active'              => $this->get_active_options(),
					'sort'                => 1,
					'modules'             => array('mgm_trial'),
					'allow_renewal'       => 0,
					'move_members_pack'	  => ''
				),
				array(
					'id'                  => 2,
					'trial_on'            => 0,
					'trial_cost'          => '0.00',
					'trial_duration'      => 0,
					'trial_duration_type' => 'd',
					'cost'                => '0.00',
					'duration'            => 1,
					'duration_type'       => 'y',
					'country'             => '',
					'num_cycles'          => '',
					'product'             => '',
					'membership_type'     => 'free',
					'description'         => 'Free Account',
					'hide_old_content'    => 0,
					'default'             => 0,
					'active'              => $this->get_active_options(),
					'sort'                => 2,
					'modules'             => array('mgm_free'),
					'allow_renewal'       => 0,
					'move_members_pack'	  => ''	
				),
				array(
					'id'                  => 3,
					'trial_on'            => 0,
					'trial_cost'          => '0.00',
					'trial_duration'      => 0,
					'trial_duration_type' => 'd',
					'cost'                => '5.00',
					'duration'            => 3,
					'duration_type'       => 'm',
					'country'             => '',
					'num_cycles'          => '',	
					'product'             => '',
					'membership_type'     => 'member',
					'description'         => 'Paid Member Account',
					'hide_old_content'    => 0,
					'default'             => 1,
					'active'              => $this->get_active_options(),
					'sort'                => 3,
					'modules'             => array('mgm_free','mgm_paypal'),
					'allow_renewal'       => 1,
					'move_members_pack'	  => ''	
				)
			);	
	}
	
	// fix
	function apply_fix($old_obj){
		// to be copied vars
		$vars = array('packs','next_id');
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
		$vars = array('packs','next_id');
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
		//This is to make sure that the default membership_type array doesn;t contain the hardcoded option Eg:'member' incase user deletes it and option array doesn't have it.
		//copy from option
		//issue#: 521
		if($option_name == 'packs') {			
			$current_value = array();
		}		
		//update class var
		$this->{$option_name} = mgm_array_merge_recursive_unique($current_value,$option_value);
	}
}
// core/libs/classes/mgm_subscription_packs.php