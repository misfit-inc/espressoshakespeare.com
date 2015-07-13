<?php
// dump
function mgk_array_dump($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

// log
function mgk_log($data){

    static $incrementer=1;

    // id

    define('LOG_REQUEST_ID' , substr(microtime(),2,8));

    // log

    $fp         = fopen(WP_PLUGIN_DIR.'/magickicker/core/logs/'.LOG_REQUEST_ID.'.log', "a+");

    $begin_crlf = "\n\r";

    $end_crlf   = "\n\r";

    // write

    fwrite($fp, $begin_crlf . ($incrementer++) .':'. $end_crlf . $data);

    // close

    fclose($fp);

}
// box top
function mgk_box_top($title, $helpkey="", $return=false, $attributes=false){
	// defaults
	$attributes_default=array('width'=>845,'style'=>'margin:10px 0;');
	// attributes
	if(is_array($attributes)){
		$options=array_merge($attributes_default,$attributes);
	}else{
		$options=$attributes_default;
	}
	// local
	extract($options);	
	
	// help key
	if(empty($helpkey)){
		$helpkey=strtolower(preg_replace('/\s+/','',$title));
	}
	// html
	$html= '<div class="mk-panel-box" style="'.$style.($width ? 'width: ' . ($width) . 'px;':'') .'">			
				<div class="box-title" style="' .($width ? 'width: ' . ($width-5) . 'px;':'') . '">			
					<h3>'.__($title, 'mgk').'</h3>				
						<div class="box-triggers">
							<img src="'.MGK_ASSETS_URL.'images/panel/help-image.png" alt="description" class="box-description" />							
							<img src="'.MGK_ASSETS_URL.'images/panel/television.png" alt="video" class="box-video" />
						</div>						
						<div class="box-description-content">				
							<p>'.mgk_get_tip($helpkey).'</p>				
						</div> <!-- end box-description-content div -->						
						<div class="box-video-content">				
							<p>'.mgk_get_video($helpkey).'</p>				
						</div> <!-- end box-video-content div -->				
					</div> <!-- end div box-title -->				
					<div class="box-content">';	
			  
	// return output
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}

// box bottom
function mgk_box_bottom($return=false){
	$html = '</div>
		   </div>';
	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}

// tips
function mgk_get_tip($key){
	global 	$mgk_tips_info;
	return (isset($mgk_tips_info[$key]))?$mgk_tips_info[$key]:"[tip '$key' missing]";
}	

// videos
function mgk_get_video($key){	
	global 	$mgk_videos_info;
	return (isset($mgk_videos_info[$key]))?$mgk_videos_info[$key]:"[video '$key' missing]";
}

// executor
function mgk_call_process($func,$default='f_index'){
	// set name flag
	$func="f_{$func}";
	// when set
	if(isset($func) && is_callable($func)){		
		call_user_func($func);
	// default	
	}else if(isset($default) && is_callable($default)){
		call_user_func($default);
	// else
	}else{
		echo 'error loading method';
	}
}

// infobar
function mgk_render_infobar() {
	$style = 'style="color:#161616; text-decoration:none;"';	

	echo '<div id="mgk-info" style="float:right; color:#161616; padding-top:10px; padding-right:15px;">
			<strong>
				<a href="http://www.magicmembers.com/" ' . $style . '>'.__('Magic Kicker','mgk').'</a> |
				<a href="http://www.magicmembers.com/support-center/" ' . $style . '>'.__('Support','mgk').'</a> |
				<a href="http://www.magicmembers.com/' . MGK_PLUGIN_PRODUCT_URL .'" ' . $style . ' target="_blank">V. ' . MGK_PLUGIN_VERSION . ' - '. MGK_PLUGIN_PRODUCT_NAME .'</a>				
			</strong>
		</div>';
}

// config
function mgk_get_setting($key,$group='general',$default=''){
	$mgk_settings =get_option('mgk_settings');
	$settings     =$mgk_settings[$group];	
	
	return (isset($settings[$key]))?$settings[$key]:$default;
}

// mail
function mgk_mail($to, $subject, $message, $from="", $subject_charset = 'UTF-8', $message_charset = 'UTF-8'){		
	// form	
	if(!empty($from)){
		list($from_name,$from_email) = explode(',',$from);	
	}else{
		$from_name  = get_option('blogname');
		$from_email = get_option('admin_email');
	}		
	// bcc
	$bcc_email = '';
	
	// header
	$headers = "MIME-Version: 1.0
	From: {$from_name} <{$from_email}>
	Reply-To: {$from_email}
	{$bcc_email}
	Return-Path: {$from_email}
	X-Sender: {$from_email}
	X-Mailer: PHP/" . phpversion() . "
	Content-Type: text/html; charset=\"$message_charset\"
	Content-Transfer-Encoding: 8bit";
    // sent
	wp_mail($to, $subject, $message, $headers);
}

// remote call
function mgk_remote_request($url, $error_string=true) {
    $string = '';
        
	if (ini_get('allow_url_fopen')) {
		if (!$string = @file_get_contents($url)) {
            if ($error_string) {
				$string = 'Could not connect to the server to make the request.';
            }
		}
	} else if (extension_loaded('curl')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$string = curl_exec($ch);
		curl_close($ch);
	} else if ($error_string) {
	    $string = 'This feature will not function until either CURL or fopen to urls is turned on.';
	}

	return $string;
}

// download updrade file
function mgk_download_url( $url ) {	
	//WARNING: The file is not automatically deleted, The script must unlink() the file.
	if ( ! $url )
		return new WP_Error('http_no_url', __('Invalid URL Provided'));

	$tmpfname = mgk_tempnam($url);
	if ( ! $tmpfname )
		return new WP_Error('http_no_file', __('Could not create Temporary file'));

	$handle = @fopen($tmpfname, 'wb');
	if ( ! $handle )
		return new WP_Error('http_no_file', __('Could not create Temporary file'));

	$response = wp_remote_get($url, array('timeout' => 120));
    
	if ( is_wp_error($response) ) {
		fclose($handle);
		unlink($tmpfname);
		return $response;
	}

	if ( $response['response']['code'] != '200' ){
		fclose($handle);
		unlink($tmpfname);
		return new WP_Error('http_404', trim($response['response']['message']));
	}

	fwrite($handle, $response['body']);
	fclose($handle);

	return $tmpfname;
}

// tempname
function mgk_tempnam($url = '', $dir = ''){
	if ( empty($dir) )
		$dir = get_temp_dir();
	
	// parse filename
	parse_str($url);
	// set filename
	$filename = (!empty($filename))?$filename:basename($url);
	if (empty($filename) )
		$filename = time();

	$filename = $dir . wp_unique_filename($dir, $filename);
	touch($filename);
	return $filename;
}

// pad string
function mgk_pad_string($str,$len=50,$linebrk=""){
	// trim space
	$str=trim(strip_tags($str));
	// nothing
	if($str=="")
		return "&nbsp;";
	
	// when length matched
	if(strlen($str)>$len){
		if($linebrk==""){
			# no line break </br>
			$str=substr($str,0,$len-5);
			$str=str_pad($str,$len,".",STR_PAD_RIGHT);
		}else{
			# break after length
			$str=chunk_split($str,$len,$linebrk);	   
		}
	}
	// return 
	return $str;
} 

// ui version
function mgk_get_jqueryui_version(){
	// compare version if greater than 2.9
	if (version_compare(get_bloginfo('version'), '2.9', '>=')){
		// ui 1.7.3 for jQuery 1.4+ options : 1.7.3 , 1.8.2
		$jqueryui_version = get_option('mgk_jqueryui_version');
		if(!$jqueryui_version){// not defined, use as coded
			$jqueryui_version = '1.7.3';		
			update_option('mgk_jqueryui_version', $jqueryui_version); // and update		 
		}
	}else{
		// ui 1.7.2 for jQuery 1.3.2+
		$jqueryui_version = '1.7.2';			 
	}
	// return
	return $jqueryui_version;
}

// deep stripslashes
function mgk_stripslashes_deep($data){	
	// clean till found '\'
	do{
		$data = stripslashes($data);
	}while(strpos($data, '\\') !==false);	
	// return
	return $data;
}

// recursive slash remove
function mgk_array_stripslashes($v) {		
	// return stripped
	return is_array($v) ? array_map('mgk_array_stripslashes', $v) : stripslashes($v);	
}

// recursive slash add
function mgk_array_addslashes($v) {
	// is it enabled, return as 
	if(MGK_MAGIC_QUOTES_GPC){
		return $v;
	}
	// return stripped
	return is_array($v) ? array_map('mgk_array_addslashes', $v) : addslashes($v);
}

// check lockout
function mgk_check_lockout($current_user) {
	global $wpdb;

	$return = false;
	$lockout = get_user_meta($current_user->ID, 'mgk_locked_out', true);
	
	// mgk_log('mgk_check_lockout user - '.$current_user->ID);
	
	// mgk_log('mgk_check_lockout lockout - '.$lockout);
	
	if (!$lockout) {

		$time_offset = time() - (mgk_get_setting('timeout_minutes') * 60); 
		$max_logins  = mgk_get_setting('timeout_logins'); 

		$sql = 'SELECT COUNT(DISTINCT(ip_address)) FROM `' . TBL_MGK_USER_IPS . '` WHERE user_id = ' . $current_user->ID . '
				AND UNIX_TIMESTAMP(access_dt) > ' . $time_offset;
		
		// mgk_log('mgk_check_lockout sql - '.$sql);					
			
		$logins = $wpdb->get_var($sql);
		
		// mgk_log('mgk_check_lockout logins - '.$logins. ' X '.$max_logins);
		if ($logins >= $max_logins) {
			$return = true;
		}
	} else if ($lockout == 1) {
		$return = true;
	} else if ($lockout > 1) {
		$lockout_expiry = mgk_check_lockout_expiry($current_user);

		if ($lockout_expiry > time()) {
			$return = true;
		}
	}

	
	return $return;
}

// lockout expiry
function mgk_check_lockout_expiry($user) {
	$lockout_expiry = 0;

	if ($locked_out_since = get_user_meta($user->ID, 'mgk_locked_out', true)) {
		$logout_secs = mgk_get_setting('lockout_minutes') * 60;
		$lockout_expiry = ($locked_out_since + $logout_secs);
	}

	return $lockout_expiry;
}

// ip block
function mgk_ip_is_blocked($ip_address) {
	global $wpdb;

	$sql = 'SELECT COUNT(id) FROM `' . TBL_MGK_BLOCKED_IPS . '` WHERE `ip_address` = "'.$ip_address.'"';
	
	// mgk_log('mgk_ip_is_blocked - '.$sql);

	return $wpdb->get_var($sql);

}

// filter post
function mgk_filter_post($content) {

	$pattern = "'\[\[user_ip]\]'is";
	$content = preg_replace_callback($pattern, "mgk_current_user_ip", $content);

	$pattern = "'\[\[user_last_ip]\]'is";
	$content = preg_replace_callback($pattern, "mgk_user_last_ip", $content);

	$pattern = "'\[\[user_last_login]\]'is";
	$content = preg_replace_callback($pattern, "mgk_user_last_login", $content);

	return $content;
}

// log ip filter on authenticate
function mgk_authenticate_user($userdata) {
	global $wpdb;
	// log
	// mgk_log('mgk_authenticate_user start '.$userdata->ID);
	
	// no check for admin
	if(is_super_admin($userdata->ID)){
		return $userdata;
	}		
	
	// mgk_array_dump($userdata);
	
	// die('error in here ');
	// log
	// mgk_log('mgk_authenticate_user other user - '.get_user_meta($user->ID, 'mgk_locked_out', true));
	// init 
	$is_error = false;
	// other user
	if (get_user_meta($userdata->ID, 'mgk_locked_out', true) > 1) {
		// log
		// mgk_log('mgk_authenticate_user mgk_locked_out - '.$user->ID);
		// expi
		$lockout_expiry = mgk_check_lockout_expiry($userdata);
		if ($lockout_expiry < time()) {
			update_user_meta($userdata->ID, 'mgk_locked_out', 0); //clear the lockout
		} else {
			$is_error = true;
		}
	} else if (get_user_meta($userdata->ID, 'mgk_locked_out', true) == 1) {
		$is_error = true;
	} else if (mgk_ip_is_blocked(mgk_current_user_ip())) {
		$is_error = true;
	} else {
		// just log		
		$sql = 'INSERT INTO `' .TBL_MGK_USER_IPS . '` (user_id, ip_address, access_dt) VALUES (' . $userdata->ID . ', 
				"' . mgk_current_user_ip() . '"	, NOW()	)';
		$wpdb->query($sql);
		
		// log
		// mgk_log('mgk_authenticate_user last - '.$sql);
	}
	
	// has error
	if ($is_error) {
		$message = mgk_get_setting('login_error');
		$error = new WP_Error();
		$error->add('locked_out', $message);		
		return $error;
	}
	
	// return userdata
	return $userdata;
}

