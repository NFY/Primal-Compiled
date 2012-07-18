<?php

/**
 * Primal Visitor Controller
 *
 * This class provides global functions for interacting with the current visitor's login session.\
 * As this class interacts almost exclusively with data in the global $_SESSION array, it has been designed
 * to also exist in the global namespace and use all static functions.
 * 
 * @package Primal
 * @author Jarvis Badgley
 * @copyright 2008 - 2012 Jarvis Badgley
 */

namespace Primal;
use \DB\User;

class Visitor {

	const COOKIE_EXPIRE = 365;
	const ERR_LOGIN_OK = 0;
	const ERR_LOGIN_BADUSER = 1;
	const ERR_LOGIN_BADPASS = 2;
	
	/**
	 * Current user cache.  This is populated the first time Visitor::Current is called and persists until the end of execution.
	 * @var User object
	 * @static
	 * @access private
	 */
	static $current;
	
	/**
	 * Checks if a user is currently signed on to the site.
	 *
	 * @static
	 * @return boolean True if a user is currently listed in the session
	 * @access public
	 */
	public static function LoggedIn() 		{
		return $_SESSION['Primal']['Visitor']['ID']?true:false;
	}
	
	
	/**
	 * Returns the current ID number of the logged in user.
	 *
	 * @static
	 * @return integer
	 * @access public
	 */
	public static function CurrentID()		{
		return (int)$_SESSION['Primal']['Visitor']['ID'];
	}
	
	/**
	 * Returns the current user object or user data
	 *
	 * @static
	 * @param string $key Optional column name from the users table.
	 * @param boolean $fresh Optional. If true, the function will fetch a fresh record from the database instead of using the record in cache
	 * @return mixed If $key is a string, function will return the matching column from the User record, otherwise it will return the User object.
	 * @access public
	 */
	public static function Current($key=null, $fresh=false) {
		if (!static::LoggedIn()) return null;
		if ($fresh || !static::$current) static::$current = new User(static::CurrentID());
		
		if ($key && is_string($key)) return static::$current[$key];
		else return static::$current;
	}

	/**
	 * Set the login session using user credentials
	 *
	 * @static
	 * @param string $email User's email address
	 * @param string $password User's password
	 * @param boolean $setcookie Optional, if true it will also set a cookie login value for auto-signin on session loss
	 * @return integer See the Visitor::ERR_LOGIN constants for return results.
	 * @access public
	 */
	public static function LoginWithEmail ($email, $password, $setcookie=false) {
		$u = new User();
		$u->load($email, 'email');
		if (!$u->found) return static::ERR_LOGIN_BADUSER;
		if (!$u->testPassword($password)) return static::ERR_LOGIN_BADPASS;

		return static::LoginWithUser($u, $setcookie);
	}
	
	/**
	 * Set the login session using a user object
	 *
	 * @static
	 * @param DB\User $user
	 * @param boolean $setcookie Optional, if true it will also set a cookie login value for auto-signin on session loss
	 * @return integer See the Visitor::ERR_LOGIN constants for return results.
	 * @access public
	 */
	public static function LoginWithUser ($user, $setcookie=false) {
		$_SESSION['Primal']['Visitor']['ID'] = (int)$user['id'];
		
		$user->handleLogin();
		
		if ($setcookie) {
			setcookie('vi', $user['id'], time()+ 60*60*24 * static::COOKIE_EXPIRE, '/');
			setcookie('vp', sha1($user['password']), time()+ 60*60*24 * static::COOKIE_EXPIRE, '/');
		}
		
		return ERR_LOGIN_OK;		
	}
	
	/**
	 * Removes the current user from the active session and kills the login cookie.
	 *
	 * @static
	 * @access public
	 */
	public static function Logout () {
		$_SESSION['Primal']['Visitor'] = array();
		static::$current = null;
		setcookie('vi', '', 0, '/');
		setcookie('vp', '', 0, '/');
	}
	
	/**
	 * Validates the current user's login credentials and redirects to the login form if they do not have access to the requested page.
	 * This function is intended to be called at the top of any pages that require a user be logged in.
	 *
	 * @static
	 * @param string $type Optional user type (part of the table schema) to test against.  Use this to validate admin users on admin only pages.
	 * @access public
	 */
	static function Validate() {
		if (!static::LoggedIn()) {

			static::ForceLogin("You must be logged-in to access that page.", $_SERVER['REQUEST_URI']);
			
		} elseif (func_num_args()) {

			if (!static::Current()->isType(func_get_args())) {
				if (class_exists('\\Primal\\Layout\\Page\\Error')) {
					$page = new Primal\Layout\Page\Error("You do not have permission to view this page."); 
					// Error is a Page template for displaying error messages.  
					// This is a cleaner option to calling die() and stops page execution.
				} else {
					return die("You do not have permission to view this page.");
				}
			}

		}
	}
	

	/**
	 * Redirects the page to the login form, displaying the passed message and redirecting to the passed url after successful login
	 *
	 * @param string $message optional message to display
	 * @param string $return_url optional url to redirect to after successful login.
	 * @return void
	 */
	static function ForceLogin($message = null, $return_url=null) {
		if ($message !== null) $_SESSION['Primal']['LoginMessage'] = $message;
		if ($return_url !== null) $_SESSION['Primal']['LoginRequest'] = $return_url;
		
		header("Location: /login/");
		exit;
	}
	
	/**
	 * Returns any active login message
	 *
	 * @return string|null
	 */
	static function GetLoginMessage() {
		return isset($_SESSION['Primal']['LoginMessage']) ? $_SESSION['Primal']['LoginMessage'] : null;
	}

	/**
	 * Returns any active login redirect
	 *
	 * @return string|null
	 */
	static function GetLoginRedirect() {
		return isset($_SESSION['Primal']['LoginRequest']) ? $_SESSION['Primal']['LoginRequest'] : null;
	}
	
	/**
	 * Resets the login message and login redirect
	 *
	 * @return void
	 */
	static function ClearLoginBounce() {
		unset($_SESSION['Primal']['LoginMessage']);
		unset($_SESSION['Primal']['LoginRequest']);
	}

}


// The following code is executed when the Visitor class is loaded.  It tests if a user is logged in to the current session.
// If no user is logged in, it checks for a login cookie and sets the current login to the user defined in the cookie.
// It also refreshes the timeout on the cookie so that it wont expire.

if (!$_SESSION['Primal']['Visitor']['ID'] && isset($_COOKIE['vi']) && isset($_COOKIE['vp'])) { //no logged in user, but a cookie exists
	
	if (!isset($_SESSION['Primal']) || !isset($_COOKIE[ini_get('session.name')])) {
		session_start();
		if (!isset($_SESSION['Primal'])) $_SESSION['Primal'] = array(); //PHP offers no way of verifying that a session has been created, so we make the Primal array as a verify check.
	}
	
	$u = new User($_COOKIE['vi']);
	if ($u && $_COOKIE['vp']===sha1($u['password'])) {
		Visitor::LoginWithUser($u);
	}
}

