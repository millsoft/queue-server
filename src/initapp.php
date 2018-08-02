<?php

namespace Millsoft\Queuer;

/**
 * Queue Server Web Server
 * By Michael Milawski
 */


require __DIR__ . '/../vendor/autoload.php';



$Q = new Queuer();
$Jobs = new Jobs();
$config = (array) $Q->getConfig();

$app = new \Slim\App(['settings' => $config]);

//Load libs:
$container = $app->getContainer();

$container['db'] = function ($c) use ($Q) {
	return $Q->db;
};

$container['jobs'] = function ($c) use ($Jobs) {
	return $Jobs;
};

$container['logger'] = function ($c) {
	$logger = new \Monolog\Logger('queue');
	$logdir = __DIR__ . "/../logs";
	$logfile = date("Y-m-d") . '.log';
	$file_handler = new \Monolog\Handler\StreamHandler($logdir . '/' . $logfile);
	$logger->pushHandler($file_handler);
	return $logger;
};