// ip check on wp_login/init
function mgk_lockout_user() {
	// get user by login
	// $current_user = get_user_by('login', $user_login);	
	// bet current user
	$current_user = wp_get_current_user();

	if ( empty( $current_user ) )
		return false;
	
	// log
	// mgk_log('mgk_lockout_user '.$current_user->ID);
	
	// other users check 
	if ($current_user->ID && !is_super_admin($current_user->ID)) {
		// log
		// mgk_log('mgk_lockout_user in ');
		// if checked
		if (mgk_check_lockout($current_user)) {
			
			// log
			// mgk_log('mgk_lockout_user mgk_check_lockout true');
			
			$lockout    = mgk_get_setting('lockout_option');// lockout|logout
			$send_email = (mgk_get_setting('email_offender') == 'email') ? true : false;

			// log
			// mgk_log('mgk_lockout_user mgk_check_lockout '.$lockout);			
			// mgk_log('mgk_lockout_user send_email '.$send_email);
			
			if ($lockout == 'lockout') {
				if ($send_email) {
					$value = 1;
				} else {
					$value = time();
				}
				// update
				update_user_meta($current_user->ID, 'mgk_locked_out', $value);
			}
			
			// send mail
			if ($send_email){
				// mgk_log('mgk_lockout_user mail sent ');
				mgk_email_user();
			}
			
			// clear cookie
			wp_clearcookie();
			
			// redirect
			$redirect = mgk_get_setting('redirect_url');	
			// redirect		
			wp_redirect($redirect);
		}
	}
	
	// return true;
}

