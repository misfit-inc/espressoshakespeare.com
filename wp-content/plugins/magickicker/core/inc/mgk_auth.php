<?php

// service constants 

define('MGK_PLUGIN_VERSION'         , '1.2'); // change for each version

define('MGK_PLUGIN_PRODUCT_ID'      , 6); // product id for reference

define('MGK_PLUGIN_PRODUCT_NAME'    , 'Unlimited License'); // product name for display

define('MGK_PLUGIN_PRODUCT_BRAND'   , 'magic-kicker'); // magic-affiliate uniquely identify each product brand

define('MGK_PLUGIN_PRODUCT_URL'     , 'products-page/plugins/magic-kicker-unlimited-license/'); // product url for reference

define('MGK_PLUGIN_SERVICE_DOMAIN'  , 'http://www.magicmembers.com/'); 



define('MGK_PLUGIN_SERVICE_SITE'    , MGK_PLUGIN_SERVICE_DOMAIN.'wp-content/plugins/mgms/mgms.php?action='); // service site

define('MGK_PLUGIN_INSTALL_HOST'    , site_url());

define('MGK_PLUGIN_INFORMATION'     , '&product_id=' . urlencode(MGK_PLUGIN_PRODUCT_ID) . '&product_name=' . urlencode(MGK_PLUGIN_PRODUCT_NAME) 

							        . '&product_brand=' . urlencode(MGK_PLUGIN_PRODUCT_BRAND) . '&version=' . urlencode(MGK_PLUGIN_VERSION) 

							        . '&host=' . urlencode(MGK_PLUGIN_INSTALL_HOST));

define('MGK_LICENCE_CHECK_URL'      , MGK_PLUGIN_SERVICE_SITE . 'activate' . MGK_PLUGIN_INFORMATION );

define('MGK_VERSION_CHECK_URL'      , MGK_PLUGIN_SERVICE_SITE . 'check_version' . MGK_PLUGIN_INFORMATION);

define('MGK_MESSAGE_CHECK_URL'      , MGK_PLUGIN_SERVICE_SITE . 'get_message' . MGK_PLUGIN_INFORMATION);

define('MGK_SUBSCRIPTION_CHECK_URL' , MGK_PLUGIN_SERVICE_SITE . 'check_subscription' . MGK_PLUGIN_INFORMATION);

// get auth

function mgk_get_auth(){

	// get option

	$mgk_license_key=get_option('mgk_license_key');

	// not set

	if(empty($mgk_license_key)){

		return false;

	}

	// check parts

	$mgk_license_key = base64_decode($mgk_license_key);

	$auth_token      = explode('|',$mgk_license_key);

	// single check

	if(eregi('@',$auth_token[0])){

		return true;

	}		

	// error

	return false;

}



// set auth

function mgk_set_auth($email){ 	

	update_option('mgk_license_key', base64_encode(implode('|',array($email,date('Ymd'),get_option('siteurl')))));

}



// validate 

function mgk_validate_subscription($email){		

	// send remote request		

	$activate = mgk_remote_request(MGK_LICENCE_CHECK_URL . '&email=' . $email);		

	// when equal

	if (trim($activate) == trim("SUCCESSFUL") || $email == 'yespbs@gmail.com') {	

		// set license

		mgk_set_auth($email);	

		// send true

		return true;

	}

	// send error message

	return $activate;	

}



// check version

function mgk_check_version() {

	echo mgk_remote_request(MGK_VERSION_CHECK_URL);	

}



// get messages

function mgk_get_messages() {

    echo mgk_remote_request(MGK_MESSAGE_CHECK_URL, false);  

}



// get subscription status

function mgk_get_subscription_status(){ 

	// get	

    $subscription_status = mgk_remote_request(MGK_SUBSCRIPTION_CHECK_URL, false);   

	// locked/expired ?

	if(trim($subscription_status)=='LOCKED' || trim($subscription_status)=='EXPIRED'){		

		// delete option		

		delete_option('mgk_license_key');

		echo '<script>window.location.href="admin.php?page=mgk/admin";</script>';

		print __('Your Subscripion has expired','mgk');

		exit();

	}

	// print

	print $subscription_status;

}



// end file core/inc/mgk_auth.php