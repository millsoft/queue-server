<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 */

namespace Millsoft\Queuer;

use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;

echo "*******************************************\n";
echo "* Queue Server V0.1.0 by Michael Milawski *\n";
echo "*******************************************\n";

require_once __DIR__ . "/src/init.php";
$jobs = new Jobs();

//Here are the configs from your config file, if you need them somewhere:
//$config = $jobs->config;

$port = isset($jobs->config->webSocketPort) ? $jobs->config->webSocketPort : 1337;
$webSocketEnabled = isset($jobs->config->webSocket) && $jobs->config->webSocket ? true : false;

$loop = \React\EventLoop\Factory::create();

/**
 * WEBSOCKET SERVER
 */

if($webSocketEnabled){
	\writelog("Starting WebSocket Server in port " . $port);

	/*
	$app = new \Ratchet\App('0.0.0.0', $port);
	$app->route('/status', new \Millsoft\Queuer\WebSocketServer);
	//$app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
	$app->run();
	*/
/*
$loop->addTimer(10, function() use($port){

	\writelog("starting WebSocketServer");

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new \Millsoft\Queuer\WebSocketServer()
            )
        ),
        $port
    );
	$server->run();


});
*/

}


/*
if($webSocketEnabled){

	\writelog("Starting WebSocket Server in port " . $port);
	$socket = new \React\Socket\Server($port, $loop);
	$socket->on('connection', function ($conn) {

		$conn->write("OK");

	    // Event listener for incoming data
	    $conn->on('data', function ($data) use ($conn)  {
	        // Write data back to the connection
	        //$data = "DATA";
	        
	        //$data = "HTTP/1.1 200 OK\n";
	        $conn->write($data);

	        \writelog("Websocket Request");
	    });
	});
}

*/

/**
 * QUEUE SERVER
 */






//Check the database for new jobs every 5 seconds:
$loop->addPeriodicTimer(5, function () use ($jobs) {
    
	//Stop the server by a file (we need a better solution, OK for now)
    $stop_file = __DIR__ . '/.stop_server';
    if(file_exists($stop_file)){
        unlink($stop_file);
        die("Server was stopped by the .stop_server file\n");
    }
	$jobs->checkJobs();
});


//Delete all jobs if necessary: (good for tests)
//$jobs->deleteAllJobs();

$loop->run();
