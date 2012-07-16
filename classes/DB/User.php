<?php
namespace DB;
use \Primal\Database\Connection;
use \Primal\SaltedHash;

/**
 * Primal User Database Model
 * 
 * @package Primal
 * @author Jarvis Badgley
 * @copyright 2008 - 2011 Jarvis Badgley
 */

/**
	
	This is a database model for working with site users.  This model is used by the Visitor controller for checking the credentials of logging in users.
	
	Passwords are encrypted with a random salt and must be set using the setPassword function.
	
	Table Schema:
	CREATE TABLE IF NOT EXISTS `users` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `type` enum('Normal','Admin') NOT NULL,
	  `email` varchar(200) NOT NULL,
	  `password` tinytext NOT NULL,
	  `last_login` datetime NOT NULL,
	  `firstname` tinytext NOT NULL,
	  `lastname` tinytext NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `email` (`email`)
	) AUTO_INCREMENT=1001 ;
	INSERT INTO `users` (`type`, `email`, `password`, `firstname`, `lastname`) VALUES ('Admin', 'admin', '$2a$10$e9ff5c93f651c4c565abaeEzhGcqZk9G0GGOB8xGjFsB0IUvJX6Bq', 'Site', 'Administrator');

	The initial insert will provide you with an admin user with the password "admin".  
	This password's hash is calculated with blowfish cost of 1024 iterations.  If you use a different length, you will have to reset the password for this account.
	
*/

/**
	------------------------------------------------------------------------------------------------
*/

class User extends \Primal\Database\Record {
	var $tablename = 'users';
	var $primary = array('id');

	const ERR_PASSCHANGE_OK = 0;
	const ERR_PASSCHANGE_WRONGCURRENT = 1;
	const ERR_PASSCHANGE_WRONGCONFIRM = 2;
	const ERR_PASSCHANGE_TOOSHORT = 3;
	const PASSWORD_MIN_LENGTH = 4;

/**
	INSTANCE FUNCTIONS
*/

	/**
	 * Alias to fetching record values as object properties.  Allows for easier retrieval via Visitor::Current();
	 *
	 * @param string $field Property name being requested
	 * @return mixed
	 */
	function __get($field) {
		if (isset($this[$field])) return $this[$field];
	}


	/**
	 * Function called by Visitor::LoginWithUser.  Updates the last_login column if it exists
	 */
	function handleLogin() {
		if (isset($this['last_login'])) $this->set('last_login','now'); //if the table for users has a last-login value, update it to right now
	}



	/**
	 * Returns the a concatenation of the user's first and last names.
	 *
	 * @return string The user's full name.
	 */
	function fullName() {
		return trim("{$this['firstname']} {$this['lastname']}");
	}

	/**
	 * Proper function for setting the user's password.  Encrypts the password using internal functions and sets it in the database.
	 * If no ID is set on the record, the function assumes that this record has not yet been created and will only set it in memory.
	 *
	 * @param string $newpass The plaintext password we wish to set for this user.
	 * @param null|boolean $autosave Optional argument to force/deny automatic calling of $this->set() for the password value
	 * @return string The generated hash.
	 */
	function setPassword($newpass, $autosave=null) {
		$sh = new SaltedHash();
		$hash = $sh->hash($newpass);
		
		if (($this['id'] && $autosave!==false) || $autosave===true) $this->set('password',$hash);
		else $this['password'] = $hash;
		return $hash;
	}

	/**
	 * Tests the passed password against the salted password stored in the user record
	 * 
	 * @param string $password
	 * @return boolean
	 */
	function testPassword($password) {
		$sh = new SaltedHash($this['password']);
		return $sh->compare($password);
	}


	/**
	 * Convenience function for changing the users password.  Requires that the user provides their current password, as well as a confirmation of their new password.
	 *
	 * @param string $old User's current password
	 * @param string $new The new password to be set on this account
	 * @param string $confirm Optional confirmation value, triggers a separate error if new passwords do not match.
	 * @return integer See the ERR_PASSCHANGE constants at the beginning of this file for return results.
	 */
	function changePassword($old, $new, $confirm=false) {
		if (!$this->testPassword($old)) return static::ERR_PASSCHANGE_WRONGCURRENT;
		if ($confirm !== false && $new != $confirm) return static::ERR_PASSCHANGE_WRONGCONFIRM;
		if (strlen(trim($new)) < static::PASSWORD_MIN_LENGTH) return static::ERR_PASSCHANGE_TOOSHORT;
	
		$this->setPassword($new);
		return static::ERR_PASSCHANGE_OK;
	}



	/**
	 * Tests if this user is one of the passed types
	 * 
	 * @param string|array $types
	 * @return boolean
	 */
	function isType() {
		$args = array();
		foreach (func_get_args() as $arg) {
			if (is_array($arg)) $args = array_merge($args, $arg);
			else $args[] = $arg;			
		}
		return in_array($this['type'], $args);
	}


/**
	STATIC FUNCTIONS AND METHODS
*/	

	


}