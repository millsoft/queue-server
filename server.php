<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 */

namespace Millsoft\Queuer;
use Nekland\Woketo\Server\WebSocketServer;

require_once __DIR__ . "/src/init.php";

$version = \getAppVersion();

echo "*******************************************\n";
echo "* Queue Server {$version} by MilMike *\n";
echo "*******************************************\n";

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
    $server = new WebSocketServer($port, '0.0.0.0');
    $webSocketHandler = new WebSocketHandler();
    $server->setMessageHandler($webSocketHandler, '/status');
    $jobs->setWebsocketHandler($webSocketHandler);

}


/**
 * QUEUE SERVER
 */
//Check the database for new jobs every 5 seconds:
$loop->addPeriodicTimer(5, function () use ($jobs) {

    //Stop the server by a file (we need a better solution, OK for now)
    $stop_file = __DIR__ . '/.stop_server';
    if(file_exists($stop_file)){
        unlink($stop_file);
        \writelog("Server was stopped by the .stop_server file");
        die();
    }


    $jobs->checkJobs();
});


//Delete all jobs if necessary: (good for tests)
//$jobs->deleteAllJobs();

//Start the loop (if no websocket was started)

if($webSocketEnabled){
    $server->setLoop($loop);
    \writelog("websocket version started");

    $server->start();
}else{
    $loop->run();
}
