<?php 

namespace API;

class Bounce extends \Primal\RESTController {
	
	public $supported = array("GET","POST","PUT","DELETE","HEAD","OPTIONS","TRACE","CONNECT");

	function request($method) {
		$this->output = array_merge($this->request->data, $this->output);
		return true;
	}

}
