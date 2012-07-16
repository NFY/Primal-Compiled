<?php 
namespace DB;
use \Primal\Database\Query;

class QueuedJob extends \Primal\Database\Record {
	var $tablename = 'job_queue';
	var $primary = array('id');
	
	public static function FetchForProcessing($count = 5, $pid = null) {		
		
		$tasks = Query::Create('job_queue')
			->whereString('status', 'Pending')
			->orderBy('`priority` DESC', 'id')
			->limit($count)
			->select(__CLASS__)
		;
		
		$task_ids = array_map(function ($n) {return $n['id'];}, $tasks);
		
		Query::Create('job_queue')
			->whereInList('id',$task_ids)
			->setString('status', 'Working')
			->setInteger('pid', $pid ?: getmypid())
			->update()
		;

		return $tasks;
		
	}
	
}

/*

CREATE TABLE IF NOT EXISTS `job_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `function` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL,
  `status` enum('Pending','Working','Complete','Failed') NOT NULL DEFAULT 'Pending',
  `date_queued` DATETIME,
  `date_completed` DATETIME,
  `pid` int(11),
  `input` text,
  `output` text,
  `error` text,
  PRIMARY KEY (`id`)
)

*/

