<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response, array $args) {
    $this->logger->addInfo("Opened Index page");

    $newResponse = $response->withJson([
        "version" => "1.0.0",
        "status" => "running",
    ]);

    return $newResponse;
});


//Stop the server
$app->get('/server/stop', function (Request $request, Response $response) {
    $this->logger->addInfo("Stopping the server");

    $stop_file = __DIR__ . '/.stop_server';
    touch($stop_file);

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


    return $newResponse;
});

/**
 * Delete a single job
 */
$app->get('/jobs/delete/{id}', function (Request $request, Response $response, $args) {
    $job_id = (int) $args['id'];
    $this->logger->addInfo("Deleting job " . $job_id);

    $deleted = $this->jobs->deleteJob($job_id);

    $newResponse = $response->withJson([
        "status" => $deleted ? 'OK' : 'ERROR',
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


/**
 * MANAGEMENT CONSOLE
 */


//Get the job status for all jobs in the queue
$app->get('/management', function (Request $request, Response $response) {

    return $this->view->render($response, 'index.html', [
        'name' => "cool"
    ]);

});
