<?php
/**
 * Magic Members uri utility class
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_uri{
	var $segments = array();
	var $uri      = ''; 
	var $format   = 'html';// default
	
	// construct
	function __construct($uri_string=''){	
		// php4
		$this->mgm_uri($uri_string);
	}
	
	// php4	construct
	function mgm_uri($uri_string=''){
		// request
		if(!$uri_string) $uri_string = $_SERVER['REQUEST_URI'];
		// set
		$this->uri_string = $uri_string;		
		// parse
		$this->parse_uri();
	}
	
	// strip_suffix
	function strip_suffix(){
		// basename
		$basename = basename($this->uri_string);
		// format from uri/ext
		$this->suffix = (strpos($basename,'.') !== FALSE) ? substr($basename,strrpos($basename, '.')+1) : '.html';
		// replace uri_string
		return str_replace('.'.$this->suffix, '', $this->uri_string);
	}
	
	// parse uri
	function parse_uri(){	
		// parse format
		$uri_string = $this->strip_suffix();		
		// split
		$parts = explode('/', $uri_string);			
		// loop and verify
		do{
			// clean - add sanitize
			$part = trim(array_shift($parts));
			// take
			if(!empty($part) && $part != '/'){
				$this->segments[] = basename($part);
			}	
		}while(count($parts)>0);		
	}	
	
	// segment
	function segment($index,$default=''){
		// get 
		if(isset($this->segments[$index])){
			return $this->segments[$index];
		}
		// return default
		return $default;		
	}
	
	// segments
	function segments($start_index=2){
		return array_slice($this->segments, $start_index);
	}	
	
	// return 
	function uri_string(){
		// return 
		return $this->uri_string;
	}
}
// core/libs/utilities/mgm_uri.php