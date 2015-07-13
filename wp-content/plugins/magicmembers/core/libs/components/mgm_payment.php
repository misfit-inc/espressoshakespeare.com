<?php
/**
 * Magic Members payment modules parent class
 *
 * @package MagicMembers
 * @since 2.5
 */
class mgm_payment extends mgm_component{
	// type
	var $type              = 'payment';
	// type
	var $button_type       = 'offsite'; // onsite / offsite
	// status
	var $status            = 'test';// test/live
	// name
	var $name              = 'Magic Members Payment Module';
	// internal name
	var $code              = 'mgm_payment';
	// dir
	var $module            = 'payment';	
	// settings tab
	var $settings_tab      = true;
	// description
	var $description       = '';
	// logo
	var $logo              = 'payment/html/logo.jpg';
	// enabled/disabled : Y/N
	var $enabled           = 'N';	
	// supported buttons types, array('subscription', 'buypost')
	var $supported_buttons = array('subscription', 'buypost');
	// can setup trial mode: Y/N, for Paypal, Authorize.net, Paypal Pro 
	var $supports_trial    = 'N';
	// cancellation support via api/post: Y/N, for Paypal, Authorize.net, Paypal Pro, AlertPay, 2Checkout 
	var $supports_cancellation = 'N';	
	// requires_product_mapping, to differenciate between modules where external product mapping is required, 
	// i.e. clickbank, 2checkout, and not required i.e. paypal, authorize.net
	var $requires_product_mapping = 'N'; 	
	// type of integration,
	// Y => offsite, html redirect, payment will be done on Gateway hosted site, 
	// N=> onsite, payment will be on site itself with credit card
	var $hosted_payment    = 'Y';
	// api end points
	var $end_points        = array();
	// settings
	var $setting           = array();
			
	// construct
	function __construct(){
		// php4 construct
		$this->mgm_payment();
	}
	
	// php4 construct
	function mgm_payment(){		
		// call parent
		parent::__construct();		
		// set code
		$this->code = __CLASS__; 		
		// desc
		$this->description = __('Payment module description', 'mgm');
		// supported buttons types
	 	$this->supported_buttons = array('subscription', 'buypost');
		// default settings
		$this->_default_setting();
	}
	
	// set template
	function set_tmpl_path($basedir=MGM_MODULE_DIR, $prefix='mgm_'){		
		// dir/module		
		$this->module = str_replace($prefix, '', $this->code);				
		// set path
		$tmpl_path = ($basedir . implode(DIRECTORY_SEPARATOR, array($this->type, $this->module, 'html')) . DIRECTORY_SEPARATOR);		
		// set		
		$this->load->set_tmpl_path($tmpl_path);
	}
		
	// enable only
	function enable($activate=false){
		// activate
		if($activate) mgm_get_class('system')->activate_module($this->code,$this->type);
		// update state
		$this->enabled = 'Y'; 		
		// reset urls
		$this->_reset_callback_urls();					
		// update option
		// update_option($this->code, $this);
		$this->save();	
	}
	
	// disable only
	function disable($deactivate=false){
		// deactivate
		if($deactivate) mgm_get_class('system')->deactivate_module($this->code,$this->type);
		// update state
		$this->enabled = 'N'; 
		// reset urls
		$this->_reset_callback_urls();
		// update options
		// update_option($this->code, $this);		
		$this->save();
		
	}
	
	// install hook
	function install(){				
		// enable
		$this->enable(true);
	}
	
	// uninstall hook
	function uninstall(){						
		// disable
		$this->disable(true);			
	}
	
	// enabled check
	function is_enabled(){
		// return true|false on enabled
		return ($this->enabled == 'Y') ? true : false;
	}
		
	// invoke hook
	function invoke($method, $args=false){
		// check
		if(method_exists($this,$method)){
			return $this->$method($args);
		}else{
			die(__(sprintf('No such method : %s',$method),'mgm'));
		}
	} 
	
	// return process, link back to site after payment is made 
	function process_return(){
		// overwrite this
		// return '';
	}
	
	// notify process, IPN/Background Notify url for silent POST after payment is made 
	function process_notify(){
		// overwrite this
		// return '';
	}
	
	// cancel process, as it says stupid
	function process_cancel(){
		// overwrite this
		// return '';
	}
	
	// unsubscribe process, IPN for unsubscribe 
	function process_unsubscribe(){
		// overwrite this
		// return '';
	}
	
	// html redirect, proxy for html redirect
	function process_html_redirect(){
		// overwrite this
		// return '';
	}
	
	// process credit card, proxy for credit card gateway
	function process_credit_card(){
		// overwrite this
		// return '';
	}
	
	// settings hook
	function settings(){
		// overwrite this
		// return '';
	}	
	
	// settings_box hook
	function settings_box(){
		// overwrite this
		// return '';
	}	
	
	// hook for post purchase setting
	function settings_post_purchase($data=false){
		// overwrite this
		// return '';
	}
	
