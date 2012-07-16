<?php 
namespace Primal\JobQueue;

use \DB\QueuedJob;

class Dispatcher {
	
	static function Run($count = 5) {
		$tasks = QueuedJob::FetchForProcessing($count);
		
		foreach ($tasks as $task) {
			
			//split the class and function name
			list($object, $function) = explode('#',$task['function']);
			
			//load the function arguments
			$input = unserialize($task['input']);
			$output = null;
			
			//if the calling class exists and is a Task object...
			if (class_exists($object) && is_subclass_of($object, '\\Primal\\JobQueue\\Task')) {
				try {
					
					$o = new $object();
					if (is_callable(array($o, $function))) {
						if (is_array($input) && !empty($input)) {
							$output = call_user_func_array(array($o, $function), $input);
						} else {
							$output = $o->{$function}();
						}
					}
					$task['output'] = serialize($output);
					$task['status'] = 'Complete';
					$task['date_completed'] = 'now';
					$task->save();
					
				} catch (\Exception $e) {
					//if any exceptions are thrown, serialize the exception and store it in the error output
					
					$task['error'] = serialize($e);
					$task['status'] = 'Failed';
					$task['date_completed'] = 'now';
					$task->save();
					
				}
			}
			
		}
	}
	
}