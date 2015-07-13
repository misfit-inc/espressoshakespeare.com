<?php
/**
 * Magic Members migrate utility class 
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_migrate {
	// filename
	private $filename = '';
	
	// constructor
	function __construct($filename = false) {
		// php4
		$this->mgm_migrate($filename);
	}
	
	// php4 construct
	function mgm_migrate($filename = false){
		// version
		$this->version = mgm_get_class('auth')->get_product_info('product_version');
		// check ext
		if ($filename) {
			$this->filename = $this->check_extension($filename);
		} else {
		// create
			$this->filename = 'export-'.$this->version.'-'.time().'.xml';
		}
	}
	
	// set file 
	function set_filename($filename) {
		// set
		$this->filename = $this->check_extension($filename);
	}

	// check ext
	function check_extension($filename) {
		// check
		if (!preg_match('/\.xml$/i',$filename)) {
			$filename .= '.xml';
		}
		// return
		return $filename;
	}
	
	// create
	function create($filepath=false){
		global $wpdb;
		// create object
		$xml = simplexml_load_string(sprintf('<?xml version="1.0" encoding="utf-8"?><magicmembers version="%s" mapping="any"></magicmembers>',$this->version)); 
		
		// check
		if($xml){
			// log
			// mgm_log(print_r($xml,true));
			$sql     = 'SELECT option_name,option_value FROM `' . $wpdb->options . '` WHERE option_name LIKE "mgm_%" ORDER BY `option_id` ASC';
			$options = $wpdb->get_results($sql);	
			// add
			$settings = $xml->addChild('general_settings');							
			foreach($options as $option){
				$setting = $settings->addChild('setting');
				$setting->addAttribute('name',$option->option_name);
				/*// load 
				$opt = get_option($option->option_name);
				// check type
				if(is_object($opt)){
					foreach(get_object_vars($opt) as $name=>$value){
						if(is_array($value)){
							foreach($value as $v){
								$setting->addChild($v);								
							}
						}else{
							$setting->addChild($name, $value);
						}

					}
				}elseif(is_array($opt)){
					foreach($opt as $name=>$value){
						$setting->addChild($name, $value);
					}
				}else{
					$setting->addChild($opt);
				}	*/					
			}			
			// create file
			if($filepath){
				return $xml->asXML($filepath);	
			}else{
				// string
				return $xml->asXML();
			}
		}else{
			// log
			// mgm_log('Error creating XML');
			return false;
		}	
	}
	
	// check html content
	function has_html($content){
		// match
		if(preg_match('/<(.*)>(.*)<\/(.*)>/', $content)){
			return true;
		}
		// negative
		return false;
	}
}
// core/libs/utilities/mgm_migrate.php