	// hook for post pack purchase setting
	function settings_postpack_purchase($data=false){
		// overwrite this
		// return '';
	}
	
	// hook for subscription package setting
	function settings_subscription_package($data=false){
		// overwrite this
		// return '';
	}
	
	// settings update
	function settings_update(){
		// form type 
		switch($_POST['setting_form']){
			case 'box':
			// from box	
			break;
			case 'main':
			// form main
			break;
		}
	}	
	
	// subscribe button hook
	function get_button_subscribe($options=array()){
		// empty
	}
	
	// buypost button hook
	function get_button_buypost($cost, $title, $return = false) {
		// empty
	}
	
	// unsubscribe button hook
	function get_button_unsubscribe($options=array()){
		// empty
		$html='<div style="margin-bottom: 10px;">
					<h4>'.__('Unsubscribe','mgm').'</h4>
					<div style="margin-bottom: 10px;">'.
						sprintf(__('If you wish to unsubscribe from %s, please click the following link. You have to manually unsubscribe from any payment gateway you used while signup.','mgm'),get_option('blogname')).
					'</div>
				</div>
				<form name="mgm_unsubscribe_form" id="mgm_unsubscribe_form" method="post" action="'. add_query_arg(array('method'=>'payment_unsubscribe'), mgm_home_url('payments')).' ">
					<input type="hidden" name="user_id" value="' . $options['user_id'] . '"/>
					<input type="button" value="'.__('Unsubscribe','mgm').'" onclick="confirm_unsubscribe()" class="button" />
				</form>';
		// return
		return $html;		
	}
	
	// buttons hook
	function get_buttons($options=array()){
		// empty
	}
	
	// links
	function get_activation_links(){
		// empty
	}
	
	// dependency_check
	function dependency_check(){
		// its ok
		return false;
	}	
	
	// internal private methods //////////////////////////////////////////
	
	// default settings
	function _default_setting(){
		// overwrite this
		// return '';
	}
	
	// get endpoint
	function _get_endpoint($type=false, $include_permalink = true){
		// status
		$type = ($type===false) ? $this->status : $type;
		// type/status
		switch($type){
			case 'test':
				// is test availble
				if($this->end_points['test'])
					return $this->end_points['test'];
				else
				// send live
					return $this->end_points['live'];	
			break;
			case 'live':
				return $this->end_points['live']; // live
			break;
			case 'credit_card': // credit_card process proxy
			case 'html_redirect': // html_redirect proxy
			case 'cancel': // transaction cancel
			case 'return':// manualpay proxy
				// if on wordpress page or custompage	
				$post_id = get_the_ID();
				// in post
				if($post_id && $include_permalink){		
					// payments url
					$payments_url = get_permalink($post_id);								
				}else if($transactions_url = $this->_get_transactions_url()){
					// payments url
					$payments_url = $transactions_url;	
				}else{
					// payments url
					$payments_url = mgm_home_url('payments');
				}
				
				// return
				return add_query_arg(array('module'=>$this->code, 'method'=>'payment_'.$type), $payments_url);
			break;
			default:
				if(isset($this->end_points[$type])) return $this->end_points[$type];		
			break;
		}	
		
		// defauult
		return 'endpoint type not defined';
	}
	
	// set endpoint
	function _set_endpoint($status, $endpoint){
		// status
		$this->end_points[$status] = $endpoint;	
	}
	
	// set endpoints
	function _set_endpoints($endpoints){
		// loop
		foreach($endpoints as $status => $endpoint){
			$this->end_points[$status] = $endpoint;	
		}
	}
	
	// validate membership type, type = md5|plain|both
	function _validate_membership_type($membership_type, $type='md5') {
		// packs
		$packs = mgm_get_class('subscription_packs');
		// loop
		foreach ($packs as $i=>$pack) {
			foreach ($pack as $j=>$apack) {
				$raw_mt = $apack['membership_type'];
				
				if (preg_match('/md5/i',$type)) {
					$apack['membership_type_md5'] = md5($apack['membership_type']);
					
					if (strtolower($apack['membership_type_md5']) == strtolower($membership_type) ) {
						$match = $raw_mt;
						break;
					}
				}
				
				if (preg_match('/plain/i',$type)) {
					if (strtolower($apack['membership_type']) == strtolower($membership_type) ) {
						$match = $raw_mt;
						break;
					}
				}
			}
			// match
			if ($match) {
				break;
			}
		}
		// return
		return $match;
	}
	
	// _get_user
	function _get_user($user_id){
		// get user
		$user = mgm_get_userdata((int) $user_id);						
		// check null
		if(is_null($user)){		
			_e('User cannot be found.','mgm');
			exit;
		}
		// send user
		return $user;
	}
	
