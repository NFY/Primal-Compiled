<?php

use \Primal\Visitor;

Visitor::Logout();

$response = new Primal\Response();
$response->redirect('/');

