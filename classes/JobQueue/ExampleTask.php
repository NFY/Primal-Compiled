<?php 

namespace JobQueue;

class ExampleTask extends Primal\JobQueue\Task {
	
	public function run ($foo) {
		return "This is some $foo, right here.";
	}
	
}

/*
Usage:

$job = new \JobQueue\ExampleTask();
$job->dispatch('garbage');

*/