	// redirect
	function _redirect($arg=false){
		// add arg	
		if(is_array($arg))
			$redirect = add_query_arg(array('status'=>'success'), $this->setting['processed_url']);
		else
			$redirect = $this->setting['processed_url'];	
		
		// redirect			
		mgm_redirect($redirect);	
	}
	
	// transactions ///////////////////////////////////////////////////
	// create transaction
	function _create_transaction($pack,$options = null){
		// global
		global $wpdb;
		// init
		$columns = array();
		// set
		$columns['payment_type'] = (isset($pack['buypost']))? 'post_purchase' : 'subscription_purchase';	
		// tran data
		$tran_data = array();
		// user
		//IMPORTANT: user_id has to be passed alogn with pack details, otherwise logged in user id 
		$tran_data['user_id'] = isset($options['user_id']) ? $options['user_id'] : mgm_get_user_id();		
		// set system currency, will update at module level after module selection
		$tran_data['currency'] = mgm_get_class('system')->setting['currency'];
		// ip
		$tran_data['client_ip'] = $_SERVER['REMOTE_ADDR'];		
		//if registration
		$tran_data['is_registration'] = (isset($options['is_registration']))? 'Y' : 'N';
		//if another subscription purchase
		$tran_data['is_another_membership_purchase'] = (isset($options['is_another_membership_purchase']))? 'Y' : 'N';
		// merge with pack
		$tran_data = array_merge($pack,$tran_data);
		// set
		$columns['data'] = json_encode($tran_data);
		// date
		$columns['transaction_dt'] = date('Y-m-d H:i:s');
		// insert
		$wpdb->insert(TBL_MGM_TRANSACTION, $columns);
		// return 
		return $wpdb->insert_id;	
	}
	
	// update transaction
	function _update_transaction($columns, $transaction_id){
		// global
		global $wpdb;
		// check
		if((int)$transaction_id>0){
			// update
			return $wpdb->update(TBL_MGM_TRANSACTION, $columns, array('id'=>(int)$transaction_id));	
		}		
		// error
		return false;		
	}
	
	// update transaction status
	function _update_transaction_status($transaction_id, $status, $status_text){
		// global
		global $wpdb;
		// set columns
		$columns = array('status'=>$status,'status_text'=>$status_text);
		// update
		return $wpdb->update(TBL_MGM_TRANSACTION, $columns, array('id'=>(int)$transaction_id));					
	}
	
	// get transaction data
	function _get_transaction($transaction_id){
		// global
		global $wpdb;	
		// check
		if((int)$transaction_id>0){
			// sql
			$sql = "SELECT * FROM ".TBL_MGM_TRANSACTION." WHERE id='{$transaction_id}'";		
			// row
			$row  = $wpdb->get_row($sql,ARRAY_A);		
			// reset data
			$row['data'] = json_decode($row['data'],true);
			// return
			return $row;
		}
		// error
		return false;
	}
	
	// get transaction type
	function _get_transaction_type($transaction_id){
		// global
		global $wpdb;
		// check
		if((int)$transaction_id>0){
			// transaction
			$payment_type = $wpdb->get_var("SELECT `payment_type` FROM ".TBL_MGM_TRANSACTION." WHERE id='{$transaction_id}'");
			// switch for old format
			if($payment_type == 'post_purchase'){
				return 'buypost';
			}else if($payment_type == 'subscription_purchase'){
				return 'subscription';	
			}else{
				return 'other';
			}		
		}
		
		// error
		return 'other';					
	}
	
	// add transaction option
	function _add_transaction_option($columns){
		// global
		global $wpdb;
		// insert
		$wpdb->insert(TBL_MGM_TRANSACTION_OPTION, $columns);
		// return 
		return $wpdb->insert_id;	
	}
	
	// get transaction by option
	function _get_transaction_by_option($option_name,$option_value){
		// global
		global $wpdb;
		// sql
		$sql = "SELECT `transaction_id` FROM `".TBL_MGM_TRANSACTION_OPTION."` WHERE `option_name` ='{$option_name}' AND `option_value`='{$option_value}'";		
		// insert
		$transaction_id = $wpdb->get_var($sql);
		// return 
		if(isset($transaction_id)){
			return $this->_get_transaction($transaction_id);
		}
		// error
		return false;	
	}
	
	// check if new transaction
	function _is_transaction($passthrough){
		// we are using transaction id as custom var
		return ((int)$passthrough > 0 && preg_match('/^(buypost|subscription)_/', $passthrough) == false);
	}
	
