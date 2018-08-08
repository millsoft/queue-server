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


// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ .  '/ui', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};