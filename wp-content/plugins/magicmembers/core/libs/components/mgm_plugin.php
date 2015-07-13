<?php
/**
 * Magic Members plugins parent class
 *
 * @package MagicMembers
 * @since 2.5
 */
class mgm_plugin extends mgm_component{
	// type
	var $type           = 'plugin';	
	// name
	var $name           = 'Magic Members Plugin';
	// internal name
	var $code           = 'mgm_plugin';
	// dir
	var $plugin         = 'plugin';
	// description
	var $description    = '';
	// enabled/disabled : Y/N
	var $enabled        = 'N';	
	// end_points
	var $end_points     = array();	
	// settings
	var $setting        = array();
	// version
	var $version        = 0.1;
	// author
	var $author         = 'Magic Media Group';
	// last_updated
	var $last_updated   = '2011-07-10';
	// author_url
	var $author_url     = 'http://www.magicmembers.com/';
	
	// namepsace prefix
	var $prefix         = 'mgm_plugin_';
	
	// construct
	function __construct(){
		// php4 construct
		$this->mgm_plugin();
	}
	
	// php4 construct
	function mgm_plugin(){		
		// call parent
		parent::__construct();		
		// set code
		$this->code = __CLASS__; 		
		// desc
		$this->description = __('plugin description', 'mgm');		
		// default settings
		$this->_default_setting();
	}
	
	// set template
	function set_tmpl_path($basedir=MGM_PLUGIN_DIR, $prefix='mgm_plugin_'){
		// dir
		$this->plugin = str_replace($prefix, '', $this->code) ;
		// set path
		$tmpl_path = ($basedir . implode(DIRECTORY_SEPARATOR, array($this->plugin, 'html')) . DIRECTORY_SEPARATOR);	
		// set		
		$this->load->set_tmpl_path($tmpl_path);
	}
	
	// enable only
	function enable($activate=false){
		// activate
		if($activate) mgm_get_class('system')->activate_plugin($this->plugin);
		// update state
		$this->enabled = 'Y'; 				
		// update option
		$this->save();
	}
	
	// disable only
	function disable($deactivate=false){
		// deactivate
		if($deactivate) mgm_get_class('system')->deactivate_plugin($this->plugin);
		// update state
		$this->enabled = 'N'; 		
		// update option
		$this->save();
	}
	
	// install hook
	function install(){			
		// enable
		$this->enable(true);
	}
	
	// uninstall hook
	function uninstall(){				
		// disable
		$this->disable(true);			
	}
	
	// enabled check
	function is_enabled(){
		// return true|false on enabled
		return ($this->enabled == 'Y') ? true : false;
	}
	
	// invoke hook
	function invoke($method, $args=false){
		// check
		if(method_exists($this,$method)){
			return $this->$method($args);
		}else{
			die(__(sprintf('No such method : %s',$method),'mgm'));
		}
	} 
	
	// settings_box hook
	function settings_box(){
		// echo 'create ' . $this->name . ' settings box';
	}	
		
	// settings update
	function settings_update(){
		// form type 
		switch($_POST['setting_form']){
			case 'box':
			// from box	
			break;
			case 'main':
			// form main
			break;
		}	
	}
	
	// get infos
	function get_info(){
		// info
		$info = array();
		// check version
		if($this->version){
			$info[] = __(sprintf('Version: %s',$this->version),'mgm');
		}
		// check author
		if($this->author){
			$info[] = __(sprintf('Author: %s',$this->author),'mgm');
		}
		// check last_updated
		if($this->last_updated){
			$info[] = __(sprintf('Last Updated: %s',date('m-d-Y',strtotime($this->last_updated))),'mgm');
		}
		// check author_url
		if($this->author_url){
			$info[] = __(sprintf('<a href="%s">%s</a>',$this->author_url,'Plugin Site'),'mgm');
		}
		// return
		return implode(' | ', $info);		
	}
	
	// internal private methods //////////////////////////////////////////
	
	// set endpoint
	function _set_endpoint($status, $endpoint){
		// status
		$this->end_points[$status] = $endpoint;	
	}
	
	// get endpoint
	function _get_endpoint($status=''){
		// force
		$status = (!empty($status)) ? $status : $this->status;
		// status
		switch($status){
			case 'test':
				return $this->end_points['test'];
			break;			
			case 'live':
			default:
				return $this->end_points['live'];
			break;
		}	
	}	
			
	// default setting
	function _default_setting(){
		// return
		return array();
	}	
	
	// prepare save, define the object vars to be saved
	function _prepare(){		
		// init array
		$this->options = array();
		// to be saved
		$vars = array('description','enabled','end_points','setting');
		// set
		foreach($vars as $var){
			// var
			$this->options[$var] = $this->{$var};
		}		
	}	
}
// end of file core/libs/components/mgm_plugin.php