	// get custom passthrough
	function _get_transaction_passthrough($passthrough, $verify=true){
		// int
		$custom = false;
		// buy post
		if(is_string($passthrough) && preg_match('/^buypost_/', $passthrough)){
			// unset
			unset($custom);
			// init
			$custom = array('payment_type'=>'post_purchase');
			// split
			list($custom['duration'], $custom['cost'], $custom['currency'], $custom['user_id'], $custom['post_id'], $custom['user_id'], $custom['client_ip']) = explode('_', preg_replace('/^buypost_/', '', $passthrough));
		}else if(is_string($passthrough) && preg_match('/^subscription_/', $passthrough)){	
		// subscription	    
			// unset
			unset($custom);
			// init
			$custom = array('payment_type'=>'subscription_purchase');
			// split
			list($custom['duration'], $custom['amount'], $custom['currency'], $custom['user_id'], $custom['membership_type'], $custom['duration_type'], $custom['role'], $custom['client_ip'], $custom['hide_old_content'], $custom['pack_id']) = explode('_', preg_replace('/^subscription_/', '', $passthrough));
		}else{
		// new 
			if($this->_is_transaction($passthrough)){
				// fetch
				$transaction = $this->_get_transaction($passthrough);
				// check
				if(isset($transaction['id'])){
					// unset
					unset($custom);
					// set
					$custom = array_merge(array('payment_type'=>$transaction['payment_type']),$transaction['data']);
					// rename some fieldsfor backward compatibility
					$custom['amount']  = $custom['cost'];
					$custom['pack_id'] = $custom['id'];
				}
			}
		}		
		
		// verify
		if($verify){
			// if not parsed, treat as error
			if($custom === false){
				// system
				$system = mgm_get_class('system');
				$dge  = ($system->setting['disable_gateway_emails'] == 'Y') ? true : false;
				// message
				$message = 'Could not read custom passthrough:' . "<br /><br />" . $passthrough;
				// notify admin, only if gateway emails on
				if(!$dge){				
					// mail
					mgm_mail($system->setting['admin_mail'], 'Error in '.(ucwords($this->module)).' custom passthrough verification', $message);
				}else{
					// log
					mgm_log($message);					
				}
				// abort
				exit();
			}
		}
			
		// return
		return $custom;			
	}
	
	// verify payment data
	function _verify_transaction($passthrough){
		// overridden in module
		return true;		
	}
	
	// log transaction
	function _log_transaction(){
		// overridden in module
		return true;
	}
	
	// set payment type : used as wrapper for backward compatibility
	function _set_payment_type($pack, $currency=''){
		// discarded			
		// encript membership_type		
		$membership_type = md5($pack['membership_type']);
		// user
		$user_id         = mgm_get_user_id();
		// currency
		if($currency==''){
			$currency = mgm_get_class('system')->setting['currency'];
		}
		// custom string
		if(isset($pack['buypost'])){
			// get_the_ID()
			$payment_type = 'buypost_' . $pack['duration'] .'_'. $pack['cost'] .'_'. $currency .'_'. $user_id .'_' . $pack['post_id'] . '_' . $_SERVER['REMOTE_ADDR'] ;
		}else{		
			$payment_type = 'subscription_' . $pack['duration'] .'_'. $pack['cost'] .'_'. $currency .'_'. $user_id .'_'. $membership_type . '_'. strtoupper($pack['duration_type']) . '_' . $pack['role'] . '_' . $_SERVER['REMOTE_ADDR'] . '_' . (int)$pack['hide_old_content']. '_' . (int)$pack['id'];
		}
		
		// return
		return $payment_type;
	}
	
	// get payment_type : used as wrapper for backward compatibility
	function _get_payment_type($passthrough){
		// buy post : backward compatibility
		if(is_string($passthrough) && preg_match('/^buypost_/', $passthrough)){
			return 'buypost';
		}else if(is_string($passthrough) && preg_match('/^subscription_/', $passthrough)){
		// subscription : backward compatibility
		    return 'subscription';	
		}else{
			// new
			if($this->_is_transaction($passthrough)){
				// type
				$transaction_type = $this->_get_transaction_type($passthrough);
				// check
				if(isset($transaction_type)){
					// set
					return $transaction_type;
				}
			}			
		}		
		// return other
		return 'other';	 
	}
	
	// create_order: discarded
	function _create_order($pack,$user_id){	
		// check		
		if ($pack['buypost'] == 1 ) {
			if (isset($pack['ppp_pack_id'])) {		
				$post_id = $pack['ppp_pack_id'];
			} else {
				$post_id = get_the_ID();	
			}		
			return 	$user_id . $post_id;
		} else{
			return $user_id;
		}
	}	
	
	// get post id
	function _get_post_redirect($passthrough){
		// get custom
		$custom = $this->_get_transaction_passthrough($passthrough, false);
		// check
		if (isset($custom['payment_type']) && $custom['payment_type'] == 'post_purchase') {
			// extract
			extract($custom);
			// check if postpack_post_id
			if(isset($postpack_post_id) && (int)$postpack_post_id>0){
				return get_permalink($postpack_post_id);
			}else if (strpos($post_id, ',') !== false) {
				// is pack, get first
				$posts = explode(',', $post_id); unset($post_id);
				// get first
				$post_id = array_shift($posts);
			} 			
			// return
			return get_permalink($post_id);
		}
		// nothing
		return false;	
	}
	
