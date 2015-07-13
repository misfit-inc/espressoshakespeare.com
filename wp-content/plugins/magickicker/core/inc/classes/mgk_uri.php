<?php
// uri class
class mgk_uri{
	var $segments  =array();
	var $uri       =""; 
	// constructor
	function mgk_uri($uri){
		$this->uri=$uri;
		$this->parse_uri();
	}
	// parse uri
	function parse_uri(){		
		// split
		$parts  =explode('/',$this->uri);		
		// loop and verify
		do{
			// clean - add xss clean
			$part=trim(array_shift($parts));
			// take
			if(!empty($part) && $part!='/'){
				$this->segments[]=$part;
			}	
		}while(count($parts)>0);		
	}
	// segment
	function segment($index,$default=''){
		// get 
		if(isset($this->segments[$index])){
			return $this->segments[$index];
		}else{
			$default;
		}
	}
}
// end of file