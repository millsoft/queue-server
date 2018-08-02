<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require_once __DIR__ . "/src/initapp.php";

$container->logger->addInfo("Hello World");

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
	$name = $args['name'];

	//throw new \Exception("Test error");
	$response->getBody()->write("Hello, $name");
	return $response;
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $this->logger->addInfo("Opened Index page");

    $newResponse = $response->withJson([
    	"version" => "1.0.0",
    	"status" => "running",
    ]);

    //$newResponse->withHeader('Content-type', 'application/json');

	return $newResponse;
});


$app->post('/jobs/add', function (Request $request, Response $response) {
    $this->logger->addInfo("Adding a job");

    $payload = $request->getParsedBody();

    $job_id = $this->jobs->addJob($payload);

    $newResponse = $response->withJson([
    	"status" => "OK",
    	"job_id" => $job_id
    ]);



    //print_r($parsedBody);


	return $newResponse;
});



$app->run();