	// callback messages
	function _setup_callback_messages($setting=array(),$use_global_message=false){
		// system
		$mgm_system = mgm_get_class('system');		
		// keys
		$msg_keys = array('success_title','success_message','failed_title','failed_message');
		// take global message/ TODO, update settings page
		if(mgm_post_var('use_global_message','N') == 'Y' || $use_global_message==true){
			// loop
			foreach($msg_keys as $msg_key){
				// set
				$this->setting[$msg_key] = $mgm_system->get_template('payment_'.$msg_key);// the raw format, without urls parsed
			}	
		}else{
		// messages from post
			// loop
			foreach($msg_keys as $msg_key){
				// set
				$this->setting[$msg_key] = (isset($setting[$msg_key]) && !empty($setting[$msg_key])) ? $setting[$msg_key] :  $mgm_system->get_template('payment_'.$msg_key, array(), true);
			}			
		}	
	}
	
	// callback urls 
	function _setup_callback_urls($setting=array()){		
		// urls keys
		$url_keys = array('notify_url'    => 'payment_notify', // payment notify/ipn etc. background	
						  'return_url'    => 'payment_return',// payment return, link back
						  'cancel_url'    => 'payment_cancel',// payment cancel, link back	
						  'processed_url' => 'payment_processed',// payment processed, thank you,cancel, failure urls
						  'thankyou_url'  => 'payment_processed');// customizable thankyou url
		// loop
		foreach($url_keys as $url_key=>$callback){			
			// set in POST
			if(isset($setting[$url_key]) && !empty($setting[$url_key])){
				// set
				$this->setting[$url_key] = $setting[$url_key];
			}else{
				// on key
				switch($url_key){
					case 'notify_url' :
					case 'return_url' :
						$payments_baseurl = mgm_home_url('payments');
					break;
					default;
						// first check module thankyou
						if($transactions_url = $this->_get_transactions_url()){
							// thankyou_url url
							$payments_baseurl = $transactions_url;			
						}else{
							$payments_baseurl = mgm_home_url('payments');
						}
					break;
				}
				// set
				$this->setting[$url_key] = add_query_arg(array('module'=>$this->code,'method'=>$callback), $payments_baseurl);
			}
		}	
	}	
	
	// thankyou url
	function _get_thankyou_url($query_arg=true){		
		// first check module thankyou
		if(isset($this->setting['thankyou_url']) && !empty($this->setting['thankyou_url'])){
			// thankyou_url
			$thankyou_url = $this->setting['thankyou_url'];		
		// next check system transactions url				
		}else if($transactions_url = $this->_get_transactions_url()){
			// transactions_url
			$thankyou_url = $transactions_url;					
		// default processed url
		}else{		
			// processed_url
			$thankyou_url = $this->setting['processed_url'];
		}
		// return
		return (!$query_arg) ? $thankyou_url : add_query_arg(array('module'=>$this->code,'method'=>'payment_processed'), $thankyou_url);
	}
	
	// reset urls
	function _reset_callback_urls(){				
		// reset
		$this->_setup_callback_urls();
	}
	
	// get transactions urls
	function _get_transactions_url(){
		// system
		$mgm_system = mgm_get_class('system');	
		// first check module thankyou
		if(isset($mgm_system->setting['transactions_url']) && !empty($mgm_system->setting['transactions_url'])){
			return $mgm_system->setting['transactions_url'];			
		}		
		// none
		return '';
	}
	
