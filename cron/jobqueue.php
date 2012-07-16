<?php 

include __DIR__.'/../index.php';

Primal\JobQueue\Dispatcher::Run(10); //grab the next ten tasks to complete

//the number of tasks should be dependant on how frequently this cron executes, and the average expected duration of available tasks.
