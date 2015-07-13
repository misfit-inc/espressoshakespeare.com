<?php
   /**
    * Table Names
    */
	
	global $wpdb;
 	// tables	
	define('TBL_MGM_COUNTRY'                 , $wpdb->prefix . 'mgm_countries');
	define('TBL_MGM_COUPON'                  , $wpdb->prefix . 'mgm_coupons');
	define('TBL_MGM_DOWNLOAD'                , $wpdb->prefix . 'mgm_downloads');
	define('TBL_MGM_DOWNLOAD_ATTRIBUTE'      , $wpdb->prefix . 'mgm_download_attributes');
	define('TBL_MGM_DOWNLOAD_ATTRIBUTE_TYPE' , $wpdb->prefix . 'mgm_download_attribute_types');
	define('TBL_MGM_DOWNLOAD_POST_ASSOC'     , $wpdb->prefix . 'mgm_download_post_assoc');
	define('TBL_MGM_POSTS_PURCHASED'         , $wpdb->prefix . 'mgm_posts_purchased');
	define('TBL_MGM_POST_PACK'               , $wpdb->prefix . 'mgm_post_packs');
	define('TBL_MGM_POST_PACK_POST_ASSOC'    , $wpdb->prefix . 'mgm_post_pack_post_assoc');
	define('TBL_MGM_POST_PROTECTED_URL'      , $wpdb->prefix . 'mgm_post_protected_urls');	
	
	define('TBL_MGM_REST_API_KEY'            , $wpdb->prefix . 'mgm_rest_api_keys');	
	define('TBL_MGM_REST_API_LEVEL'          , $wpdb->prefix . 'mgm_rest_api_levels');	
	define('TBL_MGM_REST_API_LOG'            , $wpdb->prefix . 'mgm_rest_api_logs');	
	
	define('TBL_MGM_TEMPLATE'                , $wpdb->prefix . 'mgm_templates');	
	define('TBL_MGM_TRANSACTION'             , $wpdb->prefix . 'mgm_transactions');
	define('TBL_MGM_TRANSACTION_OPTION'      , $wpdb->prefix . 'mgm_transaction_options');
	//Epoch gateway - DataPlus tables 
	define('TBL_MGM_EPOCH_TRANS_STATUS'      , 'EpochTransStats');
	define('TBL_MGM_EPOCH_CANCEL_STATUS'     , 'MemberCancelStats');
	

// end of file