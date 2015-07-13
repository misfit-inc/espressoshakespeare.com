<?php
// constants
// directory separator
define('MGK_DS'                 , DIRECTORY_SEPARATOR); 		 
// base dir
define('MGK_BASE_DIR'           , WP_PLUGIN_DIR . MGK_DS . 'magickicker'. MGK_DS);
define('MGK_BASE_URL'           , WP_PLUGIN_URL . '/magickicker/'); 
// base names/paths for plugin activation
define('MGK_PLUGIN_NAME'        , trailingslashit('magickicker/magickicker.php') ); // magickicker/magickicker.php/
define('MGK_PLUGIN_CORE_NAME'   , trailingslashit(dirname( plugin_basename( __FILE__ )))); // magickicker/core
define('MGK_CORE_DIR'           , plugin_dir_path( __FILE__ ) );
define('MGK_CORE_URL'           , plugin_dir_url( __FILE__ ) );	
define('MGK_ASSETS_DIR'         , MGK_CORE_DIR . 'assets' . MGK_DS);
define('MGK_ASSETS_URL'         , MGK_CORE_URL . 'assets/' );

// constants
require_once('inc/mgk_constants.php');  
// paths
require_once('inc/mgk_mappaths.php'); 
// functions
require_once('inc/mgk_auth.php');
require_once('inc/mgk_functions.php');
require_once('inc/mgk_db_helpers.php'); 
require_once('inc/mgk_html_helpers.php');
require_once('inc/mgk_tips_info.php');
require_once('inc/mgk_videos_info.php');
// classes
require_once('inc/classes/mgk_uri.php'); 
require_once('inc/classes/mgk_ajax.php');    
require_once('inc/classes/mgk_pager.php');	
// globals
global $mgk_ajax;

// admin menu
function mgk_admin_menu() {        
    add_menu_page(__('Magic Kicker','mgk'), __('Magic Kicker','mgk'), 'administrator', 'mgk/admin', 'mgk_admin_load_ui', MGK_ASSETS_URL.'images/icons/anchor.png');   
}

// admin manage
function mgk_admin_load_ui(){	
    global $wpdb,$mgk_ajax;    	
    // ajax
    $mgk_ajax=new mgk_ajax();   
    // build
    $mgk_ajax->build_output();

    // load interface
    require_once('admin/mgk_index.php'); 
} // end function

// activate
function mgk_activate(){

    global $wpdb;

    // after activation

    if(mgk_get_auth()){            

        // when not run

        if(!defined('mgk_installed')){

            // define tables constants

            define('TBL_MGK_BLOCKED_IPS'   , $wpdb->prefix . 'mgk_blocked_ips'); //mgk_block_list was previous

            define('TBL_MGK_USER_IPS'      , $wpdb->prefix . 'mgk_user_ips'); // mgk_ip_log    
			
			define('TBL_MGK_ACCESSED_URLS' , $wpdb->prefix . 'mgk_accessed_urls'); // mgk_url_log  
			              

            // migration once / it will take care                 

            require_once('migration/mgk_migrate.php');          

            // define

            define('mgk_installed',date('dmY'));

        }

    }    

}

// activate

function mgk_deactivate($remove_all=false){

    global $wpdb;

    

    // if remove

    if($remove_all){

        // uninstall options    

        $wpdb->query('DELETE FROM ' . $wpdb->options . ' WHERE `option_name` LIKE "mgk_%" ');

        // tables

        $tables=array(TBL_MGK_BLOCKED_IPS,TBL_MGK_USER_IPS,TBL_MGK_ACCESSED_URLS);

        foreach($tables as $table){        

            $wpdb->query("DROP TABLE `{$table}`");        

        }

    }
}

// admin scripts, css
function mgk_admin_load_scripts() { 
	// check admin ui
	if(isset($_GET['page']) && preg_match('/^mgk\/admin/',$_GET['page'])){
		// jquery from WP core	
		wp_enqueue_script('jquery');	
		// form
		// wp_enqueue_script('jquery-form');   
	
		// load respective JQueryUI
		$jqueryui_version = mgk_get_jqueryui_version();
		// load ui
		wp_enqueue_script('mgk-jquery-ui'         , MGK_ASSETS_URL . 'js/jquery/jquery.ui/jquery-ui-'.$jqueryui_version.'.min.js');  
	
		// helpers scripts
		wp_enqueue_script('mgk-jquery-form'       , MGK_ASSETS_URL . 'js/jquery/jquery.form.js'); 
		wp_enqueue_script('mgk-jquery-validate'   , MGK_ASSETS_URL . 'js/jquery/jquery.validate.pack.js');
		wp_enqueue_script('mgk-jquery-scrollto'   , MGK_ASSETS_URL . 'js/jquery/jquery.scrollTo-min.js');	
		
		// custom scripts	
		wp_enqueue_script('mgk-autotabs'          , MGK_ASSETS_URL . 'js/autotabs.js');
		wp_enqueue_script('mgk-string'            , MGK_ASSETS_URL . 'js/string.js');  
		wp_enqueue_script('mgk-helpers'           , MGK_ASSETS_URL . 'js/helpers.js');     		 
			  
		// only used for admin
		wp_enqueue_script('mgk-jquery-ajaxupload' , MGK_ASSETS_URL . 'js/jquery/jquery.ajaxfileupload.js'); 
		wp_enqueue_script('mgk-jquery-corner'     , MGK_ASSETS_URL . 'js/jquery/jquery.corner.js');        
		wp_enqueue_script('mgk-nicedit'           , MGK_ASSETS_URL . 'js/nicedit/nicedit.js'); 
		
		// ui css    
		wp_enqueue_style('mgk-ui-css' , MGK_ASSETS_URL . 'css/mgk/jquery-ui.css' );
		
		// only admin styles    
		if (is_admin()) :
			// styles
			wp_enqueue_style('mgk-adminstyles', MGK_ASSETS_URL . 'css/admin.css' );       
		endif;
	}
} 

// setup

function mgk_plugins_loaded(){     

    // scripts, css for admin only
	if (is_admin() ){   
    	add_action('init', 'mgk_admin_load_scripts');   
	}

    // lang    

    load_plugin_textdomain( 'mgk', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );    

    // after activation, enable lock

    if(mgk_get_auth()){
		
		// authenticate filter
		add_filter('wp_authenticate_user', 'mgk_authenticate_user', 30);
		// content filter
		add_filter('the_content', 'mgk_filter_post', 50);
		// init action, lockout		 
		add_action('init','mgk_lockout_user', 30); 
		// log url access		      
		add_action('init','mgk_url_access_log', 31);  		
		// activate
		mgk_check_activate_account();
    }    

    // admin actions    

    if (is_admin() ){       

      // add menu    

      add_action('admin_menu', 'mgk_admin_menu' );    

    }       
  

    // activation trigger      

    // $plugin_file = trailingslashit(str_replace('core/mgk_main.php','magickicker.php',str_replace('\\', '/', __FILE__)));      

    // auto call

    register_activation_hook(MGK_PLUGIN_NAME, 'mgk_activate');

    // once enabled this will erase all data for plugin, 

    // 27/02/2010 keep it enabled as we need to remove schedule hook

    // no data is removed unless called with true argument

    // done at settings remove option

    register_deactivation_hook(MGK_PLUGIN_NAME, 'mgk_deactivate');

    // force call once

    mgk_activate();            

}
// setup  
add_action('plugins_loaded', 'mgk_plugins_loaded'); 
// end of file 