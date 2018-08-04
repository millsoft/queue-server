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
echo "* Queuer Server V1.0 by MilMike          *\n";
echo "******************************************\n";

//At first delete all jobs
$jobs->deleteAllJobs();


/*
//Add a mock job for dev purposes:
$jobs->addJob([
	"command" => [
		"type" => "http",
		"url" => "http://httpbin.org/put?job=longjob",
		"method" => "PUT",
		"timeout" => 5,
		"params" => [
			"name" => "Michel",
			"test" => "12345",
		],

	],

	//Callback after the job was completed (POST Request)
	//This can tell the application that something was done.
	"callback_done" => [
		"type" => "http",
		"url" => "http://httpbin.org/get?job=done",
		"method" => "GET",
		"params" => [
			"name" => "Michel",
			"test" => "12345",
		],
	],

]);
*/



//$socket->listen(1337);
$loop->run();
