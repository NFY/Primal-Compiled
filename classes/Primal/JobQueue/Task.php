<?php 
namespace Background;

use \DB\QueuedJob;

abstract class Task {
	private $function = 'run';
	private $priority = 0;
	
	abstract public function run();
	
	public function dispatch() {
		
		$o = new QueuedJob();
		$o['function'] = get_called_class().'#'.$this->function;
		$o['priority'] = $this->priority;
		$o['input'] = serialize(func_get_args());
		$o['date_queued'] = 'now';
		$o->save(true);
		
		return $o['id'];
	}

}