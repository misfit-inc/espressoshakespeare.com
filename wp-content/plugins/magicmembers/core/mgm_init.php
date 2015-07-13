<?php 
/**
 * Magic Members Init class
 *
 * @package MagicMembers
 * @since 2.0
 */
class mgm_init{	
	// loaded files
	var $_loaded_files = array();
	
	// construct
	function __construct(){
		// php 4
		$this->mgm_init();
	}
	
	// php4 construct
	function mgm_init(){
		//session initialization:
		add_action('init', array($this, '_initialize_session'));
		
		// define internal constants
		$this->_constants();			
		// load library files	
		$this->_autoload();			
		// create default dirs
		mgm_create_files_dir(array(WP_UPLOAD_DIR, MGM_FILES_DIR, MGM_FILES_EXPORT_DIR, MGM_FILES_DOWNLOAD_DIR, 
		                           MGM_FILES_LOG_DIR, MGM_FILES_MODULE_DIR, MGM_FILES_UPLOADED_IMAGE_DIR));		
	}
	
	// setup
	function setup(){		
		// loaded 
		add_action('plugins_loaded', array($this, 'plugins_loaded'), 100);
	}
		
	// loaded
	function plugins_loaded(){
		global $wp_query;		
		// do the conversion	
		$this->_conversion();		
		// check cookie
		mgm_check_cookie();		
		// load language domain	
		load_plugin_textdomain( 'mgm', false, MGM_PLUGIN_CORE_NAME . 'lang' );			
		// email filters
		$this->_set_mail_filters();				
		// add after verify, must be accessible to non admins too		
		$mgm_auth = mgm_get_class('auth');		
		// verify
		if($mgm_auth->verify()){			
			// daily cron - used for reminder mails, with this any page hit will fire the cron, not limited to admin 
			add_action('mgm_daily_schedule', array($this, 'process_daily_schedule'));// hook on event	
			add_action('mgm_quarterhourly_schedule'		, array($this, 'process_quarterhourly_schedule'));					
		}		
		// wp-admin actions						
		if (is_admin() ){ 	  
	 	 	// add menu	
	 	 	add_action('admin_menu', array($this, 'admin_menu'));	
			// scripts
			//load scripts and css only when mgm interface is loaded/edit post page
			if(mgm_if_load_admin_scripts())
				add_action('init', array($this, 'admin_load_scripts')); // testing			
			// activation hook
			register_activation_hook(MGM_PLUGIN_NAME, array($this, 'activate'));	
			// deactivation hook
			register_deactivation_hook(MGM_PLUGIN_NAME, array($this, 'deactivate'));
			// force activation once
			$this->activate();	
		}else {
			wp_enqueue_script('jquery');
		}
		
		// test schedular
		// $this->process_daily_schedule();	
		// show messages if any 
		add_action('admin_notices', array($this,'_admin_notices'));
		// global variable for dynamic scripts;		
		global $mgm_scripts;				
	}	
	
	// activate
	function activate(){
		global $wpdb;				
		// get auth
		$auth = mgm_get_class('auth');
		// verify key		
		if($auth->verify()){					
			// migration once / it will take care 	
			require_once('migration/mgm_migrate.php');											
			
			// set up daily cron event, once
			if( !wp_next_scheduled('mgm_daily_schedule') ){
				// add
				wp_schedule_event(time(), 'daily', 'mgm_daily_schedule'); // the name of event/schedule hook	
			}			
			
			// run others once
			if(!get_option('mgm_version') || (version_compare(get_option('mgm_upgrade_id'), '1.8', '<'))){// add version/upgrade compare, transaction added on 1.8 and pages later
				// create pages
				mgm_create_default_pages();
				
				// enable active modules
				$payment_modules = mgm_get_class('system')->get_active_modules('payment');
				// active modules
				if($payment_modules){
					// loop
					foreach($payment_modules as $module){
						// install modules
						mgm_get_module($module, 'payment')->enable();// enable only
					}
				}	
				// update version
				update_option('mgm_version', $auth->get_product_info('product_version')); 
				update_option('mgm_build', MGM_BUILD); 				
			}				
		}			
	}	
	
