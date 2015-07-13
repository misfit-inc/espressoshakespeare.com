<?php
/**
 * Magic Members core functions, can not be overridden
 *
 * @package MagicMembers
 * @since 2.5
 */

/**
 * Magic Members import dependant file
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, comma separaed file paths
 * @return none
 */ 
function mgm_import_dependency($names){
	// get name
	$files = explode(',', $names);
	// load if set
	if(is_array($files)){
		// loop
		foreach($files as $file){
			// include
			include_once($file.'.php');
		}
	}
}
/**
 * Magic Members get option : wrapper for mgm objects stored in wp options table
 * kept for backward compatibility
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, class name/code
 * @param bool, reload from file flag
 * @return object/instance
 */     
function mgm_get_option($option_name, $cached=true){	
	// append prefix
	if(!preg_match('/^mgm_/',$option_name)) $option_name = 'mgm_'.$option_name;	
	
	// return object
	if($cached){// false: load form database, true: load fresh from file
		// get object
	    if($option_class_obj = get_option($option_name) ){
			// if object
			if(is_object($option_class_obj)){
				// return
		 		return $option_class_obj;
			}
		}	
	}	
	// return default	
	if(class_exists($option_name)){
		// object
		$option_class_obj = new $option_name;	
		// update
		// update_option($option_name, $option_class_obj);
		// return
		return $option_class_obj;
	}	
	
	// return dummy
	return new stdClass;
}
/**
 * Magic Members get class : loads mgm classed after mrging options from database
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, class name/code
 * @param bool, reload from file flag
 * @return object/instance
 */   
function mgm_get_class($class_name){
	global $mgm_classes;	
	
	// append prefix
	if(!preg_match('/^mgm_/',$class_name)) $class_name = 'mgm_'.$class_name;		
	
	// check if loaded 
	if(isset($mgm_classes[$class_name])) return $mgm_classes[$class_name];	
	
	// return default	
	if(class_exists($class_name)){
		// create instance
		return $mgm_classes[$class_name] = & new $class_name;			
	}		
	// return simple instance
	return new stdClass;
}
/**
 * Magic Members get config : get config form config array
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, key name/code
 * @param string, group
 * @param mixed, default 
 * @return mixed
 */ 
function mgm_get_config($key, $default=''){
	global $mgm_config;
	// return
	return isset($mgm_config[$key])? $mgm_config[$key] : $default;
}
/**
 * Magic Members get member : get member class object
 * get member object, using usermeta table
 *
 * @package MagicMembers
 * @since 2.5
 * @param int, user id
 * @return object
 */    
function mgm_get_member($user_id, $cached=false){	
	// return object from saved user meta
	if($cached){
		// get cached
		if($mgm_member = get_user_option('mgm_member', $user_id)){
			// check object 
			if(is_object($mgm_member) && method_exists($mgm_member, 'set_field')){
				// set user id
				if(!$mgm_member->id) $mgm_member->set_field('id', $user_id);
				// return
				return $mgm_member;
			}	
		}
	}	
		
	// return default	
	if(class_exists('mgm_member')){
		// get object
		$mgm_member = & new mgm_member($user_id);		
		// unset options for recursion bug
		if($mgm_member->options) unset($mgm_member->options);
		// save
		if($cached && $user_id>0){			
			// update
			update_user_option($user_id, 'mgm_member', $mgm_member, true);		
		}
		// return
		return $mgm_member;
	}	
	
	// return dummy
	return new stdClass;
}

/**
 * Magic Members get post object : get post class object
 * get post object, using postmeta table
 *
 * @package MagicMembers
 * @since 2.5
 * @param int, post id
 * @return object
 */ 
function mgm_get_post($post_id, $cached=false){	
	// return object from saved post meta, make hidden meta
	if($cached){
		// check
		if($mgm_post_meta = get_post_meta($post_id, '_mgm_post', true)){// single
			// get first element if array
			if(is_array($mgm_post_meta)){
				// getch
				$mgm_post_meta = array_shift($mgm_post_meta);
			}
			// bugfix for iss#174, object serialization happens earlier
			$mgm_post = maybe_unserialize($mgm_post_meta); 
			// check if object
			if(is_object($mgm_post) && method_exists($mgm_post, 'set_field')){
				// set user id
				if(!$mgm_post->id) $mgm_post->set_field('id', $post_id);
				// return
				return $mgm_post;
			}
		}	
	}	
		
	// return default	
	if(class_exists('mgm_post')){
		// get object
		$mgm_post = & new mgm_post($post_id);	
		// unset options for recursion bug
		if($mgm_post->options) unset($mgm_post->options);	
		// save when post exists
		if($cached && $post_id>0){					
			// update
			update_post_meta($post_id, '_mgm_post', $mgm_post);		
		}
		// return object
		return $mgm_post;
	}	
	
	// return dummy
	return new stdClass;
}
/**
 * Magic Members get available modules
 * read from dir and list all available modules
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, type of module
 * @return object
 */ 
function mgm_get_available_modules($type='payment'){
	// init	
	$modules[$type] = $_module_dirs = array();		
	// core module dirs 
	if($module_dirs = glob(MGM_MODULE_DIR . $type . '/*', GLOB_ONLYDIR )){
		$_module_dirs = $module_dirs;	
	}
	// extend module dirs	
	if($module_dirs = glob(MGM_EXTEND_MODULE_DIR . $type . '/*', GLOB_ONLYDIR )){
		$_module_dirs = array_merge($_module_dirs, $module_dirs);		
	}
	// check
	if(count($_module_dirs)>0){
		// loop
		foreach($_module_dirs as $_module_dir){
			// name
			$name = pathinfo($_module_dir,PATHINFO_BASENAME);
			// set internal name
			$filename = 'mgm_'.$name.'.php';
			// check path
			if(file_exists($_module_dir . DIRECTORY_SEPARATOR . $filename)){
				$modules[$type][] = pathinfo($_module_dir,PATHINFO_BASENAME);
			}
		}
	}	
	// return 
	return $modules[$type];
}
/**
 * Magic Members get module
 * wrapper for only module object, loaded on demand
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, module name
 * @param string, module type
 * @return object
 */ 
