<?php
/**
 * Magic Members ajax utility class
 *
 * @package MagicMembers
 * @since 2.5
 */
class mgm_ajax{
	// ajax_request
	var $ajax_request = false; 
	
	// construct
	function __construct(){
		// php4 
		$this->mgm_ajax();
	}
	
	// php4 construct
	function mgm_ajax(){
		// check if ajax
		$this->ajax_request = (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]  == 'XMLHttpRequest') ? TRUE : FALSE;
	}	
	
	// check if ajax
	function is_ajax_request(){
		// return 
		return $this->ajax_request;
	}	
	
	// header
	function send_header(){
		// when not sent
		if(!headers_sent()){
			if(preg_match('/application\/json/i',$_SERVER['HTTP_ACCEPT'])){
				header("Content-type:application/json");	
			}else if(preg_match('/text\/javascript/i',$_SERVER['HTTP_ACCEPT'])){
				header("Content-type:text/javascript");	
			}else if(preg_match('/text\/plain/i',$_SERVER['HTTP_ACCEPT'])){
				header("Content-type:text/plain");	
			}else{
				header("Content-type:text/html");	
			}	
		}
	}
	
	// start output
	function start_output($header=true){
		// check if ajax
		if($this->is_ajax_request()){
			// start
			ob_end_clean();
			//to restart output buffering: issue#577	
			ob_start();		
			// header
			$this->send_header();	
			// return		
			return true;
		}
	}
	
	// end output
	function end_output(){
		// is ajax
		if($this->is_ajax_request()){
			// flush
			ob_end_flush();
			// exut
			exit();
		}
	}	
}
// core/libs/utilities/mgm_ajax.php