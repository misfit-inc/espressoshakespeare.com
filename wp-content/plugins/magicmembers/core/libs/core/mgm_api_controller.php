<?php
/**
 * Magic Members api modules parent class
 * base class for admin modules
 *
 * @package MagicMembers
 * @since 2.5
 */
class mgm_api_controller extends mgm_object{
	// loader
	// public $load;
	// public $process;
	// request
	protected $request;
	protected $response;	
	protected $_methods = array('get', 'delete', 'post', 'put');
	
	// List all supported methods, the first will be the default format
	protected $_supported_formats = array(
		'xml'     => 'application/xml',
		'json'    => 'application/json',
		//'jsonp' => 'application/javascript',
		'phps'    => 'application/vnd.php.serialized',
		'php'     => 'text/plain'
	);
	
	// construct
	public function __construct(){		
		// php4 construct
		$this->mgm_api_controller();
	}
	
	// php4 construct
	public function mgm_api_controller(){
		// parent
		parent::__construct();					
		// processor
		// $this->process = & new mgm_processor();
		// rest
		$this->_rest_request();
	}	
	
	/*// init
	public function init(){	
		// set instance
		$this->process->set_instance($this);	
		// call
		$this->process->call();
	}	*/		
	
	// response
	public function response($data = array(), $http_code = null)
	{
		// If data is empty and not code provide, error and bail
		if (empty($data) && $http_code === null)
    	{
    		$http_code = 404;
    	}
		// Otherwise (if no data but 200 provided) or some data, carry on camping!
		else
		{
			// cool syntax
			is_numeric($http_code) || $http_code = 200;

			// If the format method exists, call and return the output in that format
			if (method_exists($this, '_format_'.$this->response->format))
			{
				// Set the correct format header
				header('Content-Type: '.$this->_supported_formats[$this->response->format]);
				
				// method
				$method = '_format_'.$this->response->format;
				// output
				$output = $this->$method($data);
			}

			// If the format method exists, call and return the output in that format
			elseif (method_exists('mgm_format', 'to_'.$this->response->format))
			{
				// Set the correct format header
				header('Content-Type: '.$this->_supported_formats[$this->response->format]);
				
				// method
				$method = 'to_'.$this->response->format;				
				
				// output
				$output = mgm_format::$method($data);				
			}
			// Format not supported, output directly
			else
			{
				$output = $data;
			}
		}
		
		// header	
		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);
		header('Content-Length: ' . strlen($output));
		