	// deactivate
	function deactivate($force=false){
		global $wpdb;
		
		// if remove by force
		if($force){
			// uninstall options	
			$wpdb->query("DELETE FROM " . $wpdb->options . " WHERE `option_name` LIKE 'mgm_%' ");
			// user meta			
			$wpdb->query("DELETE FROM " . $wpdb->usermeta . " WHERE `meta_key ` LIKE 'mgm_%' ");
			// post meta				
			$wpdb->query("DELETE FROM " . $wpdb->postmeta . " WHERE `meta_key ` LIKE '_mgm_%' ");
			// tables			
			foreach($this->_get_tables() as $table){		
				$wpdb->query("DROP TABLE `{$table}`");		
			}
		}
		// clear daily event hook
		wp_clear_scheduled_hook('mgm_daily_schedule');
	}
	
	// admin menu
	function admin_menu(){		
		// page
		$page = (isset($_GET['page']) && preg_match('/^mgm\/admin/',$_GET['page'])) ? $_GET['page'] : 'mgm/admin';
		// add main menu
    	add_menu_page(__('Magic Members','mgm'), __('Magic Members','mgm'), 'administrator', $page, array($this, 'admin_load_ui'), MGM_ASSETS_URL.'images/icons/status_offline.png');				
		// add after verify
		if(mgm_get_class('auth')->verify()){
			// current user
			$current_user = wp_get_current_user();
			// current user rolw
			$current_user_role = (isset($current_user->roles[0]))?$current_user->roles[0]: 'subscriber';			
			// profile menu
			add_submenu_page('profile.php', __('Membership Details','mgm'), __('Membership Details','mgm'), $current_user_role, 'mgm/profile',array($this, 'admin_load_ui'));
			// restricted
			add_submenu_page('profile.php', __('Members Content','mgm'), __('Members Content','mgm'), $current_user_role, 'mgm/membership/content',array($this, 'admin_load_ui'));		
		}
	}
	
	// loads page on action
	function admin_load_ui() {		
		// get current user
		get_currentuserinfo();		
		// page
		$page = $_GET['page'] ;		
		// get page
		switch($page){
			case 'mgm/profile':	
			case 'mgm/membership/content':	
			case 'mgm/admin':		
				// page name  				
				$page_name = str_replace('/','_',$page);
			break;	
			case 'mgm/admin/files':	
				// page name  				
				$page_name = 'mgm_files';
			break;				
			default:				
				// all in ajax
				$page_name = str_replace('/', '_', str_replace('mgm/admin/', 'mgm_', $page));				
			break;		
		}
		// check file
		if(file_exists(MGM_CORE_DIR . 'admin' . DIRECTORY_SEPARATOR . $page_name . '.php')){									
			// load page class 
			// MARK not found for some server			
			$page_class = include_once(MGM_CORE_DIR . 'admin' . DIRECTORY_SEPARATOR . $page_name . '.php'); 					
			// echo $page_class;
			if(class_exists($page_class)){
				// object
				$page_class_obj = new $page_class;
				// load
				$page_class_obj->init();				
			}else{
				 // wp_redirect(admin_url());
				 die(sprintf(__("Class %s Does not exist!",'mgm'), $page_class));
			}
		}else{
			// wp_redirect(admin_url('/admin.php?page=mgm/admin'));
			die(sprintf(__("File %s Does not exist!",'mgm'), $page_name . '.php'));
		}			
	}
	
	// process_daily_schedule
	function process_daily_schedule(){		
		// object
		$mgm_schedular = mgm_get_class('schedular');		
		// add
		$mgm_schedular->add_schedule('daily','reminder_mailer');// reminder mails
		$mgm_schedular->add_schedule('daily','reset_expiration');// reset expire date
		// run
		$mgm_schedular->run('daily');
	}
	
	// process 15 minutes schedule
	function process_quarterhourly_schedule(){	
		$mgm_schedular = mgm_get_class('schedular');
		$mgm_schedular->add_schedule('quarterhourly', 'epoch_dataplus_transactions');
		//run schedules task
		$mgm_schedular->run('quarterhourly');
	}
	