	// cc fields
	function _get_ccfields($user=NULL, $html_type='div'){
		// name
		$name='';
		// if user
		if($user){
			$name = ($user->first_name) ? mgm_str_concat($user->first_name, $user->last_name) : $user->display_name;
		}
		$cancel_url =  $this->_get_endpoint('cancel');		
		// type
		switch($html_type){
			case 'table':
				$html= "<table border='0' cellpadding='1' cellspacing='0' width='100%'>
							<tr>
								<td valign='top'>
									<label>Credit Card Number *</label>
								</td>
								<td>
									<input autocomplete='off' type='text' value='' name='mgm_card_number' id='mgm_card_number' size='40' maxlength='16' class='input {required: true, minlength:13, maxlength:16, digits:true}'/>
								</td>
							</tr>
							<tr>
								<td valign='top'>
									<label>Credit Card Expiry *</label>
								</td>
								<td valign='top'>	
									<input type='text' size='2' value='' name='mgm_expiry[month]' id='mgm_expiry_month' maxlength='2' class='input {required: true, minlength:2, maxlength:2, digits:true}'/>			
									&nbsp;/&nbsp;
									<input type='text' size='4' value='' name='mgm_expiry[year]' id='mgm_expiry_year' maxlength='4' class='input  {required: true, minlength:4, maxlength:4, digits:true}'/>
								</td>
							</tr>			
							<tr>
								<td valign='top'>
									<label>CVV *</label>
								</td>							
								<td valign='top'>	
									<input autocomplete='off' type='text' size='4' value='' name='mgm_card_code' id='mgm_card_code' maxlength='4' class='input {required: true, minlength:3, maxlength:4, digits:true}'/>
								</td>
							</tr>
							<tr>
								<td valign='top'>	
									<label>Card Type *</label>
								</td>
								<td valign='top'>		
									<select name='mgm_cctype' id='mgm_cctype' class='select'>
										<option value='Visa'>Visa</option>
										<option value='Mastercard'>MasterCard</option>
										<option value='Discover'>Discover</option>
										<option value='Amex'>Amex</option>
									</select>
								</td>	
							</tr>
							<tr>
								<td></td>
								<td valign='top'>	
									<input type='submit' class='button' value='Submit' onClick='mgm_submit_cc_payment(\"".$this->code."\")'>
									<input type='button' class='button' value='Cancel' onClick='mgm_cancel_cc_payment(\"".$cancel_url."\")'>
								</td>
							</tr>
						</table>";
			break;
			case 'div':
			default:
				$html= "<p>
							<label for='mgm_card_number'>".__('Card Holder Name','mgm')." <span class='required'>*</span></label><br />
							<input autocomplete='off' type='text' value='".$name."' name='mgm_card_holder_name' id='mgm_card_holder_name' size='40' maxlength='150' class='input {required: true, minlength:5, maxlength:150}' />
						</p>
						<p>
							<label for='mgm_card_number'>".__('Credit Card Number','mgm')." <span class='required'>*</span></label><br />
							<input autocomplete='off' type='text' value='' name='mgm_card_number' id='mgm_card_number' size='40' maxlength='16' class='input {required: true, minlength:13, maxlength:16, digits:true}' />
						</p>	
						<p>
							<label for='mgm_expiry'>".__('Credit Card Expiry','mgm')." <span class='required'>*</span></label><br />							
							<select name='mgm_expiry[month]' id='mgm_expiry_month' class='select' style='width:70px'>
								".mgm_make_combo_options(array('01','02','03','04','05','06','07','08','09','10','11','12'), date('m'), MGM_VALUE_ONLY)."
							</select>
							<select name='mgm_expiry[year]' id='mgm_expiry_year' class='select' style='width:100px'>
								".mgm_make_combo_options(range(date('Y')-1, date('Y')+10), date('Y'), MGM_VALUE_ONLY)."
							</select>
						</p>		
						<p>
							<label for='mgm_card_code'>".__('CVV','mgm')." <span class='required'>*</span></label><br />
							<input autocomplete='off' type='text' size='4' value='' name='mgm_card_code' id='mgm_card_code' maxlength='4' class='input {required: true, minlength:3, maxlength:4, digits:true}'/>
						</p>
						<p>
							<label for='mgm_cctype'>".__('Card Type','mgm')." <span class='required'>*</span></label><br />
							<select name='mgm_cctype' id='mgm_cctype' class='select' style='width:250px'>
								<option value='Visa'>".__('Visa','mgm')."</option>
								<option value='Mastercard'>".__('MasterCard','mgm')."</option>
								<option value='Discover'>".__('Discover','mgm')."</option>
								<option value='Amex'>".__('Amex','mgm')."</option>
							</select>
						</p>
						<p>
							<input type='submit' class='button' value='".__('Submit','mgm')."' onClick='mgm_submit_cc_payment(\"".$this->code."\")'>
							<input type='button' class='button' value='".__('Cancel','mgm')."' onClick='mgm_cancel_cc_payment(\"".$cancel_url."\")'>
						</p>";
			break;
		}
		// cc form
		$cc_form = "<div id='" . $this->code . "_form_cc' class='ccfields' style='display:block; text-align:left'>{$html}</div>";
		// filter
		$cc_form = apply_filters('mgm_cc_form_html',$cc_form,$this->code);
		// return
		return $cc_form;		
	}
	
	// get address fileds
	function _get_address_fields($user){
		// member
		$mgm_member = mgm_get_member($user->ID);
		// userfields
		$uf_on_paymentpage = mgm_get_class('member_custom_fields')->get_fields_where(array('display'=>array('on_payment'=>true)));		
		// address_fields
		$address_fields = array();
		// found some
		if($uf_on_paymentpage){
			// loop
			foreach($uf_on_paymentpage as $uf){				
				// value
				if($uf_value = $mgm_member->custom_fields->$uf['name']){
					$address_fields[$uf['name']] = $uf_value;
				}
			}
		}	
		// return 
		return $address_fields;		
	}
	
