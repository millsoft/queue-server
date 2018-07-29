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
	//$memory = memory_get_usage() / 1024;
	//$formatted = number_format($memory, 3) . 'K';
	//$jobs_count = $jobs->getCountNewJobs();
	//echo "Current memory usage: {$formatted}\n";
	//echo "Jobs in queue: n";

	$jobs_count = $jobs->getCountNewJobs();
	if ($jobs_count) {
		\writelog("Jobs in queue: " . $jobs_count);
	}

});

echo "********************************\n";
echo "* Queuer Worker V1.0 by Michel *\n";
echo "********************************\n";

$jobs->addJob(null);

\writelog("Waiting for jobs...");

//$socket->listen(1337);
$loop->run();