	// public_load_min_scripts, for register and payment
	function public_load_min_scripts() {	
		// jquery from WP core, keep clash in mind, do not consider on admin
		if(get_option('mgm_disable_core_jquery') != 'Y' || is_admin()){
			wp_enqueue_script('jquery');
		}
		// form
		// wp_enqueue_script('jquery-form');	
		
		// helpers scripts		
		wp_enqueue_script('mgm-jquery-validate' , MGM_ASSETS_URL . 'js/jquery/jquery.validate.pack.js');
		wp_enqueue_script('mgm-jquery-metadata' , MGM_ASSETS_URL . 'js/jquery/jquery.metadata.js');
		wp_enqueue_script('mgm-jquery-form'     , MGM_ASSETS_URL . 'js/jquery/jquery.form.js');  
		
		// custom scripts
		wp_enqueue_script('mgm-helpers'         , MGM_ASSETS_URL . 'js/helpers.js'); 		
		wp_enqueue_script('mgm-string'          , MGM_ASSETS_URL . 'js/string.js'); 
		
		// ui css	
		wp_enqueue_style('mgm-ui-css'           , MGM_ASSETS_URL . 'css/mgm/jquery-ui.css' );
		
		// css		
		wp_enqueue_style('mgm-site-css'         , MGM_ASSETS_URL . 'css/mgm_site.css' );		
	}
	
	// public_load_scripts
	function public_load_scripts() {	
		// jquery from WP core, keep clash in mind, do not consider on admin
		if(get_option('mgm_disable_core_jquery') != 'Y' || is_admin()){
			wp_enqueue_script('jquery');
		}
		// form
		// wp_enqueue_script('jquery-form');	
		
		// helpers scripts		
		wp_enqueue_script('mgm-jquery-validate' , MGM_ASSETS_URL . 'js/jquery/jquery.validate.pack.js');
		wp_enqueue_script('mgm-jquery-metadata' , MGM_ASSETS_URL . 'js/jquery/jquery.metadata.js');
		wp_enqueue_script('mgm-jquery-form'     , MGM_ASSETS_URL . 'js/jquery/jquery.form.js');  
		
		// custom scripts
		wp_enqueue_script('mgm-helpers'         , MGM_ASSETS_URL . 'js/helpers.js'); 		
		wp_enqueue_script('mgm-string'          , MGM_ASSETS_URL . 'js/string.js'); 
		
		// ui css	
		wp_enqueue_style('mgm-ui-css', MGM_ASSETS_URL . 'css/mgm/jquery-ui.css' );
		
		// css
		if (!is_admin()) :
			wp_enqueue_style('mgm-site-css'   , MGM_ASSETS_URL . 'css/mgm_site.css' );
		endif;	
	}
		
	// admin_load_scripts
	function admin_load_scripts() {
		// check page and remove conflicts, mainly css and jQuery 
		if(isset($_GET['page']) && $_GET['page'] == 'mgm/admin'){
			 $this->adjust_ui_conflicts();			
		}	
		// load public
		$this->public_load_scripts();			
		// load respective jQueryUI
		$jqueryui_version = mgm_get_jqueryui_version();	
		// load ui
		wp_enqueue_script('mgm-jquery-ui', MGM_ASSETS_URL . 'js/jquery/jquery.ui/jquery-ui-'.$jqueryui_version.'.min.js');
		
		// helpers scripts		
		wp_enqueue_script('mgm-jquery-ajaxupload' , MGM_ASSETS_URL . 'js/jquery/jquery.ajaxfileupload.js');  	
		wp_enqueue_script('mgm-jquery-scrollto'   , MGM_ASSETS_URL . 'js/jquery/jquery.scrollTo-min.js');
		wp_enqueue_script('mgm-jquery-corner'     , MGM_ASSETS_URL . 'js/jquery/jquery.corner.js');	
		wp_enqueue_script('mgm-jquery-tools'      , MGM_ASSETS_URL . 'js/jquery/jquery.tools.min.js');				
		wp_enqueue_script('mgm-nicedit'           , MGM_ASSETS_URL . 'js/nicedit/nicedit.js');		
		wp_enqueue_script('mgm-jquery_collapsible_checkbox_tree', MGM_ASSETS_URL . 'js/jquery/jquery_collapsible_checkbox_tree.js');			
		
		// only admin styles	
		if (is_admin()) :
			// styles
			wp_enqueue_style('mgm-admincss'       , MGM_ASSETS_URL . 'css/mgm_admin.css' );	
			wp_enqueue_style('mgm-adminoverlay'   , MGM_ASSETS_URL . 'css/mgm_overlay_apple.css' );	
			wp_enqueue_style('mgm-jquery_collapsible_checkbox_treey'   , MGM_ASSETS_URL . 'css/jquery_collapsible_checkbox_tree.css' );	
		// member list tree:			
		endif;	
	}
	
