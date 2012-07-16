<?php 
namespace Layout;

class Error extends Confirmation {
	function __construct($message) {
		$this->addClass('Error');
		parent::__construct('An Error Has Occurred', $message);
	}
}