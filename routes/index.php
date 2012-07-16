<?php

$page = new Primal\Layout\Page();
$page->setTitle('Primal Placeholder Title');
$page->addMeta('keywords', '');
$page->addMeta('description', '');
$page->addClass('Index');
$page->start();

$v = new Primal\Layout\View('index');
$v->output();

$page->stop();