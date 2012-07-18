<?php 

namespace Primal;

class Session extends SessionAbstraction  {
	protected $session_key = 'Primal';
	
	function __construct($start = false) {
		if ($start || $_COOKIE[ini_get('session.name')]) $this->start();
	}
	
	protected $started = false;
	protected function start() {
		//if the session is already started, we don't need to do anything here.
		$this->started = true;
		if (isset($_SESSION['Primal'])) return;
		
		if ($session_id !== null) session_id($session_id);
		elseif (isset($_REQUEST["PHPSESSID"])) session_id($_REQUEST["PHPSESSID"]);
		//if a session ID is passed in the request, use it.	 This is to get around cookie issues with requests from inside Flash.
		
		session_start();
		if (!isset($_SESSION['Primal'])) $_SESSION['Primal'] = array(); //PHP offers no way of verifying that a session has been created, so we make the Primal array as a verify check.		
	}

	protected static $singleton;
	static function Singleton() {
		return (!static::$singleton) ? static::$singleton = new static() : static::$singleton;
	}	
	
	public function pushNotification($message, $class="notice") {
		$this['Notifications'][] = array($message, $class);
	}

	public function popNotifications() {
		$list = $this['Notifications'];
		$this['Notifications'] = array();
		return $list;
	}
	
}