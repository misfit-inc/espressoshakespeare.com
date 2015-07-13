<?php
/**
 * Magic Members autoresponder modules parent class
 *
 * @package MagicMembers
 * @since 2.5.0
 */
class mgm_autoresponder extends mgm_component{	
	// type
	var $type           = 'autoresponder';	
	// name
	var $name           = 'Magic Members Autoresponder Module';
	// internal name
	var $code           = 'mgm_autoresponder';
	// dir
	var $module         = 'autoresponder';
	// description
	var $description    = '';
	// enabled/disabled : Y/N
	var $enabled        = 'N';	
	// end_points
	var $end_points     = array();	
	// settings
	var $setting        = array();
	// postfields
	var $postfields     = array();
	
	// construct
	function __construct(){
		// php4 construct
		$this->mgm_autoresponder();
	}
	
	// php4 construct
	function mgm_autoresponder(){
		// call parent
		parent::__construct();		
		// set code
		$this->code = __CLASS__; 
		// desc
		$this->description = __('<p>Autoresponder module description</p>','mgm');		
	}
	
	// set template
	function set_tmpl_path($basedir=MGM_MODULE_DIR, $prefix='mgm_'){
		// dir/module		
		$this->module = str_replace($prefix, '', $this->code) ;
		// set path		
		$tmpl_path = ($basedir . implode(DIRECTORY_SEPARATOR, array($this->type, $this->module, 'html')) . DIRECTORY_SEPARATOR);		
		// set		
		$this->load->set_tmpl_path($tmpl_path);
	}
	
	// enable api hook
	function enable($activate=false){
		// activate
		if($activate) mgm_get_class('system')->activate_module($this->module,$this->type);					
		// update state
		$this->enabled = 'Y'; 				
		// update optiona
		$this->save();
	}
	
	// disable api hook
	function disable($deactivate=false){
		// deactivate
		if($deactivate) mgm_get_class('system')->deactivate_module($this->module,$this->type);
		// update state
		$this->enabled = 'N'; 		
		// update options
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
		return ($this->enabled == 'Y' && mgm_get_class('system')->is_active_module($this->module,$this->type)) ? true : false;
	}
	
	// invoke api hook
	function invoke($method, $args=false){
		// check
		if(method_exists($this,$method)){
			return $this->$method($args);
		}else{
			die(__(sprintf('No such method : %s',$method),'mgm'));
		}
	} 	
	
	// settings api hook
	function settings(){
		// overwrite this
		// return '';
	}
	
	// settings_box api hook
	function settings_box(){
		// overwrite this
		// return '';
	}	
		
	// settings api update
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
	
	// send api hook
	function send($user_id){		
		// set params, to be overridden by child class
		$this->_set_postfields($user_id);
		// transport
		$this->_transport();
	}
	
	// internal private methods //////////////////////////////////////////
	
	// transport
	function _transport(){
		// urls
		$url    = $this->_get_endpoint('live');
		$fields = $this->_get_postfields();
		
		// curl handle
		$ch = curl_init($url);		
		
		// set headers
		$headers   = array();
		$headers[] = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11";
		$headers[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$headers[] = "Accept-Language: en-us,en;q=0.5";
		$headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$headers[] = "Keep-Alive: 300";
		$headers[] = "Connection: keep-alive";
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		$headers[] = "Content-Length: " . strlen($fields);
		// apply filter
		$headers = apply_filters('mgm_autoresponder_headers', $headers, $this->code);
		
		// set options
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);		
		curl_setopt($ch, CURLOPT_POST, true);				
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);		
		curl_setopt($ch, CURLOPT_HEADER, $headers);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);// new			
		curl_setopt($ch, CURLOPT_REFERER, get_option('siteurl'));	
		// send headers
		// if($this->send_headers){
			// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// }
			
		// get result			
		$result = curl_exec($ch);	
		// close			
		curl_close($ch);			
		
		// return action
		do_action('mgm_autoresponder_result', $result, $this->code);	
		
		// return 
		return true;
	}
	
	// set postfields
	function _set_postfields($user_id){
		// get userdata
		$user = mgm_get_userdata($user_id);	
		// user 		
		$user_email   = stripslashes($user->user_email);		
		$display_name = stripslashes(($user->first_name) ? mgm_str_concat($user->first_name,$user->last_name) : $user->display_name);	
		// set
		$this->postfields = array('email'=>$user_email,'name'=>$display_name);
	}
	
	// get post fields
	function _get_postfields(){
		// int
		$_postfields = array();
		// check
		if ( count($this->postfields) )
			// loop
			foreach ( $this->postfields as $i => $f )
				// set
				$_postfields[] = urlencode($i) . '=' . urlencode($f);
		// return		
		return implode('&', $_postfields);
	}
	
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
	
	// for serialize
	function __toString(){
		// return
		return serialize($this);
	}
	
	// fix
	function apply_fix($old_obj){
		// to be copied vars
		$vars = array('description','enabled','end_points','setting');
		// set
		foreach($vars as $var){
			// var
			$this->{$var} = (isset( $old_obj->{$var} ) ) ? $old_obj->{$var} : '';
		}			
		// save
		$this->save();	
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
// end of file core/libs/components/mgm_autoresponder.php