// log accessed urls
function mgk_url_access_log(){
	global $wpdb;	
	// do not log admin pages
	if(!is_admin()){
		// get user 
		$current_user = wp_get_current_user();
		// get url
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		// get last 
		// echo $current_url;		
		// get last logged ip id
		$ip_id = $wpdb->get_var("SELECT `id` FROM `".TBL_MGK_USER_IPS."` WHERE `user_id` = '{$current_user->ID}' ORDER BY `access_dt` DESC LIMIT 1 ");
		// insert
		if($ip_id){
			$wpdb->insert(TBL_MGK_ACCESSED_URLS, array('ip_id'=>$ip_id, 'url'=>$current_url, 'access_dt'=>date('Y-m-d h:i:s')));
		}
	}
}
// filnename parse
function mgk_is_login_referrer() {
	$url = pathinfo($_SERVER['HTTP_REFERER'], PATHINFO_FILENAME);	
	if($url == 'wp-login'){
		return true;
	}
	// 	false
	return false;
}

// current IP
function mgk_current_user_ip() {
	if ( isset($_SERVER["REMOTE_ADDR"]) ){
		return $_SERVER["REMOTE_ADDR"] ;
	} else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ){
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else if ( isset($_SERVER["HTTP_CLIENT_IP"]) ){
		return $_SERVER["HTTP_CLIENT_IP"] ;
	} 
}

