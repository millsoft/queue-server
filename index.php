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

	return $newResponse;
});


$app->post('/jobs/add', function (Request $request, Response $response) {
    $this->logger->addInfo("Adding a job");

    $payload = $request->getParsedBody();

    for ($x = 0 ; $x < 10; $x++){
        $job_id = $this->jobs->addJob($payload);
    }

    $newResponse = $response->withJson([
    	"status" => "OK",
    	"job_id" => $job_id
    ]);


	return $newResponse;
});



//Get the job status for a specific job
$app->get('/jobs/status/{id}', function (Request $request, Response $response, array $args) {
    $job_id = (int) $args['id'];
    $status = $this->jobs->getStatus($job_id);
    $status_code = 200;

    if(!$status){
        $status_code = 404;
        $status = 'Job not found';
    }

    $status = [
        'status'  => $status_code,
        'data'  => $status,
    ];

    $newResponse = $response->withJson($status, $status_code);
    return $newResponse;
});


//Get the job status for all jobs in the queue
$app->get('/jobs/status', function (Request $request, Response $response) {

    $status = $this->jobs->getJobsCount();
    $status = [
        'status'  => 200,
        'data'  => $status,
    ];

    $newResponse = $response->withJson($status);
    return $newResponse;
});





//Middleware - will be used later for auth
$app->add(function ($request, $response, $next) {
    //$response->getBody()->write('BEFORE');
    $response = $next($request, $response);
    //$response->getBody()->write('AFTER');
    return $response;
});


$app->run();