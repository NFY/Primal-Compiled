<?php 

namespace Primal;

class JSONErrorHandler {
	
	private static $initialized = false;
	
	function __construct() {
		if (static::$initialized) return;
		
		static::$initialized = true;
		
		ini_set('html_errors', 'off');
		
		set_exception_handler(function ($ex) {
			header('Content-type: application/json', 500);
			echo json_encode(array(
				'crash'=>array(
					'type'=>'Unhandled '.get_class($ex),
					'level'=>$ex->getCode(),
					'message'=>$ex->getMessage(),
					'file'=>$ex->getFile(),
					'line'=>$ex->getLine(),
					'trace'=>$ex->getTrace()
				)
			));
			exit;
		});

		set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
			header('Content-type: application/json', 500);
			echo json_encode(array(
				'crash'=>array(
					'type'=>'RuntimeError',
					'level'=>$errno,
					'message'=>$errstr,
					'file'=>$errfile,
					'line'=>$errline,
					'context'=>$errcontext,
					'trace'=>debug_backtrace()
				)
			));
			exit;
		}, E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	}
	
	public static function Init() {
		return new static();
	}
}
