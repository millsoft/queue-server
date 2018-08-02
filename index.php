<?php

namespace Millsoft\Queuer;

/**
 * Queue Server Web Server
 * By Michael Milawski
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';


$Q = new Queuer();
$config = (array) $Q->getConfig();

$app = new \Slim\App(['settings' => $config]);
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];

	//throw new \Exception("FUCK");
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->write("MilMike Queue Server");
    return $response;
});



$app->run();