	// define constants
	function _constants(){
		// directory separator
		define('MGM_DS'                 , DIRECTORY_SEPARATOR); 
				 
		// base dir
 		define('MGM_BASE_DIR'           , WP_PLUGIN_DIR . MGM_DS . 'magicmembers'. MGM_DS);
		define('MGM_BASE_URL'           , WP_PLUGIN_URL . '/magicmembers/'); 
					
		// base names/paths for plugin activation
		define('MGM_PLUGIN_NAME'        , trailingslashit('magicmembers/magicmembers.php') ); // magicmembers/magicmembers.php/
		define('MGM_PLUGIN_CORE_NAME'   , trailingslashit(dirname( plugin_basename( __FILE__ )))); // magicmembers/core
		define('MGM_CORE_DIR'           , plugin_dir_path( __FILE__ ) );// absolute path to this folder , with trailing slash
		define('MGM_CORE_URL'           , plugin_dir_url( __FILE__ ) );	// absolute url to this folder , with trailing slash		  
		
		// assets
		define('MGM_ASSETS_DIR'         , MGM_CORE_DIR . 'assets' . MGM_DS);	
		define('MGM_ASSETS_URL'         , MGM_CORE_URL . 'assets/' );	
		
		// core library
		define('MGM_LIBRARY_DIR'        , MGM_CORE_DIR . 'libs' . MGM_DS );	
		define('MGM_LIBRARY_URL'        , MGM_CORE_URL . 'libs/' );	
			
		// core hooks & processors
		define('MGM_HOOKS_DIR'          , MGM_CORE_DIR . 'hooks' . MGM_DS ); 
		define('MGM_HOOKS_URL'          , MGM_CORE_URL . 'hooks/' );
			
		// core modules
		define('MGM_MODULE_DIR'         , MGM_CORE_DIR . 'modules' . MGM_DS );	
		define('MGM_MODULE_URL'         , MGM_CORE_URL . 'modules/' );	
		
		// core plugins
		define('MGM_PLUGIN_DIR'        , MGM_CORE_DIR . 'plugins' . MGM_DS ); 
		define('MGM_PLUGIN_URL'        , MGM_CORE_URL . 'plugins/' );
		
		// core widgets
		define('MGM_WIDGET_DIR'        , MGM_CORE_DIR . 'widgets' . MGM_DS ); 
		define('MGM_WIDGET_URL'        , MGM_CORE_URL . 'widgets/' );	
		
		// core api
		define('MGM_API_DIR'           , MGM_CORE_DIR . 'api' . MGM_DS ); 
		define('MGM_API_URL'           , MGM_CORE_URL . 'api/' );			
		
		// extend dir
		if(!defined('MGM_EXTEND_DIR')){
			define('MGM_EXTEND_DIR'     , MGM_BASE_DIR . 'extend' . MGM_DS ); 
		}
		// extend url
		if(!defined('MGM_EXTEND_URL')){
			define('MGM_EXTEND_URL'     , MGM_BASE_URL . 'extend/' ); 
		}
		
		// extended libs
		if(!defined('MGM_EXTEND_LIBRARY_DIR')){
			define('MGM_EXTEND_LIBRARY_DIR'    , MGM_EXTEND_DIR . 'libs' . MGM_DS ); 
		}
		// url
		if(!defined('MGM_EXTEND_LIBRARY_URL')){
			define('MGM_EXTEND_LIBRARY_URL'    , MGM_EXTEND_URL . 'libs/' ); 
		}
		
		// extended modules
		if(!defined('MGM_EXTEND_MODULE_DIR')){
			define('MGM_EXTEND_MODULE_DIR'    , MGM_EXTEND_DIR . 'modules' . MGM_DS ); 
		}
		// module url
		if(!defined('MGM_EXTEND_MODULE_URL')){
			define('MGM_EXTEND_MODULE_URL'    , MGM_EXTEND_URL . 'modules/' ); 
		}
		
		// extended plugins
		if(!defined('MGM_EXTEND_PLUGIN_DIR')){
			define('MGM_EXTEND_PLUGIN_DIR'    , MGM_EXTEND_DIR . 'plugins' . MGM_DS ); 
		}
		if(!defined('MGM_EXTEND_PLUGIN_URL')){
			define('MGM_EXTEND_PLUGIN_URL'    , MGM_EXTEND_URL . 'plugins/' ); 
		}
		
		// extended widgets
		if(!defined('MGM_EXTEND_WIDGET_DIR')){
			define('MGM_EXTEND_WIDGET_DIR'    , MGM_EXTEND_DIR . 'widgets' . MGM_DS ); 
		}
		if(!defined('MGM_EXTEND_WIDGET_URL')){
			define('MGM_EXTEND_WIDGET_URL'    , MGM_EXTEND_URL . 'widgets/' ); 
		}
		
		// wp uploads
		define('WP_UPLOAD_DIR'          , WP_CONTENT_DIR . MGM_DS . 'uploads' . MGM_DS );
		define('WP_UPLOAD_URL'          , WP_CONTENT_URL . '/uploads/' );		
		// mgm files
		define('MGM_FILES_DIR'          , WP_UPLOAD_DIR . 'mgm' . MGM_DS);		
		define('MGM_FILES_URL'          , WP_UPLOAD_URL . 'mgm/' );		
		// export files
		define('MGM_FILES_EXPORT_DIR'   , MGM_FILES_DIR . 'exports' . MGM_DS);		
		define('MGM_FILES_EXPORT_URL'   , MGM_FILES_URL . 'exports/' );		
		// download files
		define('MGM_FILES_DOWNLOAD_DIR' , MGM_FILES_DIR . 'downloads' . MGM_DS);		
		define('MGM_FILES_DOWNLOAD_URL' , MGM_FILES_URL . 'downloads/' );		
		// log files
		define('MGM_FILES_LOG_DIR'      , MGM_FILES_DIR . 'logs' . MGM_DS);		
		define('MGM_FILES_LOG_URL'      , MGM_FILES_URL . 'logs/' );		
		// module files
		define('MGM_FILES_MODULE_DIR'   , MGM_FILES_DIR . 'modules' . MGM_DS);		
		define('MGM_FILES_MODULE_URL'   , MGM_FILES_URL . 'modules/' );	
		// image files
		define('MGM_FILES_UPLOADED_IMAGE_DIR' , MGM_FILES_DIR . 'images' . MGM_DS);		
		define('MGM_FILES_UPLOADED_IMAGE_URL' , MGM_FILES_URL . 'images/' );	
	}
	
