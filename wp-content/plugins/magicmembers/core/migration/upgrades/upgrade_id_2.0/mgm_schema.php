<?php
/** 
 * Schema update
 */ 	
 // transaction options, custom fields
 $sql = 'CREATE TABLE `' . TBL_MGM_TRANSACTION_OPTION . '` (
			`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`transaction_id` bigint(20) UNSIGNED NOT NULL,	
			`option_name` VARCHAR( 255 ) NOT NULL ,
			`option_value` TEXT NOT NULL,
			 UNIQUE KEY `transaction_id` (`transaction_id`,`option_name`)
			) ENGINE = MYISAM COMMENT = "transaction options"';
 $wpdb->query($sql);
 
 // end of file
