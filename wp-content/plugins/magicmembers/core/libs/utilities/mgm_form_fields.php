<?php
/**
 * Magic Members form fields generation utility class
 *
 * @package MagicMembers
 * @since 2.5
 */ 
class mgm_form_fields{
	// config
	var $config = array();
	
	// construct
	function __construct($config=array()){
		// php4
		$this->mgm_form_fields($config);
	}
	
	// php4 construct
	function mgm_form_fields($config=array()){
		// defaults
		$config = !empty($config) ? $config : array('wordpres_form'=>false);
		// set 
		$this->set_config($config);
	}
	
	// set_config
	function set_config($config=array()){		
		// set
		$this->config = $config;		
	}
	
	// get_config
	function get_config($key,$default=''){
		// set
		return (isset($this->config[$key])) ? $this->config[$key] : $default;
	}
	
	// generate element
	function get_field_element($field, $name='custom_field', $value=''){
		// check first callback by name
		if(method_exists($this, 'field_'.$field['name'].'_callback')){
			return call_user_func_array(array($this, 'field_'.$field['name'].'_callback'), array($field,$name,$value));
		}
		// check element by type
		if(method_exists($this, 'field_type_'.$field['type'])){
			return call_user_func_array(array($this, 'field_type_'.$field['type']), array($field,$name,$value));
		}		
		
		// error
		if(isset($field['name'])){
			// leave error
			return sprintf('No formatter for %s',$field['type']);
		}
		// return 
		return '';
	}
	