// email to user
function mgk_email_user() {
	get_currentuserinfo();
	global $current_user;

	$to      = $current_user->user_email;
	$subject = mgk_get_setting('locked_mail_subject');
	$message = mgk_get_setting('locked_mail_message');

	$key = md5('user_' . $current_user->ID) . mt_rand(1,999);

	$url = add_query_arg(array('mgk_activate'=>$key), site_url());
	
	$link = '<a href="' . $url . '">' . $url . '</a>';
	$message = str_replace('[activation_link]',$link, $message);
	$message = str_replace('[name]',$current_user->display_name, $message);

	//set key
	update_user_meta($current_user->ID, 'mgk_activation_key', $key);

	// mail ti user
	wp_mail($to, $subject, $message);
}

function mgk_check_activate_account() {
	global $wpdb;

	
	if (mgk_get_var('mgk_activate')) {
	
		$sql = "SELECT `user_id` FROM `" . $wpdb->usermeta . "` WHERE `meta_key` = 'mgk_activation_key'
			    AND meta_value = '" . mysql_real_escape_string(mgk_get_var('mgk_activate')) . "'";
		
		if ($id = $wpdb->get_var($sql)) {
			update_usermeta($id, 'mgk_locked_out', 0);
			delete_usermeta($id, 'mgk_activation_key');

			$redirect = mgk_get_setting('activation_url');
			if ($redirect) {
				echo '<script>document.location="' . $redirect . '";</script>';
			}
		}
	}
}

