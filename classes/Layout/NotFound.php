<?php 
namespace Layout;

class NotFound extends Error {
	function __construct($message = null) {
		header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 404 Not Found');
		parent::__construct(is_null($message)?"The requested resource could not be located":$message);
	}
}
