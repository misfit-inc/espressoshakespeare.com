<?php
/**
 * Magic Members admin plugins module
 *
 * @package MagicMembers
 * @since 2.0
 */
 class mgm_plugins extends mgm_controller{
 	
	// construct
	function mgm_plugins()
	{		
		// load parent
		parent::__construct();
	}
	
	// index
	function index(){
		// data
		$data = array();		
		// load template view
		$this->load->template('plugins/index', array('data'=>$data));		
	}		
	
	// lists
	function plugins_list(){
		// data
		$data = array();		
		// get plugins
		$plugins = mgm_get_available_plugins();	
		// init 
		$data['plugins'] = array();	
		// plugins
		foreach($plugins as $plugin){						
			// get plugin
			$pi = mgm_get_plugin('mgm_plugin_'.$plugin);			
			// data	
			$pi_data = $pi->settings_box();					
			// get html
			$data['plugins'][$plugin] = $pi_data;
		}				
		// load template view
		$this->load->template('plugins/list', array('data'=>$data));	
	}
 }
// return name of class 
return basename(__FILE__,'.php');
// end file /core/admin/mgm_plugins.php