<?php
// table schema
// block list
$sql = "CREATE TABLE IF NOT EXISTS `".TBL_MGK_BLOCKED_IPS."` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`ip_address` VARCHAR( 20 ) NOT NULL,
		`blocked_dt` DATETIME NOT NULL,
		 PRIMARY KEY (  `id` )
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Blocked ip records';";
$wpdb->query($sql);	
// ip logs
$sql = "CREATE TABLE IF NOT EXISTS `".TBL_MGK_USER_IPS."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` bigint(20) unsigned NOT NULL,
		  `ip_address` varchar(20) NOT NULL,
		  `access_dt` DATETIME NOT NULL,
		   PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='IP log records';";
$wpdb->query($sql);	
// page logs
$sql = "CREATE TABLE IF NOT EXISTS `".TBL_MGK_ACCESSED_URLS."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `ip_id` int(11) unsigned NOT NULL,
		  `url` TEXT NOT NULL, 
		  `access_dt` DATETIME NOT NULL,		  
		   PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='URL log records';";
$wpdb->query($sql);		
// end of file