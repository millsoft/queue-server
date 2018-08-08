<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 */

namespace Millsoft\Queuer;

echo "*******************************************\n";
echo "* Queue Server V0.0.1 by Michael Milawski *\n";
echo "*******************************************\n";

require_once __DIR__ . "/src/init.php";
$jobs = new Jobs();

//Here are the configs from your config file, if you need them somewhere:
//$config = $jobs->config;

$loop = \React\EventLoop\Factory::create();

//Check the database for new jobs every 5 seconds:
$loop->addPeriodicTimer(5, function () use ($jobs) {
    
	//Stop the server by a file (we need a better solution, OK for now)
    $stop_file = __DIR__ . '/.stop_server';
    if(file_exists($stop_file)){
    	die("Server was stopped by the .stop_server file");
    	unlink($stop_file);
    }

	$jobs->checkJobs();
});


//Delete all jobs if necessary: (good for tests)
//$jobs->deleteAllJobs();

$loop->run();
