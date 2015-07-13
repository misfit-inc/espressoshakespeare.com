<?php
// options
 // settions
 $mgk_settings = array();
 // general
 $mgk_settings['general']['redirect_url']      = site_url('wp-login.php');
 $mgk_settings['general']['email_offender']    = 'timed';
 $mgk_settings['general']['timeout_minutes']   = 5;
 $mgk_settings['general']['timeout_logins']    = 2;
 $mgk_settings['general']['lockout_option']    = 'lockout';
 $mgk_settings['general']['lockout_minutes']   = 5;
 $mgk_settings['general']['login_error']       = __('Your account has been locked out.', 'mgk');
 $mgk_settings['general']['activation_url']    = site_url('wp-login.php');
 $mgk_settings['general']['notify_admin']      = 'Y';
 $mgk_settings['general']['short_date_format'] = 'm-d-Y';
 $mgk_settings['general']['long_date_format']  = 'm-d-Y H:i:s';
 // 	pagination
 $mgk_settings['general']['pagination']        = 20;	
 // to user : account locked notifictaion
 $mgk_settings['general']['locked_mail_subject']  = 'Multiple Users Detected on user account';
 $mgk_settings['general']['locked_mail_message']  = 'Dear [name],<br>'.
												   'Your User Account was accessed from two separate IP addresses at the same time. <br>																
												    Click the following link to reactivate your account::<br>'.
												   '[activation_link]<br>'.
												   'Regards,<br>'.
												   'Magic Kicker Team';												  																
																   																 
 // add settings
 add_option('mgk_settings', $mgk_settings);

 // version
 update_option('mgk_version', MGK_PLUGIN_VERSION);

 // update upgrade id, track for upgrade 
 // default 
 $upgrade_id = '1.0';
 // get list of upgrades
 $upgrades = glob(str_replace('install','upgrades',dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'upgrade_id_*', GLOB_BRACE);
 // we have some in the list
 if(count($upgrades)>0){
	// loop
	foreach($upgrades as $upgrade){		
		// get id form folder
		$upgrade_id = str_replace('upgrade_id_', '', pathinfo($upgrade, PATHINFO_BASENAME));
	}
 }		
 // update			
 update_option('mgk_upgrade_id', $upgrade_id);// 1.1 is last

// end of file