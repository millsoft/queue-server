<?php
namespace Millsoft\Queuer;

require_once __DIR__ . "/src/init.php";
$jobs = new Jobs();
//$jobs->checkJobs();

/*
$app = function ($request, $response) {
$response->writeHead(200, array('Content-Type' => 'text/plain'));
$response->end(print_r($request, 1));
};
 */

$loop = \React\EventLoop\Factory::create();
//$socket = new \React\Socket\Server('[::1]:1337', $loop);
//$http = new \React\Http\Server($socket, $loop);

//$http->on('request', $app);
//echo "Server running at http://127.0.0.1:1337\n";

//Check the database for new jobs every x seconds:
$loop->addPeriodicTimer(5, function () use ($jobs) {
	$jobs->checkJobs();

});

echo "******************************************\n";
echo "* Queuer Server V1.0 by Michael Milawski *\n";
echo "******************************************\n";

//At first delete all jobs
$jobs->deleteAllJobs();

//Add a mock job for dev purposes:
$jobs->addJob([
	"command" => [
		"type" => "http",
		"url" => "http://www.cool.de",
		"method" => "post",
		"params" => [
			"name" => "Michel",
			"test" => "12345",
		]
	],

	//Callback after the job was completed (POST Request)
	//This can tell the application that something was done.
	"callback_done" => [
		"url" => "https://www.example.com/jobdone",
		"params" =>
		[
			"jobname" => "send_newsletters",
			"id" => 12345,
			"id_persons" => 56789,
		],
	],

]);

//$socket->listen(1337);
$loop->run();
