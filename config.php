<?php 

use Primal\Database\Connection;

Connection::AddLink(array(
	'method'	=>Connection::METHOD_MYSQL,
	'database'	=>'primal',
	'host'		=>'localhost',
	'username'	=>'root',
	'password'	=> null
));

$env = Environment::getInstance();
$env->setName('development');