	// by type /////////////////////////////////////////////////////////////////////////////////////////
	// input type
	function field_type_input($field,$name,$value){
		// value filter
		$value = $this->_filter_value($field,$name,$value);		
		// readonly
		$readonly = ($field['attributes']['readonly']) ?'readonly="readonly"':'';
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}
		// extra
		if(isset($field['attributes']['class'])){
			$classes[] = $field['attributes']['class'];
		}
		// join 
		$class = implode(' ',$classes);
		// return
		return sprintf('<input type="%s" name="%s" value="%s" class="%s" %s/>',$field['type'],$this->_get_element_name($field,$name),$value,$class,$readonly);	
	}
	
	// text
	function field_type_text($field,$name,$value){
		// return
		return $this->field_type_input($field,$name,$value);		
	}
	
	// hidden
	function field_type_hidden($field,$name,$value){
		// return
		return $this->field_type_input($field,$name,$value);		
	}	
	
	// password
	function field_type_password($field,$name,$value){
		// return
		return $this->field_type_input($field,$name,$value);	
	}
	
	// textarea
	function field_type_textarea($field,$name,$value){
		// value filter
		$value = $this->_filter_value($field,$name,$value);
		// readonly
		$readonly = ($field['attributes']['readonly']) ?'readonly="readonly"':'';
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}
		// extra		
		$classes[] = 'mgm_field_textarea';		
		// join 
		$class = implode(' ',$classes);
		// return
		return sprintf('<textarea name="%s" class="%s" %s>%s</textarea>',$this->_get_element_name($field,$name),$class,$readonly,$value);
	}
	
	// checkbox
	function field_type_checkbox($field,$name,$value){
		// options
		$options = preg_split('/[;,]/', $field['options']);
		// check
		if(count($options)) {
			// value
			$value = $this->_filter_value($field,$name,$value);
			// return
			return mgm_make_checkbox_group(sprintf('%s[]',$this->_get_element_name($field,$name)),$options,$value,MGM_VALUE_ONLY,'','div');
		}	
		// return default
		return $this->field_type_input($field,$name,$value);	
	}
	
	// radio
	function field_type_radio($field,$name,$value){
		// options
		$options = preg_split('/[;,]/', $field['options']); 		
		// check
		if(count($options)){
			// value
			$value = $this->_filter_value($field,$name,$value);	
			// return
			return mgm_make_radio_group(sprintf('%s',$this->_get_element_name($field,$name)),$options,$value,MGM_VALUE_ONLY,'','div');
		}	
		// return default
		return $this->field_type_input($field,$name,$value);	
	}
	
	// select
	function field_type_select($field,$name,$value,$options=NULL,$type=MGM_VALUE_ONLY,$sel_match='DEFAULT'){
		// get options
		$options = ($options) ? $options : preg_split('/[;,]/', $field['options']); 
		// value
		$value = $this->_filter_value($field,$name,$value);	
		// make options
		$options = mgm_make_combo_options($options,$value,$type,false,$sel_match);		
		// readonly
		$readonly = ($field['attributes']['readonly']) ?'readonly="readonly"':'';
		
		if($field['attributes']['readonly'] && is_array($options) && isset($options[$value]) && !empty($options[$value]))
			$options = array(array_search($value, $options) => $value);
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}			
		// join 
		$class = implode(' ',$classes);
		// return
		return sprintf('<select name="%s" class="%s" %s>%s</select>',$this->_get_element_name($field,$name),$class,$readonly,$options);
	}
	
	// label
	function field_type_label($field,$name,$value){
		// return
		$value = $value ? $value : mgm_stripslashes_deep($field['value']);	
		// return
		return sprintf('<label class="mgm_field_label">%s</label>',$value);
	}
	
	// html
	function field_type_html($field,$name,$value){
		// value
		$value = $value ? $value : html_entity_decode(mgm_stripslashes_deep($field['value']));
		// return
		return sprintf('<div class="mgm_field_html">%s</div>',$value);
	}
	
	// image : experimental
	function field_type_image($field,$name,$value){				  
		$content = '';		
		//make sure readonly if not admin
		$read_only = ($field['attributes']['readonly'] && !is_super_admin()) ? true : false;
		if(!empty($value)) {
			$url = $value;  
			
			$image = sprintf('<img src="%s" alt="%s" >', $url, basename($url) );
			if(!empty($value) && !$read_only) {
				$image .= sprintf('&nbsp;<span onclick="delete_upload(this,\'%s\')"><img style="cursor:pointer;" src="'.MGM_ASSETS_URL . '/images/icons/cross.png" alt="%s" title="%s"></span>', $this->_get_element_name($field,$name), __('Delete','mgm'), __('Delete','mgm') );
			}
			$content = $image;
		}
				
		if(empty($content) && !$read_only) {
			$type = 'file';
			$content .= sprintf('<input type="%s" name="%s" id="%s">',$type, $this->_get_element_name($field,$name), $this->_get_element_id($field,$name));
			$content .= sprintf('&nbsp;<img id="%s" src="%s" alt="%s" title="%s">', 'uploader_loading', esc_url( admin_url( 'images/wpspin_light.gif' ) ), __('Loading','mgm'), __('Loading','mgm') );
		}
		
		$type = 'hidden';		
		$content .= "<br/>" . sprintf('<input type="%s" name="%s" id="%s" %s>',$type, $this->_get_element_name($field,$name), $this->_get_element_id($field,$name), (!empty($value) ? ' value="'.$url.'" ' : ''));
		
		return sprintf('<div class="mgm_field_image">%s</div>', $content);
	}
	//captcha field:
	function field_type_captcha($field,$name){
		$recaptcha = mgm_get_class('recaptcha')->recaptcha_get_html();
		
		return sprintf('<div class="mgm_field_captcha">%s</div>', $recaptcha);
	}
	// by type end/////////////////////////////////////////////////////////////////////////////////////////
	
	// by name ////////////////////////////////////////////////////////////////////////////////////////////	
	
	// autoresponder
	function field_autoresponder_callback($field,$name,$value){
		// value filter
		$value = $this->_filter_value($field,$name,$value);
		// checked
		$checked = ($value == $field['value'])?'checked="true"':'';
		// return
		return sprintf('<input type="checkbox" name="%s" value="%s" align="absmiddle" %s/>',$this->_get_element_name($field,$name),$field['value'],$checked);
	}
	
	// birthdate
	function field_birthdate_callback($field,$name,$value){
		// extra class
		if(!$field['attributes']['readonly']) 
			$field['attributes']['class'] = 'mgm_date';
		// return
		return $this->field_type_input($field,$name,$value);
	}
	
	// username
	function field_username_callback($field,$name,$value){
		// value
		if(!$value){
			$value = (isset($_POST['user_login'])) ? esc_attr(stripslashes($_POST['user_login'])) : '';
		}	
		// readonly
		$readonly = (isset($field['attributes']['readonly']) && $field['attributes']['readonly']) ?'readonly="readonly"':'';
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}			
		// join 
		$class = implode(' ',$classes);
		// default field
		$html  = sprintf('<input type="text" name="user_login" id="user_login2" class="%s" value="%s" %s/>',$class,$value,$readonly);
		// hide on default
		if($this->get_config('wordpres_form')){
			// hide default field
			$html.= '<script language="javascript">jQuery(document).ready(function(){jQuery("#user_login").parent().remove();});</script>';
		}
		// return
		return $html;
	}
	
	// user_login
	function field_user_login_callback($field,$name,$value){
		// return
		return $this->field_username_callback($field,$name,$value);
	}
	
	// email
	function field_email_callback($field,$name,$value){
		// value
		if(!$value){
			$value = (isset($_POST['user_email'])) ? esc_attr(stripslashes($_POST['user_email'])) : '';
		}	
		// readonly
		$readonly = (isset($field['attributes']['readonly']) && $field['attributes']['readonly']) ?'readonly="readonly"':'';
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}			
		// join 
		$class = implode(' ',$classes);
		// default field
		$html  = sprintf('<input type="text" name="user_email" id="user_email2" class="%s" value="%s" %s/>',$class,$value,$readonly);
		// hide on default
		if($this->get_config('wordpres_form')){
			// hide default field
			$html.= '<script language="javascript">jQuery(document).ready(function(){jQuery("#user_email").parent().remove();});</script>';
		}
		// return
		return $html;
	}
	
	// user_login
	function field_user_email_callback($field,$name,$value){
		// return
		return $this->field_email_callback($field,$name,$value);
	}
	
	// password
	function field_password_callback($field,$name,$value){
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}			
		// join 
		$class = implode(' ',$classes);			
		// input html
		$html = sprintf('<input type="password" name="user_password" id="user_password" class="%s" value="%s" />',$class,$value);		
		// return
		return $html;
	}
	
	// password confirm
	function field_password_conf_callback($field,$name,$value){		
		// classes, default name
		$classes = array($name);
		// required
		if($field['attributes']['required']){
			$classes[] = 'required';
		}			
		// join 
		$class = implode(' ',$classes);				
		// html
		$html = sprintf('<input type="password" name="user_password_conf" id="user_password_conf" class="%s" value="%s" />',$class,$value);			
		// return 
		return $html;	
	}
	
	// user password
	function field_user_password_callback($field,$name,$value){
		// return
		return $this->field_password_callback($field,$name,$value);
	}
	
	// user_password_conf
	function field_user_password_conf_callback($field,$name,$value){
		// return
		return $this->field_password_conf_callback($field,$name,$value);
	}
	
	// terms_conditions
	function field_terms_conditions_callback($field,$name,$value){
		// copy subscription_introduction
		if(empty($field['value'])){
			$field['value'] = mgm_get_class('system')->get_template('tos', array(), true);
		}
		// checked
		$checked = (isset($_POST['mgm_tos']) && $_POST['mgm_tos'] == 1)?'checked="true"':'';
		// return
		$html  = $this->field_type_html($field,$name,$value);
		$html .= sprintf('<input type="checkbox" class="checkbox required" name="mgm_tos" id="mgm_tos" value="1" %s/> &nbsp; <label for="mgm_tos">%s</label>',$checked,__('I agree to the Terms and Conditions.','mgm'));		
		// return
		return $html;
	}
	
	// subscription_introduction
	function field_subscription_introduction_callback($field,$name,$value){
		// copy subscription_introduction
		if(empty($field['value'])){
			$field['value'] = mgm_get_class('system')->get_template('subs_intro', array(), true);
		}
		// return
		return $this->field_type_html($field,$name,$value);
	}
	
	// country
	function field_country_callback($field,$name,$value){
		// options
		$options = mgm_field_values(TBL_MGM_COUNTRY, 'code', 'name');
		// default
		if(empty($field['value'])) $field['value'] = 'US';
		if($field['attributes']['readonly'] ) $options = array($value => $options[ $value ]);
		// return
		return $this->field_type_select($field,$name,$value,$options,MGM_KEY_VALUE);		
	}
	
	// display_name
	function field_display_name_callback($field,$name,$value){
		// options
		$options = (isset($field['options']) && is_array($field['options'])) ? $field['options'] : mgm_get_user_display_names();		
		if($field['attributes']['readonly'] && in_array($value, $options) ) $options = array(array_search($value,$options) => $value);		
		// return
		return $this->field_type_select($field,$name,$value,$options,MGM_VALUE_ONLY);		
	}
	
	// user_url
	function field_user_url_callback($field,$name,$value){
		// fix name
		$field['name'] = 'url';
		// return
		return $this->field_type_input($field,$name,$value);
	}
	
	// subscription_options
	function field_subscription_options_callback($field,$name,$value) {				
		// get object
		$packs_obj = mgm_get_class('subscription_packs');	
		// get mgm_system
		$mgm_system = mgm_get_class('system');											
		// packs
		$packs = $packs_obj->get_packs('register');
														
		// active payment modules
		$a_payment_modules = $mgm_system->get_active_modules('payment');
		// active module
		if (count($a_payment_modules) == 0) {
			return  '<p>' . __('There are no payment gateways active. Please contact the administrator.','mgm') . '</p>';
		}else{		
			// only when hide_subscription is false
			// if((bool)$pack['hide_subscription']==true){
				// return __('Subscription is not active!','mgm');
			// }	
			
			// html
			$html = '';	
			// payment_module
			$payment_modules = array(); 
			// loop
			foreach($a_payment_modules as $payment_module){
				// skip free/trial				
				//issue#: 483
				//if(in_array($payment_module, array('mgm_free','mgm_trial','mgm_manualpay'))) continue;											
				if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;											
				// increment 
				$payment_modules[] = $payment_module;
			}								
			// args
			$args = $this->get_config('args', array());	
			
			// selected_subscription	
			$selected_subs = mgm_get_selected_subscription($args);
			
			// loop packs
			foreach ($packs as $pack) {					
				// reset
				$checked = mgm_select_subscription($pack,$selected_subs);						
				// skip other when a package sent as selected
				if($selected_subs !== false){
					if(empty($checked)) continue;
				}	
				
				// subs encrypted
				$subs_enc = mgm_encode_package($pack);
														
				// issue #338:(enable free gateway for cost=0)
				if ((strtolower($pack['membership_type']) == 'free' || ($pack['cost'] == 0 && mgm_get_module('mgm_free')->enabled=='Y')) && in_array('mgm_free', $a_payment_modules)) {
					// html										
					//NOTE: Do not change the mgm_subs_wrapper class. It is being used in payment_gateways Custom field
					//Refer to : function field_payment_gateways_callback()		
					$html.= '<div class="mgm_subs_wrapper">
								 <div class="mgm_subs_option">
									' . sprintf('<input type="radio" %s class="checkbox" name="mgm_subscription" value="%s" />', $checked, $subs_enc) . '														
								 </div>
								 <div class="mgm_subs_pack_desc">
									' . mgm_stripslashes_deep($packs_obj->get_pack_desc($pack)) . ' 
								 </div>
								 <div class="clearfix"></div>
								 <div class="mgm_subs_desc">
									' . mgm_stripslashes_deep($pack['description']) . '
								 </div>
							 </div>';
				// trial		  
				}elseif (strtolower($pack['membership_type']) == 'trial' && in_array('mgm_trial', $a_payment_modules)) {
					// html
					$html.= '<div class="mgm_subs_wrapper">
								 <div class="mgm_subs_option">
									' . sprintf('<input type="radio" %s class="checkbox" name="mgm_subscription" value="%s" />', $checked, $subs_enc) . '
								 </div>
								 <div class="mgm_subs_pack_desc">
									' . mgm_stripslashes_deep($packs_obj->get_pack_desc($pack)) . '
								 </div>
								 <div class="clearfix"></div>
								 <div class="mgm_subs_desc">
									' . mgm_stripslashes_deep($pack['description']) . '
								 </div>
							 </div>';
				}else{										
					// paid subscription active
					if(count($payment_modules)){
						// check cost and hide false
						if ($pack['cost']){
							$html.= '<div class="mgm_subs_wrapper">
										 <div class="mgm_subs_option">
											' . sprintf('<input type="radio" %s class="checkbox" name="mgm_subscription" value="%s" />', $checked, $subs_enc) . '
										 </div>
										 <div class="mgm_subs_pack_desc">
											' . mgm_stripslashes_deep($packs_obj->get_pack_desc($pack)) . '
										 </div>
										 <div class="clearfix"></div>
										 <div class="mgm_subs_desc">
											' . mgm_stripslashes_deep($pack['description']) . '
										 </div>
									 </div>';
						}// end if
					}else{
						// set message
						$html .= sprintf('<div class="message" style="margin:10px 0px; overflow: auto;color:red;font-weight:bold">%s</div>',__('Please enable a payment module to allow paid subscription.','mgm'));											
					}// end paid											
				} 	
			}// end pack loop	
		}	
		// return
		return $html;
	}
	/**
	 * Callback for payment_gateways field
	 *
	 * @param array $field
	 * @param string $name
	 * @param string $value
	 * @return string
	 */	
	function field_payment_gateways_callback($field,$name,$value) {
		
		//check subscription options custom_field is enabled:		
		$continue = false;
		$obj_customfields = mgm_get_class('member_custom_fields');		
		$arr_sub_options = $obj_customfields->get_field_by_name('subscription_options');
		
		if(isset($arr_sub_options['id']) && !empty($obj_customfields->sort_orders) && in_array($arr_sub_options['id'], $obj_customfields->sort_orders))
			$continue = true;
			
		if(!$continue)
			return '';
				
		$system = mgm_get_class('system');
		// args
		$args = $this->get_config('args', array());				
		// selected_subscription	
		$selected_subs = mgm_get_selected_subscription($args);		
		// get active modules
		$a_payment_modules = $system->get_active_modules('payment');
		//get modules for packs:
		$allpacks = mgm_get_class('subscription_packs')->get_packs();
		$pack_modules = array();		
		foreach ($allpacks as $allp) {
			// reset
			$include = mgm_select_subscription($allp,$selected_subs);						
			// skip other when a package sent as selected
			if($selected_subs !== false){
				if(empty($include)) continue;
			}
			$pack_modules = array_merge($pack_modules,$allp['modules']);
		}
		$pack_modules = array_unique($pack_modules);
		
		$payment_modules = array();			
		
		if($a_payment_modules) {
			// loop
			foreach($a_payment_modules as $payment_module){
				// not trial
				if(in_array($payment_module, array('mgm_free','mgm_trial'))) continue;
				// modules
				if(!in_array($payment_module, $pack_modules)) continue;
				// store
				$payment_modules[] = $payment_module;					
			}
		}
		//$payment_modules = array($payment_modules[0]);
		$module_count = count($payment_modules);
		$hide_div = ($module_count === 1) ? 'style="display:none;"' : ''; 
		//NOTE: uncomment the below line to enable module display even if only one exists
		//$hide_div = "";		
		//NOTE: do not change the id: mgm_payment_gateways_container
		$html = sprintf('<div id="mgm_payment_gateways_container" class="mgm_payment_wrapper" %s >', $hide_div);
		//loop through payment modules:				
		if( $module_count == 0 ) {
			$html .= '<div>' . __('No active payment module', 'mgm') . '</div>';
		}else {			
			//print each module:
			foreach($payment_modules as $payment_module) {
				//checked: if(selected/only one gateway exists)
				$checked = ((!empty($value) && $value == $field['value']) || $module_count === 1 )?'checked="true"':'';
				// get obj
				$mod_obj = mgm_get_module($payment_module, 'payment');							
				// html
				//NOTE: //NOTE: do not change the id: %s_container
				$img_url = mgm_ssl_url($mod_obj->logo);
				//set module logo image h/w
				//default:in px
				$img_width = 70;
				$img_height = 60;
				$arr_img = @getimagesize($img_url);
				if(!empty($arr_img)) {
					//get higher dimension
					if($arr_img[0] > $arr_img[1] ) {
						$img_height = 0;
						$img_width = ($arr_img[0] >= $img_width) ? $img_width: $arr_img[0]; 
					}else {
						$img_width = 0;
						$img_height = ($arr_img[1] >= $img_height) ? $img_height: $arr_img[1]; 
					}
				}else 
					$img_width = 0;
				//module description
				$html .= (sprintf('<div id="%s_container" class="mgm_payment_opt_wrapper" %s>', $mod_obj->code ,$hide_div)).
						  (sprintf('<input type="radio" %s class="checkbox" name="mgm_payment_gateways" value="%s" alt="%s" />', $checked, $mod_obj->code, $mod_obj->name)) .
						  
						  (sprintf('<img style="margin:10px 0px 0px 15px;" src="%s" alt="%s" %s %s />',  $img_url, $mod_obj->name, ($img_width > 0 ? 'width="'.$img_width.'px"' : ''), ($img_height > 0 ? 'height="'.$img_height.'px"' : '') )) .
						  (sprintf('<div class="mgm_paymod_description">%s</div>', mgm_stripslashes_deep($mod_obj->description))) .
						  '</div>';
						  
			}
			
			//scripts required for pack buttons:
			$packs_obj = mgm_get_class('subscription_packs');															
			// packs
			$packs = $packs_obj->get_packs('register');			
			//script to show/hide appicable module buttons
			$html .= '<script type="text/javascript">'."\n";
			$html .= 'jQuery(document).ready(function() {'."\n".
						//gateways will be an array of enabled modules
						'var mgm_update_payment_gateways=function(gateways) {'."\n".							
							//get module radios
							'var obj_radio = jQuery("#mgm_payment_gateways_container input[type=\'radio\']");'."\n".							
							'if(gateways.length == 0) {'."\n".
								//hide container								
								'obj_radio.each( function(){ jQuery("#"+this.value+"_container").fadeOut(); } );'."\n".
								'jQuery("#mgm_payment_gateways_container").fadeOut();'."\n".
							'}else {'."\n".
								//hide container just for animation
								'jQuery("#mgm_payment_gateways_container").fadeOut();'."\n".
								//loop through modules to show/hide appicable modules
								' obj_radio.each( function(){'."\n".
									'var modulecode = this.value;'."\n". 
									'var found = false;'."\n".								
									//'jQuery("#"+modulecode+"_container").hide();'."\n".									
									'jQuery.each(gateways,function(i,n){ if(modulecode == n){ found = true;} });'."\n".																		
									'//show/hide each module'."\n".
									'if(found) {'."\n".
										'jQuery("#"+modulecode+"_container").fadeIn();'."\n".
									'}else{'."\n".
										'jQuery("#"+modulecode+"_container").fadeOut();'."\n".
									'}'."\n".
								'} ); '."\n";
								//show container
								//NOTE: comment the below condition to enable module display even if only one exists
								if($module_count != 1)
									$html .= 'jQuery("#mgm_payment_gateways_container").fadeIn();'."\n";
					$html .='}';
							//unset previous selection
							//if only one module exists, uncheck obly for free/trial
							if($module_count === 1){
								$html .= 'obj_radio.each( function(){ this.checked = (gateways.length == 0 ? false : true); } );'."\n";
							}else {
								//if multiple module exists, uncheck all							
								$html .= 'obj_radio.each( function(){ this.checked = false; } );'."\n";
							}
					$html .='}'."\n"						
						;	
			//bind the above function to click event of pack radios		
			foreach ($packs as $pack) {
				$subs_enc = mgm_encode_package($pack);			
				$arr_modules = array_diff($pack['modules'], array('mgm_free', 'mgm_trial'));
				$html .= 'jQuery(".mgm_subs_wrapper input[value=\''.$subs_enc.'\']").bind("click",function(){mgm_update_payment_gateways('.(!empty($arr_modules) ? '[\''.implode('\',\'', $arr_modules).'\']' : '[]').');});'."\n";					
			}
			
			$html .= '});'."\n";
			$html .= '</script>';			
		}
		$html .= '</div>';
		return $html;
		
	}
	// by name end/////////////////////////////////////////////////////////////////////////////////////////
	
	// internal ///////////////////////////////////////////////////////////////////////////////////////////
	// filter_value
	function _filter_value($field,$name,$value){
		// isset post						
		if(isset($_POST[$name][$field['name']])){
			return mgm_stripslashes_deep($_POST[$name][$field['name']]);
		}		
		// value
		if(!empty($value)){
			return $value;
		}					
		// return
		return mgm_stripslashes_deep($field['value']);	
	}
	
	// element name
	function _get_element_name($field,$name){
		// return
		return sprintf('%s[%s]',$name,$field['name']);	
	}
	
	// element id
	function _get_element_id($field,$name){
		// return
		return sprintf('%s_%s',$name,$field['name']);	
	}
}
// core/libs/utilities/mgm_form_fields.php