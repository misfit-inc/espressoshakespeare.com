<?php
 global $wpdb; 	
 // rename tables
 // RENAME TABLE `wp_mgk_block_list` TO `wp_mgk_blocked_ips` ;
 $sql = "RENAME TABLE `".$wpdb->prefix."mgk_block_list` TO `".$wpdb->prefix."mgk_blocked_ips`";
 $wpdb->query($sql);
 
 // RENAME TABLE `wp_mgk_ip_log` TO `wp_mgk_user_ips` ;
 $sql = "RENAME TABLE `".$wpdb->prefix."mgk_ip_log` TO `".$wpdb->prefix."mgk_user_ips`";
 $wpdb->query($sql);
 	
 // table schema	
 // ALTER TABLE `wp_mgk_block_list` CHANGE `ip` `ip_address` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL 
 $sql = "ALTER TABLE `".TBL_MGK_BLOCKED_IPS."` CHANGE `ip` `ip_address` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
 $wpdb->query($sql);	
 
 // ALTER TABLE `wp_mgk_block_list` ADD `blocked_dt` DATETIME NOT NULL 
 $sql = "ALTER TABLE `".TBL_MGK_BLOCKED_IPS."` ADD `blocked_dt` DATETIME NOT NULL;";
 $wpdb->query($sql);	
 
 // ALTER TABLE `wp_mgk_block_list` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT 
 $sql = "ALTER TABLE `".TBL_MGK_BLOCKED_IPS."` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;";
 $wpdb->query($sql);
 
 // ALTER TABLE `wp_mgk_ip_log` CHANGE `unixtime` `access_dt` DATETIME NOT NULL 
 $sql = "ALTER TABLE `".TBL_MGK_USER_IPS."` CHANGE `unixtime` `access_dt` DATETIME NOT NULL;";
 $wpdb->query($sql);
 
 // ALTER TABLE `wp_mgk_ip_log` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
 // CHANGE `user_id` `user_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
 // CHANGE `ip_address` `ip_address` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL  
 $sql = "ALTER TABLE `".TBL_MGK_USER_IPS."` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
         CHANGE `user_id` `user_id` BIGINT( 20 ) UNSIGNED NOT NULL, 
		 CHANGE `ip_address` `ip_address` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
 $wpdb->query($sql);
 
 // new table
 // page logs
 $sql = "CREATE TABLE IF NOT EXISTS `".TBL_MGK_ACCESSED_URLS."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `ip_id` int(11) unsigned NOT NULL,
		  `url` TEXT NOT NULL, 
		  `access_dt` DATETIME NOT NULL,
		   PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='URL log records';";
 $wpdb->query($sql);	
 
?>	