	// set_address_fields
	function _set_address_fields($user, &$data, $mapping, $callback=NULL){	
		// get address_fields
		$address_fields = $this->_get_address_fields($user);
				
		// loopcustom fields map
		foreach($mapping as $custom_field=>$payment_field){
			// string 
			if(is_string($payment_field) ){
				// set
				if(isset($address_fields[$custom_field])){
					// filter
					$value = ($callback) ? call_user_func_array($callback, array($custom_field, $address_fields[$custom_field])) : $this->_address_field_filter($custom_field,$address_fields[$custom_field]);
					// set
					$data[$payment_field] = $value;
				}
			}else if(is_array($payment_field)){
				// array for address
				$uf_values = explode("\n", $address_fields[$custom_field]);				
				// loop
				foreach($payment_field as $pf){
					// set
					if($uf_value = array_shift($uf_values)){
						$data[$pf] = substr($uf_value, 0, 64);// take 64 chars only per line
					}	
				}
			}	
		}
		
		// concat name
		if(isset($mapping['full_name'])){		
			// value
			$value = ($user->first_name) ? mgm_str_concat($user->first_name,$user->last_name): $user->display_name;;
			// filter			
			$value = ($callback) ? call_user_func_array($callback, array('full_name', $value)) : $this->_address_field_filter('full_name', $value);		
			// set
			$data[$mapping['full_name']] = $value;		
		}
	}
	
	// filter	
	function _address_field_filter($name, $value){
		// trim space
		$value = trim($value);
		// apply filter
		switch($name){
			case 'full_name':
				// trim chars
				$value = substr($value, 0, 40);
			break;
			case 'address':								
				// trim chars
				$value = substr($value, 0, 255);
			break;
			case 'zip':
				// trim chars
				$value = substr($value, 0, 12);
			break;
			case 'phone':
				// trim chars
				$value = substr($value, 0, 20);
			break;
			case 'first_name':
				// trim chars
				$value = substr($value, 0, 40);
			break;
			case 'last_name':
				// trim chars
				$value = substr($value, 0, 40);
			break;
			case 'city':
				// trim chars
				$value = substr($value, 0, 40);
			break;
			case 'state':
				// trim chars
				$value = substr($value, 0, 2);
			break;
			default:
				// trim chars
				$value = substr($value, 0, 50);
			break;	
		}
		// return
		return $value;
	}	
	
	// set purchased
	function _set_purchased($user_id,$post_id){
		global $wpdb;
		//if we are looking at a pack then explode the buy post item number and loop through it
		if (strpos($post_id, ',') !== false) {
			$posts = explode(',', $post_id);
		} else {
			$posts = array($post_id);
		}
		
		// create unique
		$posts = array_unique($posts);
		
		// insert
		foreach ($posts as $post_id) {
			$sql = "REPLACE INTO `" . TBL_MGM_POSTS_PURCHASED . "` 
			        (user_id, post_id, purchase_dt) VALUES ('{$user_id}', '{$post_id}', NOW())";
			$wpdb->query($sql); //insert the post purchased record
		}
	}
			
	// for serialize
	function __toString(){
		return serialize($this);
	}	
	
	// auto login after subscribed
	// @param:  transaction_id / user_id
	function _auto_login($id) {
		$setting = mgm_get_class('system')->setting;		
		if(!isset($setting['enable_autologin']) || (isset($setting['enable_autologin']) && $setting['enable_autologin'] != 'Y' ))
			return false;
		if(is_numeric($id)) {
			$user_id = null;
			$custom = $this->_get_transaction_passthrough($id);
			if($custom['payment_type'] != 'subscription_purchase' || (!isset($custom['is_registration']) || (isset($custom['is_registration']) && $custom['is_registration'] != 'Y' )) )
				return false;
			if(is_numeric($custom['user_id']) && $custom['user_id'] > 0 ) {
				return mgm_encode_id($id);					
			}						
		}
		return false;
	}
	