// user last ip
function mgk_user_last_ip($user_id=false) {
	global $wpdb;
		
	$return = false;

	if (!$user) {
		// get current user
		$current_user = wp_get_current_user();
		// set id
		$user_id = $current_user->ID;
	}

	if ($user_id) {

		$sql = 'SELECT `ip_address`	FROM `' . TBL_MGK_USER_IPS . '` WHERE `user_id` = "' . $user_id . '"
				ORDER BY `access_dt` DESC LIMIT 1';
		$return = $wpdb->get_var($sql);
	}

	return $return;
}
// user last login
function mgk_user_last_login($user=false) {
	global $user_ID, $wpdb;

	if (!$user) {
		$user = $user_ID;
	}

	if ($user) {
		$time = false;

		$sql = 'SELECT `ip_address`, `access_dt`	FROM `' . TBL_MGK_USER_IPS . '`	WHERE `user_id` = '.$user.'
				ORDER BY `access_dt` DESC LIMIT 1';
		if ($r = $wpdb->get_row($sql)) {
			$time = date(mgk_get_setting('short_date_format'), strtotime($r->access_dt));
		}
	}

	return $time;
}

function mgk_request_var($key, $default='', $strip_tags=false) {
	if (isset($_POST[$key])) {
		$default = $_POST[$key];

		if ($strip_tags) {
			$default = strip_tags($default);
		}
	}

	return $default;
}

function mgk_post_var($key, $default='', $strip_tags=false) {
	if (isset($_POST[$key])) {
		$default = $_POST[$key];

		if ($strip_tags) {
			$default = strip_tags($default);
		}
	}

	return $default;
}

function mgk_get_var($key, $default='', $strip_tags=false) {
	if (isset($_GET[$key])) {
		$default = $_GET[$key];

		if ($strip_tags) {
			$default = strip_tags($default);
		}
	}

	return $default;
}

// get_jquery_ui_versions
function mgk_get_jquery_ui_versions(){
	// read
	$_versions = glob(MGK_ASSETS_DIR . implode(MGK_DS, array('js','jquery','jquery.ui')) . MGK_DS . 'jquery-ui-*.{js}', GLOB_BRACE);	
	// init
	$versions = array('1.7.2','1.7.3','1.8.2');
	// check
	if($_versions){
		// loop
		foreach($_versions as $_version){
			// trim
			$versions[] = str_replace(array('jquery-ui-','.min.js'), '', basename($_version));
		}
	}	
	// return 
	return array_unique($versions);	
}

// end of file