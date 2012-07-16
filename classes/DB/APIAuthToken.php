<?php 

class APIAuthToken extends DBRecord {
	var $tablename = 'api_auth_tokens';
	var $primary = array('token');
		
	static function RemoveOld() {
		Database::Query("DELETE FROM api_auth_tokens WHERE last_used < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
	}

}


/**

CREATE TABLE IF NOT EXISTS `api_auth_tokens` (
  `token` varchar(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `source_ip` varchar(20) NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`token`)
)

*/