	/**
	 * Confirm notify URL.
	 * related to issue#: 528
	 * As PAYPALPRO doesn't use overridden notifyurl, and posts IPN to default IPN settings URL on merchant panel
	 * Confirm module field in transactions table/mgm_member object
	 * 
	 */
	function _confirm_notify() {
		$mod_code = '';
		//check possible params for transaction id [rp_invoice_id, invoice, custom]
		if(isset($_POST['rp_invoice_id']) && is_numeric($_POST['rp_invoice_id'])) {
			$transaction_id = $_POST['rp_invoice_id'];
		}elseif (isset($_POST['invoice']) && is_numeric($_POST['invoice'])) {
			$transaction_id = $_POST['rp_invoice_id'];
		}elseif (isset($_POST['custom']) && is_numeric($_POST['custom'])) {
			$transaction_id = $_POST['rp_invoice_id'];
		}elseif (isset($_POST['custom']) && !is_numeric($_POST['custom']) &&  preg_match('/^subscription_/', $_POST['custom'])) {
			//for backward compatibility:
			//transaction cannot be found for old users: 
			$transdata = $this->_get_transaction_passthrough($_POST['custom']);
			$mgm_member = mgm_get_member($transdata['user_id']);
			if(isset($mgm_member->payment_info->module) && !empty($mgm_member->payment_info->module)) {
				$mod_code = preg_match('/mgm_/', $mgm_member->payment_info->module) ? $mgm_member->payment_info->module : 'mgm_' .$transdata['module'];
			}
		}
		//if a transaction id is found
		if(isset($transaction_id)) {
			$transdata = $this->_get_transaction($transaction_id);
			if(!empty($transdata['module'])) {
				$mod_code = preg_match('/mgm_/', $transdata['module']) ? $transdata['module'] : 'mgm_' .$transdata['module'];  				
			}
		}
		//if module code is found and not belongs to current module, then invode process_notify() function of the applicable module.
		
		if(!empty($mod_code) && $mod_code != $this->code) {
			//recall process_notifyof the module
			//keep the log untill paypal is resolved.
			mgm_log('FROM PAYMENT: recalling ' . $mod_code .'->process_notify() FROM: '. $this->code );	
			mgm_get_module($mod_code, 'payment')->process_notify();
			return false;
		}
		
		return true;
	}
	/**
	 * This function needs to be overridden
	 *
	 * @param unknown_type $trans_ref
	 * @param unknown_type $user_id
	 * @param unknown_type $subscr_id
	 */
	function cancel_recurring_subscription($trans_ref = null, $user_id = null, $subscr_id = null) {
		return true;
	}
	/**
	 * Validate credit card fields.
	 * Modules can override this function
	 * @param unknown_type $calling_fun
	 * @return unknown
	 */
	function validate_cc_fields($calling_fun) {		
		$error = new WP_Error();					
		//mgm_array_dump($_POST);	
		$_POST['mgm_card_holder_name'] 	= trim($_POST['mgm_card_holder_name']);
		$_POST['mgm_card_number'] 		= trim($_POST['mgm_card_number']);
		$_POST['mgm_expiry']['month'] 	= trim($_POST['mgm_expiry']['month']);
		$_POST['mgm_expiry']['year']  	= trim($_POST['mgm_expiry']['year']);
		$_POST['mgm_card_code'] 		= trim($_POST['mgm_card_code']);
		$_POST['mgm_cctype'] 			= trim($_POST['mgm_cctype']);
		if(empty($_POST['mgm_card_holder_name'])) {
			$error->add('invalid_card_holder_name', __('<strong>ERROR</strong>: Invalid Card Holder Name', 'mgm'));
		}
		
		if(!is_numeric($_POST['mgm_card_number']) || 13 > strlen($_POST['mgm_card_number'])) {
			$error->add('invalid_card_number', __('<strong>ERROR</strong>: Invalid Credit Card Number', 'mgm'));
		}
		
		if(!is_numeric($_POST['mgm_expiry']['month']) || !is_numeric($_POST['mgm_expiry']['year'])) {
			$error->add('invalid_expiry', __('<strong>ERROR</strong>: Invalid Credit Card Expiry', 'mgm'));
		}
		
		if(!is_numeric($_POST['mgm_card_code']) || 3 > strlen($_POST['mgm_card_code'])) {
			$error->add('invalid_card_code', __('<strong>ERROR</strong>: Invalid CVV', 'mgm'));
		}
		
		if(empty($_POST['mgm_cctype'])) {
			$error->add('invalid_cctype', __('<strong>ERROR</strong>: Invalid Card Type', 'mgm'));
		}	

		return $error;	
	}
	/**
	 * Wrapper function for validate_cc_fields
	 *
	 * @param unknown_type $calling_fun
	 */
	function validate_cc_fields_process($calling_fun = 'process_html_redirect', $return = true) {
		//Only if submitted from credit card form 
		if(isset($_POST['submit_from']) && $_POST['submit_from'] == $calling_fun ) {
			$errors = $this->validate_cc_fields($calling_fun);				
			if(is_wp_error($errors) && $errors->get_error_code()) {			
				$error_string = mgm_set_errors($errors, true);
				
				if($return)
					return $error_string;
				else 
					echo $error_string;	
			}else {			
				//call process_credit_card:
				$this->process_credit_card();
			}
		}
		
		return '';
	}
	
	// fix
	function apply_fix($old_obj){
		// to be copied vars
		$vars = array('button_type','status','settings_tab','description','logo','enabled','supported_buttons','supports_trial',
					  'supports_cancellation','requires_product_mapping','hosted_payment','end_points','setting');
		// set
		foreach($vars as $var){
			// var
			$this->{$var} = (isset( $old_obj->{$var} ) ) ? $old_obj->{$var} : '';
		}			
		// save
		$this->save();	
	}
	
	// prepare save, define the object vars to be saved
	function _prepare(){		
		// init array
		$this->options = array();
		// to be saved
		$vars = array('button_type','status','settings_tab','description','logo','enabled','supported_buttons','supports_trial',
					  'supports_cancellation','requires_product_mapping','hosted_payment','end_points','setting');
		// set
		foreach($vars as $var){
			// var
			$this->options[$var] = $this->{$var};
		}		
	}
}
// end of file core/libs/components/mgm_payment.php