	// library files
	function _autoload(){			
		// set path to core/libs
		set_include_path(get_include_path(). PATH_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(MGM_CORE_DIR,'libs')));
		// read extend configure
		if(file_exists(MGM_EXTEND_DIR . 'configure.php')) require_once(MGM_EXTEND_DIR . 'configure.php');
				
		// init
		$this->_files = array();	
		
		// scan core dirs, only auto loaded resources included here		
		// config files		
		$this->_add_files(MGM_LIBRARY_DIR   . 'configs'    . MGM_DS . 'mgm_*.php');
		// core files
		$this->_add_files(MGM_LIBRARY_DIR   . 'core'       . MGM_DS . 'mgm_*.php');		
		// component files
		$this->_add_files(MGM_LIBRARY_DIR   . 'components' . MGM_DS . 'mgm_*.php');		
		// class files	
		$this->_add_files(MGM_LIBRARY_DIR   . 'classes'    . MGM_DS . 'mgm_*.php');	
		// utilities files	
		$this->_add_files(MGM_LIBRARY_DIR   . 'utilities'  . MGM_DS . 'mgm_*.php');		
		// function files
		$this->_add_files(MGM_LIBRARY_DIR   . 'functions'  . MGM_DS . 'mgm_*.php');		
				
		// scan extend dirs, only auto loaded resources
		// scope prefix for extended files
		$prefix  = (isset($mgm_config['ext']['prefix'])) ? $mgm_config['ext']['prefix'] : 'mgmx_';	
		// extended core files
		$this->_add_files(MGM_EXTEND_LIBRARY_DIR . 'core'       . MGM_DS . $prefix . '*.php');
		// extended component files
		$this->_add_files(MGM_EXTEND_LIBRARY_DIR . 'components' . MGM_DS . $prefix . '*.php');
		// extended class files
		$this->_add_files(MGM_EXTEND_LIBRARY_DIR . 'classes'    . MGM_DS . $prefix . '*.php');
		// extended function files
		$this->_add_files(MGM_EXTEND_LIBRARY_DIR . 'functions'  . MGM_DS . $prefix . '*.php');
		// extended widget files
		$this->_add_files(MGM_EXTEND_WIDGET_DIR  . $prefix      . 'widget_*.php');
		
		// load files
		$this->_load_files();			
			
		// init widgets so that overload can actually work
		$this->_init_widgets();
		
