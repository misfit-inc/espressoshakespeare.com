<?php
// Constants
// control constants
define('MGM_BUILD'                  , '2.5.0'); // development build, let this controlled by priyabrata, branch.sub-branch/svn release
define('MGM_STAGE'                  , 'dev');// put stable/live for distribution, control flags for development
// notify constants
define('MGM_DEVELOPER_EMAIL'        , 'developer@magicmembers.com');// optional
define('MGM_REPORTBUG_EMAIL'        , 'reportbug@magicmembers.com');// optional

// service constants 
// moved to classes/mgm_auth.php  for security
// define('MGM_SERVICE_DOMAIN', 'http://localhost/magicmediagroup/'); only open when testing

// information constants
define('MGM_GET_FORUM_URL'          , 'http://www.magicmembers.com/support/rss/topics');
define('MGM_GET_NEWS_URL'           , 'http://www.magicmembers.com/?cat=19&feed=rss2');
define('MGM_GET_BLOG_RSS'           , 'http://www.magicmembers.com/?cat=3&feed=rss2');

// affilte id
define('MGM_AFFILIATE_ID'           , '1');// default 

// system constants
// access
define('MGM_ACCESS_PRIVATE'         , __('Private', 'mgm'));
define('MGM_ACCESS_PUBLIC'          , __('Public', 'mgm'));

// statuses
define('MGM_STATUS_NULL'            , __('Inactive','mgm'));
define('MGM_STATUS_ACTIVE'          , __('Active','mgm'));
define('MGM_STATUS_EXPIRED'         , __('Expired','mgm'));
define('MGM_STATUS_PENDING'         , __('Pending','mgm'));
define('MGM_STATUS_TRIAL_EXPIRED'   , __('Trial Expired','mgm'));
define('MGM_STATUS_CANCELLED'       , __('Cancelled','mgm'));
define('MGM_STATUS_ERROR'           , __('Error','mgm'));
define('MGM_STATUS_AWAITING_CANCEL' , __('Awaiting Cancelled','mgm'));

// date formats
define('MGM_DATE_FORMAT'            , 'M dS Y');
define('MGM_DATE_FORMAT_LONG'       , 'F j, Y');
define('MGM_DATE_FORMAT_LONG_TIME'  , 'F j, Y g:i A');
define('MGM_DATE_FORMAT_SHORT'      , 'm/d/Y');
define('MGM_DATE_FORMAT_INPUT'      , 'm/d/Y');

// comobo options
define('MGM_VALUE_ONLY'             , 1);
define('MGM_KEY_VALUE'              , 2);

// api uri
define('MGM_API_URI_PREFIX'        , SITECOOKIEPATH . 'mgmapi');
define('MGM_API_ALLOW_HOST'        , '127.0.0.1,localhost,sandbox.sologicsolutions.com,sandbox.wpmembershipservice.com,test.membershipsoftware.tv');// all, none or named, restricted now
define('MGM_API_KEY_VAR'           , 'X-MGMAPI-KEY');
define('MGM_API_CLASS_PREFIX'      , 'mgm_api_');
 
// end of file