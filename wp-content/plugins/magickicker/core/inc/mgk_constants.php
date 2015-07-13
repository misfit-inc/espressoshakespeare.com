<?php
// control constants
define('MGK_BUILD'                   , '2.1'); // development build/release, let this controlled by priyabrata, branch.sub-branch/svn release
define('MGK_STAGE'                   , 'dev');// put stable/live for distribution, control flags for development

// service constants, to be updated by manager
// moved to inc/mgk_auth.php  for security

// define GPC if not done
define('MGK_MAGIC_QUOTES_GPC'        , ini_get('magic_quotes_gpc'));

// date formats
define('MGK_DATE_FORMAT'             , 'M dS Y');
define('MGK_DATE_FORMAT_LONG'        , 'F j, Y');
define('MGK_DATE_FORMAT_SHORT'       , 'm/d/Y');
define('MGK_DATE_FORMAT_INPUT'       , 'm/d/Y');

// combo options
define('MGK_VALUE_ONLY'              , 1);
define('MGK_KEY_VALUE'               , 2);
// end of file