<?php

include_once __DIR__ . '/../vendor/autoload.php';

$configure = new \Deimos\Flow\Configure();

$configure->debugEnable();

$configure->compile(dirname(__DIR__) . '/compile');
$configure->template(dirname(__DIR__) . '/view');

$flow = new Deimos\Flow\Flow($configure);

$flow->assign('content', 'Have fun code!');
$flow->storage = range(1, 5);

echo $flow->render('admin/bug');
