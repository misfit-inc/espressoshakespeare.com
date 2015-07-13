<?php
// ajax class
class mgk_ajax{
	var $is_ajax=false; 
	// constructor
	function mgk_ajax(){
		// check if ajax
		$this->is_ajax=(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]  == 'XMLHttpRequest')?TRUE:FALSE;
	}	
	// check if ajax
	function is_ajax(){
		return $this->is_ajax;
	}	
	// header
	function send_header(){
		if(eregi('application/json',$_SERVER['HTTP_ACCEPT'])){
			header("Content-type:application/json");	
		}else if(eregi('text/javascript',$_SERVER['HTTP_ACCEPT'])){
			header("Content-type:text/javascript");	
		}else if(eregi('text/plain',$_SERVER['HTTP_ACCEPT'])){
			header("Content-type:text/plain");	
		}else{
			header("Content-type:text/html");	
		}	
	}
	// start output
	function Start_Output($header=true){
	  if($this->is_ajax){
		ob_end_clean();
		if(!headers_sent()){
			$this->send_header();	
		}
		return true;
	  }
	}
	// end output
	function end_output(){
	  if($this->is_ajax){
		ob_end_flush();
		exit();
	  }
	}
	// build output
	function build_output($options=false,$load=''){	
		// cgi bug
		// load for twice for cgi servers
		if(mgk_is_cgi_server()){						
			mgk_set_include_path();
		}		
		// ajax call
		if($this->is_ajax){
			$load=(!empty($load))? $load : $_GET['load'];
			if(isset($load) && !empty($load)){
				$options=$this->filter_options($options);
				// page
				$page = $options['prefix'] . $load . $options['extension'];
				$this->Start_Output();			
				if(file_exists(MGK_CORE_DIR . $options['container'] . DIRECTORY_SEPARATOR . $page)){					
					require_once(MGK_CORE_DIR . $options['container'] . DIRECTORY_SEPARATOR . $page);
				}else{
					echo 'File ['.$options['container'] . DIRECTORY_SEPARATOR. $page.'] could not be loaded.';
				}
				$this->end_output();
			}
		}
	}
	// options
	function filter_options($options){
		$d_options=array('container'=>implode(DIRECTORY_SEPARATOR, array('admin','html')),'extension'=>'.tpl.php','prefix'=>'mgk_');
		if(is_array($options)){
			$d_options=array_merge($d_options,$options);
		}
		// send
		return $d_options;
	}
}
// end of file