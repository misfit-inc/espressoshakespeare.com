<?php
/**
 * Magic Members ResetApi Server utility class
 *
 * @package MagicMembers
 * @since 2.5.2
 */ 
class mgm_restapi_server{
	// static
	static function init(){
		// trim prefix
		$uri_string = str_replace(MGM_API_URI_PREFIX, '', $_SERVER['REQUEST_URI']);
		// parse
		$uri = new mgm_uri($uri_string);
		// get request resource
		$class  = $uri->segment(0);
		$action = $uri->segment(1);
		$params = $uri->segments(2);// after 2nd	
		// class name
		$class_name = MGM_API_CLASS_PREFIX . $class;		
		// response
		$response = NULL;
		// check class
		if(file_exists(MGM_API_DIR . $class_name . '.php')){	
			// include
			@include_once(MGM_API_DIR . $class_name . '.php');		
			// load class
			if(class_exists($class_name)){					
				// init 
				$cls_obj = new $class_name();
				// uri
				$cls_obj->set_uri_string($uri_string);
				// route
				$cls_obj->route_action($action,$params); exit;				
			}else{
				// handle class error
				$response = array(array('status' => false, 'error' => __(sprintf('Invalid Request: no such api resource - %s', $class),'mgm')), 404);
			}
		}else{
			// handle class error
			$response = array(array('status' => false, 'error' => __(sprintf('Invalid Request: no such api resource - %s', $class),'mgm')), 404);
		}
		
		// response
		if($response){
			// copy
			$api = new mgm_api_controller();
			// response
			$api->response(array_shift($response), array_shift($response));
		}
	}
}
// core/libs/utilities/mgm_restapi_server.php