function mgm_get_module($module_name, $type='payment', $cached=false){
	global $mgm_modules;
	
	// append prefix
	if(!preg_match('/^mgm_/',$module_name)) $module_name = 'mgm_'.$module_name;	
	
	// check if loaded 
	if(isset($mgm_modules[$module_name])) return $mgm_modules[$module_name];
	
	// module folder name
	$module_folder = str_replace('mgm_', '', $module_name);	
	// module class name
	$module_class  = $module_name; // to allow override
	
	// load file if no class
	if(!class_exists($module_class)){		
		// module_file
		$module_file = MGM_MODULE_DIR . implode(DIRECTORY_SEPARATOR, array($type, $module_folder, $module_name)) . '.php';
		// check
		if(file_exists($module_file)){			
			// include
			@include_once($module_file);	
		}else{
		// check in extend
			// module_file
			$module_file = MGM_EXTEND_MODULE_DIR . implode(DIRECTORY_SEPARATOR, array($type, $module_folder, $module_name)) . '.php';
			// include
			if(file_exists($module_file)){			
				// include
				@include_once($module_file);	
			}else{
				die(__(sprintf('No such module: %s/%s ',$type,$module_name),'mgm'));
			}
		}	
	}
	
	// return object form database 	
	if($cached){
		if($module_object = get_option($module_name)) return $mgm_modules[$module_name] = $module_object;		
	}
	
	// return default	
	if(class_exists($module_class)){
		// get instance
		$module_object = & new $module_class;	
		// update option
		// update_option($module_name, $module_object);
		// return
		// return $module_object;
		return $mgm_modules[$module_name] = $module_object;
	}
	
	// return dummy
	return new stdClass;		
}
/**
 * Magic Members get available plugins
 * read from dir and list all available modules
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, fresh load
 * @return object
 */
function mgm_get_available_plugins($flush=false){
	// init
	$plugins = $_plugin_dirs = array();	
	// core plugin dirs 
	if($plugin_dirs = glob(MGM_PLUGIN_DIR . '/*', GLOB_ONLYDIR )){
		$_plugin_dirs = $plugin_dirs;	
	}
	// extend plugin dirs	
	if($plugin_dirs = glob(MGM_EXTEND_PLUGIN_DIR . $type . '/*', GLOB_ONLYDIR )){
		$_plugin_dirs = array_merge($_plugin_dirs, $plugin_dirs);	
	}
	// check
	if(count($_plugin_dirs)>0){
		// loop
		foreach($_plugin_dirs as $_plugin_dir){
			// get name
			$name     = pathinfo($_plugin_dir,PATHINFO_BASENAME);
			// set intarnal name
			$filename = 'mgm_plugin_'.$name.'.php';
			// check trigger 
			if(file_exists($_plugin_dir . DIRECTORY_SEPARATOR . $filename)){
				// set
				$plugins[] = pathinfo($_plugin_dir,PATHINFO_BASENAME);
			}
		}
	}	
	// return 
	return $plugins;
}
/**
 * Magic Members get plugin
 * wrapper for only plugin object, loaded on demand
 *
 * @package MagicMembers
 * @since 2.5
 * @param string, plugin name
 * @return object
 */ 
function mgm_get_plugin($plugin_name){
	global $mgm_plugins;
	
	// append prefix
	if(!preg_match('/^mgm_plugin_/',$plugin_name)) $plugin_name = 'mgm_plugin_'.$plugin_name;	
	
	// check if loaded 
	if(isset($mgm_plugins[$plugin_name])) return $mgm_plugins[$plugin_name];
	
	// plugin folder name
	$plugin_folder = str_replace('mgm_plugin_', '', $plugin_name);
	
	// plugin class name
	$plugin_class = $plugin_name; // to allow override
	
	// load file if no class
	if(!class_exists($plugin_class)){
		/*
		// get path
		$plugin_path = MGM_PLUGIN_DIR . implode(DIRECTORY_SEPARATOR, array($plugin_folder, $plugin_name)) . '.php';
		// if found
		@include_once($plugin_path);	
		*/
		
		// plugin_file
		$plugin_file = MGM_PLUGIN_DIR . implode(DIRECTORY_SEPARATOR, array($plugin_folder, $plugin_name)) . '.php';		
		// check
		if(file_exists($plugin_file)){			
			// include
			@include_once($plugin_file);	
		}else{
		// check in extend
			// plugin_file
			$plugin_file = MGM_EXTEND_PLUGIN_DIR . implode(DIRECTORY_SEPARATOR, array($plugin_folder, $plugin_name)) . '.php';
			// include
			if(file_exists($plugin_file)){			
				// include
				@include_once($plugin_file);	
			}else{
				die(__(sprintf('No such plugin: %s ',$plugin_name),'mgm'));
			}
		}	
	}	

	// return object form database 
	if($plugin_object = get_option($plugin_name)) return $plugin_object;	
	
	// return default	
	if(class_exists($plugin_class)){
		// get object
		$plugin_object = & new $plugin_class;	
		// update object
		// update_option($plugin_name, $plugin_object);
		// return
		// return $plugin_object;
		return $mgm_plugins[$plugin_name] = $plugin_object;
	}
	
	// return dummy
	return new stdClass;		
}
// end file /core/inc/mgm_core_functions.php