		// log
		// mgm_log($http_code. ' '. $output);		
		exit($output);
	}
	
	// re route to verb
	public function route_action($action, $params){
		// set action/params
		$this->request->action = $action; 
		$this->request->params = $params;
		// access control
		if(!$this->_is_authorized($message)){
			// response
			$response = array(array('status' => false, 'error' => $message), 403);
		}else{			
			// action 
			$action_verb = '' ;
			// by name
			if(method_exists($this, ($action . '_' . $this->request->method))){
				$action_verb = $action . '_' . $this->request->method ;
			}elseif($this->request->method == 'get' && method_exists($this,$action)){
				$action_verb = $action;
			}
			// load action
			if( $action_verb ){
				// call
				$response = call_user_func_array(array(&$this, $action_verb), $params);						
			}else{
				// handle action error					
				$response = array(array('status' => false, 'error' => __(sprintf('Invalid Request: no such api action - %s', $action),'mgm')), 404);
			}
		}
		// response
		$this->response(array_shift($response), array_shift($response));
	}
	
	// set_uri_string
	function set_uri_string($uri_string){
		// request uri 
		$this->request->uri_string = $uri_string;	
	}
	
	// -- PRIVATE -------------------------------------------------
	// build request
	private function _rest_request(){
		// request 
		$this->request = new stdClass;
		// request method
		$this->request->method = $this->_get_request_method();		
		// request format
		$this->request->format = $this->_get_request_format();				
		// store data
		$this->request->data = $this->_get_request_data();		
		// response 
		$this->response = new stdClass;
		// response format
		$this->response->format = $this->_get_response_format();			
		// log
		// mgm_log($this, 'restapi_' . time() . '_' . $this->request->method);									
	}
		
	// get request method/verb
	function _get_request_method(){
		// request
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		// check
		if (in_array($method, $this->_methods)){
			// return 
			return $method;
		}
		// default
		return 'get';		
	}
	
	// get request format
	function _get_request_format(){
		// if set
		if (isset($_SERVER['CONTENT_TYPE'])){
			// Check all formats against the HTTP_ACCEPT header
			foreach ($this->_supported_formats as $format => $mime){
				// match
				if (strpos($match = $_SERVER['CONTENT_TYPE'], ';')){
					$match = current(explode(';', $match));
				}
				// match
				if ($match == $mime){
					// return 
					return $format;
				}
			}
		}
		// null
		return NULL;
	}
	
	// get request data
	function _get_request_data(){
		// BODY from input stream
		$this->request->data['body'] = NULL;		
		// on verb
		switch ($this->request->method)
		{
			case 'get':
				// GET variables
				parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $this->request->data['get']);
			break;
			case 'post':
				// POST variables
				$this->request->data['post'] = $_POST;
				// format
				if($this->request->format) $this->request->data['body'] = file_get_contents('php://input');
			break;
			case 'put':
				// HTTP body
				if($this->request->format && $this->request->format != 'php') 
				{
					$this->request->data['body'] = file_get_contents('php://input');
				}else
				{
				// No file type, parse args
					parse_str(file_get_contents('php://input'), $this->request->data['put']);
				}				
			break;
			case 'delete':
				// DELETE variables
				parse_str(file_get_contents('php://input'), $this->request->data['delete']);
			break;
		}

		// Now we know all about our request, let's try and parse the body if it exists
		if ($this->request->format && $this->request->data['body'])
		{
			$this->request->data['body'] = $this->_get_request_body();
		}
		
		// merge
		$this->request->data['global'] = array_merge((array)$this->request->data['get'],(array)$this->request->data['post'],
													 (array)$this->request->data['put'],(array)$this->request->data['delete'],
													 (array)$this->request->data['body']);
		// return self
		return $this->request->data;											 
	}
	
	// get response format
	function _get_response_format($default='xml'){
		// get patterb
		$pattern = '/\.(' . implode('|', array_keys($this->_supported_formats)) . ')$/';
		
		// file extension is used
		if (preg_match($pattern, $_SERVER['REQUEST_URI'], $matches))
		{
			return $matches[1];
		}elseif ($this->request->data['get'] && ! is_array(end($this->request->data['get'])) && preg_match($pattern, end($this->request->data['get']), $matches))
		{
			// Check if a file extension is used in get param
			// The key of the last argument
			$last_key = end(array_keys($this->request->data['get']));

			// Remove the extension from arguments too
			$this->request->data['get'][$last_key] = preg_replace($pattern, '', $this->request->data['get'][$last_key]);
			// return
			return $matches[1];
		}

		// format arg in get
		if (isset($this->request->data['get']['format']) && array_key_exists($this->request->data['get']['format'], $this->_supported_formats))
		{
			return $this->request->data['get']['format'];
		}
		
		// return default
		return $default;
	}
	
	// parse only known formats
	function _get_request_body(){
		//  format
		switch($this->request->format){			
			case 'json':// json, .json
				return mgm_format::to_array(mgm_format::from_json($this->request->data['body']));
			break;			
			case 'phps': // php serialize, .phps
				return mgm_format::to_array(mgm_format::from_phps($this->request->data['body']));
			break;
			case 'php': // php array, .php				
				return mgm_format::to_array(mgm_format::from_php($this->request->data['body']));
			break;
			case 'xml':	// xml, .xml
			default:
				return mgm_format::to_array(mgm_format::from_xml($this->request->data['body']));
			break;
		}
	}
	
	// check authorized
	function _is_authorized(&$message){
        global $wpdb;
		// init defaults
		$api_key = '';
		// set message
		$message = __('Invalid API Key.','mgm');
		// authorized
		$authorized = false;
		// rest
		$this->rest = new stdClass;
		// get key name
		$key_name = strtoupper(str_replace('-', '_', MGM_API_KEY_VAR));	
		// Work out the name of the SERVER entry based on config
		if(isset($_SERVER['HTTP_' . $key_name])){ 
		// HTTP_X_MGMAPI_KEY 
			$api_key = $_SERVER['HTTP_' . $key_name];		
		}elseif(isset($this->request->data['global'][MGM_API_KEY_VAR]))	{ 
		// post/get val X_MGMAPI_KEY
			$api_key = $this->request->data['global'][MGM_API_KEY_VAR];
		}else{
			// set message
			$message = __('Invalid Request, No API Key provided!','mgm');
		}			
		
		// check key 
		if ($api_key){
			// check db
			$row = $wpdb->get_row(sprintf("SELECT * FROM `%s` WHERE `api_key` = '%s'", TBL_MGM_REST_API_KEY, $api_key)); 							
			// set
			if(isset($row->id) && (int)$row->id > 0){
				// set
				$this->rest->access = $row;
				// return
				$authorized = true;
			}
		}		
		// log, @todo, if only active
		$this->_log_request($api_key, $authorized);
		// No key has been sent
		return $authorized;
	}
	
	// log
	function _log_request($api_key, $authorized){
		global $wpdb;
		// sql data
		$sql_data = array();
		// set
		$sql_data['api_key']       = $api_key;
		$sql_data['uri']           = $this->request->uri_string;
		$sql_data['method']        = $this->request->method;
		$sql_data['params']        = json_encode($this->request->data['global']);
		$sql_data['ip_address']    = $_SERVER['REMOTE_ADDR'];
		$sql_data['is_authorized'] = ($authorized === TRUE) ? 'Y' : 'N';
		$sql_data['create_dt']     = date('Y-m-d H:i:s');
		// insert
		$wpdb->insert(TBL_MGM_REST_API_LOG, $sql_data);	
	}
}
// end of file core/libs/core/mgm_api_controller.php
