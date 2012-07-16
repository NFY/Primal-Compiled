<?php 

namespace API;

use \Primal\Visitor;

class Auth extends \Primal\RESTController {
 
	public $supported = array("GET","POST");

	function request($method) {
		if (!$this->validUser()) return false;

		$this->output['name'] = array(
			'full'=>$this->user->fullName(),
			'first'=>$this->user['firstname'],
			'last'=>$this->user['lastname']
		);
		
		if ($this->request['token']) {
			$t = new \DB\APIAuthToken();
			$t['token'] = md5(uniqid());
			$t['user_id'] = $this->user['user_id'];
			$t['source_ip'] = $this->request->ip;
			$t->save();
			
			$this->output['token'] = $t['token'];
		}
		
		if ($this->request['session']) {
			$this->route->startSession();
			Visitor::LoginWithUser($this->user);
			$this->output['session']['PHPSESSID'] = session_id();
		}

		return true;
    }

}
