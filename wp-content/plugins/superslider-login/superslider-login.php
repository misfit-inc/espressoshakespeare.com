<?php
/*
Plugin Name: SuperSlider-Login
Plugin URI: http://wp-superslider.com/superslider/superslider-login
Description:  A slidein login panel. Theme based, animated, automatic user detection , uses mootools 1.2 java script.
Tags: login, slider, superslider, tab, tabs, mootools 1.2, mootools
Text Domain: superslider-login 
Author: Daiv Mowbray
Author URI: wp-superslider.com
Version: 1.0

*/ 

/*  Copyright 2008  Daiv Mowbray  (email : daiv.mowbray@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('ssLogin')) {
    class ssLogin	{
				/**
		* @var string   The name the options are saved under in the database.
		*/
		var $js_path;
		var $loginOpOut;
		var $optionsName = "ssLogin_options";
		var $login_domain = 'superslider-login';
		var $base_over_ride;
		var $ssBaseOpOut;
		
		function set_login_paths()
			{
			if ( !defined( 'WP_CONTENT_URL' ) )
				define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
			if ( !defined( 'WP_CONTENT_DIR' ) )
				define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
			if ( !defined( 'WP_PLUGIN_URL' ) )
				define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
			if ( !defined( 'WP_PLUGIN_DIR' ) )
				define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
			if ( !defined( 'WP_LANG_DIR') )
				define( 'WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );

			}
		
		/**
		* PHP 4 Compatible Constructor
		*/
		function ssLogin(){//$this->__construct();
			
			ssLogin::login();
		
		}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
		
			self::login();
		
		}
		
		function language_switcher() {

			$superslider_login_locale = get_locale();
			$superslider_login_mofile = dirname(__FILE__) . "/languages/superslider-login-".$superslider_login_locale.".mo";
			$plugin_dir = basename(dirname(__FILE__));

			load_plugin_textdomain($login_domain, 'wp-content/plugins/languages/' . $plugin_dir );		
		}
		
		/**
		* Retrieves the options from the database.
		* @return array
		*/
		function get_login_options() {
			$loginOptions = array(
				"load_moo"    => "on",
				"css_load"    => "default",
				"css_theme"   => "default", 
				"opacity"     => "0.7",
				"resize_dur"  => "800",
				"mode"        => "horizontal",
				"trans_type"	=> "Sine",
				"trans_typeout" => "easeOut",
				"loginlink" => ".comment-reply-login",
				"header_text"  =>  "Welcome",
				"message_text"  =>  "Remember the Prime Directive of Netiquette: Those are real people out there.",
				"guest_header_text"  =>  "Join",
				"guest_message_text"  =>  "Join us as we spread the word.",
				'delete_options' => ''				
				);

			$getOptions = get_option($this->optionsName);
				if (!empty($getOptions)) {
					foreach ($getOptions as $key => $option) {
						$loginOptions[$key] = $option;
					}
			}
			update_option($this->optionsName, $loginOptions);
				//return $loginOptions;
		}
		
		/**
		* Saves the admin options to the database.
		*/
		function saveloginOptions(){
			update_option($this->optionsName, $this->loginOptions);
		}
		
		/**
		* Loads functions into WP API
		* 
		*/
		function login_init() {

			$this->loginOptions = $this->get_login_options();
			$this->set_login_paths();
			$this->js_path = WP_CONTENT_URL . '/plugins/'. plugin_basename(dirname(__FILE__)) . '/js/';
			
			// lets see if the base plugin is here and get its options
			if (class_exists('ssBase')) {
					$this->ssBaseOpOut = get_option('ssBase_options');
					extract($this->ssBaseOpOut);
					$this->base_over_ride = $ss_global_over_ride;
				}else{
				$this->base_over_ride = 'false';
				}
			wp_register_script(
			'moocore',
			$this->js_path.'mootools-core-1.4.1-full-compat-yc.js',
			NULL, '1.4.1');
			
			wp_register_script(
			'moomore',
			$this->js_path. 'mootools-more.js',
			array( 'moocore' ), '1.4.0.1');
					
            $this->language_switcher();
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function login_ui(){
			global $base_over_ride;
			global $login_domain;
			include_once 'admin/superslider-login-ui.php';
		} 
		
		function login_admin_pages(){
		
			if( function_exists('add_options_page') ) {
				if( current_user_can('manage_options') ) {
					if (!class_exists('ssBase')) $plugin_page = add_options_page(__('Superslider Login', 'superslider-login'),__('SuperSlider-Login', 'superslider-login'), 8, 'superslider-login', array(&$this, 'login_ui'));
					add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_login'), 10, 2 );	
					
					add_action ( 'admin_print_styles', array(&$this,'ssbox_admin_style'));
					if (!class_exists('ssBase')) add_action('admin_print_scripts-'.$plugin_page, array(&$this,'sslogin_admin_script'));
				}					
			}
		}
		function sslogin_admin_script(){
		      wp_enqueue_script('jquery-ui-tabs');	// this should load the jquery tabs script into head
		
	   }
		/**
		* Add link to options page from plugin list.
		*/
        function filter_plugin_login($links, $file) {
             static $this_plugin;
                if (  ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
    
            if (  $file == $this_plugin )
                $settings_link = '<a href="admin.php?page=superslider-login">'.__('Settings', $login_domain).'</a>';
                array_unshift( $links, $settings_link ); //  before other links
                return $links;
        }

		
		/**
		*	remove options from DB upon deactivation
		*/
		function login_ops_deactivation(){		
			if($this->loginOpOut[delete_options] == true){
			 delete_option($this->optionsName);
		  }
		}

		/**
		* 
		*/
		
		function login_starter(){

			extract($this->loginOpOut);
			$resizeTrans = 'Fx.Transitions.'.$trans_type.'.'.$trans_typeout;
			
	$myscroll = "var loginScroller = new Fx.Scroll(window,{      
            wait: 'false',
            offset: {'x': 0, 'y': -20},
            wheelStops: 'false',
            duration:".$resize_dur."});";

			$mylogin =	"<script type=\"text/javascript\">
				// <![CDATA[
			window.addEvent('domready', function() {                              
                var loginpanel =  document.id('loginpanel'); 
                var openlogin =  document.id('openlogin');
                var closelogin =  document.id('closelogin');              
                var loginSlide = new Fx.Slide('loginpanel',
                    {duration:".$resize_dur.",
                    transition: ".$resizeTrans.",
                    mode: '".$mode."',
                    wait: false,
                        onComplete: function(){  }
                    });               
                loginpanel.setStyle('display','block');
                loginSlide.hide();                
                openlogin.addEvent('click', function(e){
                    e.stop();
                    loginSlide.slideIn();
                    this.setStyle('display','none');
                    closelogin.setStyle('display','inline-block');                    
                });
                closelogin.addEvent('click', function(e){
                    e.stop();
                    loginSlide.slideOut();
                    this.setStyle('display','none');
                    openlogin.setStyle('display','inline-block');                   
                });
                document.getElements('".$loginlink."').addEvent('click', function(event){                
                     event.stop();
                     loginSlide.slideIn();
                     openlogin.setStyle('display','none');
                     closelogin.setStyle('display','inline-block'); 
                     loginScroller.toTop(0, 0);
                });
                ".$myscroll."
			});// ]]></script>	";

			echo $mylogin;

		}
		
		/**
		* Tells WordPress to load the scripts
		*/
		function login_add_scripts() {
			global $base_over_ride;

			extract($this->loginOpOut);
			
			if (!is_admin()) {				
                if (function_exists('wp_enqueue_script')) {
                if ($this->base_over_ride != "on") {
                    if ($load_moo == 'on'){
                        wp_enqueue_script('moocore');		
                        wp_enqueue_script('moomore');
                    }
                }							
				}	
			}
			
		}
		
		/**
		* Adds a link to the stylesheet to the header
		*/
		function login_add_css() {
		
            extract($this->loginOpOut);

            if ( (class_exists('ssBase')) && ($this->ssBaseOpOut['ss_global_over_ride']) ) { extract($this->ssBaseOpOut); }

            if ($css_load == 'default') {
                    $cssFile = WP_PLUGIN_URL.'/superslider-login/plugin-data/superslider/ssLogin/'.$css_theme.'/'.$css_theme.'_'.$mode.'.css';
   
                } elseif ($css_load == 'pluginData') {
                    $cssFile = WP_CONTENT_URL.'/plugin-data/superslider/ssLogin/'.$css_theme.'/'.$css_theme.'_'.$mode.'.css';
     
                }elseif ($css_load == 'off') {
                    $cssFile = '';
                    
                }
             if ($css_load !== 'off'){
		        wp_register_style('superslider_login', $cssFile);
                wp_enqueue_style( 'superslider_login');
		      
		      }
		}
		
		
		function login() {
			
			$this->loginOpOut = get_option($this->optionsName);
			
			register_activation_hook(__FILE__, array(&$this,'login_init') ); //http://codex.wordpress.org/Function_Reference/register_activation_hook
			register_deactivation_hook( __FILE__, array(&$this,'login_ops_deactivation') ); //http://codex.wordpress.org/Function_Reference/register_deactivation_hook
			
			add_action ( 'init', array(&$this,'login_init' ) );			
			add_action ( 'admin_menu', array(&$this,'login_admin_pages'));
			add_action ( 'init' , array(&$this,'load_login') );

		}
		function load_login(){
		   if (!is_admin()){
		    	 add_action ( 'wp_print_styles', array(&$this,'login_add_css'));
		    	 add_action ( 'wp_print_scripts', array(&$this,'login_add_scripts')); //this loads the mootools scripts.
			     add_action ( 'wp_footer', array(&$this,'login_panel_out'));
			     add_action ( 'wp_footer' , array(&$this,'login_starter') );
		    }
		}
		/**
		*     can I use this: esc_html() to add html to the messages ?
		*/
		function login_panel_out(){

		    include_once 'login_panel.php';
		  
		}
		
		function ssbox_admin_style(){
			if ($this->base_over_ride != "on") {
				$cssAdminFile = WP_PLUGIN_URL.'/superslider-login/admin/ss_admin_style.css';    			
				
				wp_register_style('superslider_admin', $cssAdminFile);
                wp_enqueue_style( 'superslider_admin');
			}	
			
		}


    }// end class login
}// end if class login

//instantiate the class
if (class_exists('ssLogin')) {
	$myssLogin = new ssLogin();
}
?>