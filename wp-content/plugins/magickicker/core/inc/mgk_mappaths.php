<?php
// use function wrapper for cgi servers
function mgk_set_include_path(){	
	// use new style from MGM	
	set_include_path(get_include_path(). PATH_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(MGK_CORE_DIR)));
}

// check cgi
function mgk_is_cgi_server(){    
	$sapi_type = php_sapi_name();
	if (substr($sapi_type, 0, 3) == 'cgi') {
		return true;
	} else {
		return false;
	}
}

// load for once works in php apache module
mgk_set_include_path();
// end of file