		// if auth, load hooks and widgets
		if( mgm_get_class('auth')->verify() ){
			// init
			$this->_files = array();	
			// widget files
			$this->_add_files(MGM_WIDGET_DIR    . 'mgm_widget_*.php');	
			// hooks files
			$this->_add_files(MGM_HOOKS_DIR     . 'mgm_*.php');	
			// load files
			$this->_load_files();	
		}	
	}
	
	// load files
	function _load_files(){
		// check
		if( $this->_files ){
			// loop for base/object file
			foreach($this->_files as $_file){					
				// check
				if(basename($_file) == 'mgm_base.php' || basename($_file) == 'mgm_object.php'){						 			
					// include
					include_once( $_file );
					// store
					$this->_loaded_files[] = basename($_file);
				}
			}			
			// load others
			foreach($this->_files as $_file){							 			
				// check
				if(in_array(basename($_file),$this->_loaded_files)) continue;
				// include				
				include_once( $_file );					
				// store				
				$this->_loaded_files[] = basename($_file);
			}
		}	
	}
	
	// init widgets
	function _init_widgets(){
		global $mgm_widgets;
		// check
		if($mgm_widgets){
			// loop
			foreach($mgm_widgets as $mgm_widget){
				// init
				$mgm_widget->init();
			}
		}
	}
	
	// get table names
	function _get_tables(){
		$tables    = array();
		$constants = get_defined_constants();		
		// loop
		foreach($constants as $constant=>$value){
			if(preg_match('/^TBL\_MGM\_/',$constant)){
				$tables[] = $value;
			}
		}
		// return
		return $tables;
	}	

	// add email filters
	function _set_mail_filters(){		
		// filters
		add_filter('wp_mail_from'        , array($this, '_set_mail_from')        , 10);
		add_filter('wp_mail_from_name'   , array($this, '_set_mail_from_name')   , 10);
		//issue#: 473
		//add_filter('wp_mail_content_type', array($this, '_set_mail_content_type'), 10);
		add_filter('wp_mail_charset'     , array($this, '_set_mail_charset')     , 10);	
	}
	
	// return / set from_email
	function _set_mail_from($arg){
		return mgm_get_class('system')->setting['from_email'];
	}
	
	// return / set from_name
	function _set_mail_from_name($arg){
		return mgm_get_class('system')->setting['from_name'];
	}
	
	// return / set content_type
	function _set_mail_content_type($arg){		
		return mgm_get_class('system')->setting['email_content_type'];
	}
	
	// return / set charset
	function _set_mail_charset($arg){
		return mgm_get_class('system')->setting['email_charset'];
	}	
	
	// adjust ui conflicts	
	function adjust_ui_conflicts(){
		// remove tubepress ui css
		remove_action('admin_init', array('org_tubepress_env_wordpress_Admin',  'initAction'));// class method
		
		// remove event_espresso ui css
		remove_action('admin_print_styles', 'event_espresso_config_page_styles'); // function
	}	
	
	// add files
	function _add_files($pattern){
		// get files
		if($_files = glob($pattern)){			
			// add
			$this->_files = array_merge($this->_files, $_files);
		}
	}
	
	// show admin notices
	function _admin_notices(){
		// show
		if (is_super_admin() ){			
			// mgm_daily_schedule not found and mgm_version found, plugin is installed but cron is not
			if( !wp_get_schedule('mgm_daily_schedule') && get_option('mgm_version')){				
				// message
				$message = 'Magic Members Schedular is not installed, this is required to run periodical updates and membership expiration. 
				            Please deactivate and reactivate the Plugin using Plugin Management screen, this will reinstall the Schedular.';
				// show
				mgm_notice(__($message,'mgm'), true);	
			}		
		}
	}
	//session
	function _initialize_session() {
		if(!isset($_SESSION))
			session_start();
	}
	
	// class conversion
	function _conversion(){
		// version_compare(get_option('mgm_build'), '2.22', '<=') || !get_option('mgm_auth_options')
		if(!get_option('mgm_class_conversion')){			
			// fix all
			mgm_fix_class_conversion();// fix
			// update
			update_option('mgm_class_conversion', time());
		}	
		// fix / convert users
		if(!get_option('mgm_converted_users')){
			mgm_fix_users();
		}
		// fix / convert posts
		if(!get_option('mgm_converted_posts')){
			mgm_fix_posts();
		}	
	}
}
// return name of class
return basename(__FILE__,'.php');
// end file /core/mgm_init.php