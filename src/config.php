<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 *
 * This file is the main configuration file. This file will always be loaded.
 * You can use also another config: config_dev.php. In this file you can overwrite all these settings
 * here without changing the config.php
 */

namespace Millsoft\Queuer;

class GlobalConfig {

    //Database configuration: (MYSQL)
    //See file assets/database/queueserver.sql for database dump.

	public $db = [
		"host" => "0.0.0.0",
		"dbname" => "queue",
		"user" => "root",
		"pass" => "",
	];

	//how many workers can work simultanously?
	//This depends on the server power
	public $maxThreads = 5;

	//default name of the app or job subject. Each job can set its own context
	public $defaultContext = 'default';

	//should the jobs be executed asynchronously or synchronously?
	//async = true works currently only on linux, so set it to false when you work on windows.
	public $async = true;

	//the "php" command with which the workers should be executed
	public $phpCommand = 'php';

	//Default timeout for HTTP requests - can be overriden for each job
	public $httpTimeout = 3600;

	public $workerScript = __DIR__ . '/../work.php';

	public $displayErrorDetails = true;

	public $addContentLengthHeader = false;


	//Run the web controller server
    //Web Controller is server.php but also with the possibility to directly communicate with it over web
	public $webSocket = false;
	public $webSocketPort = 8080;
	public $webSocketServer = "localhost";
	public $webSocketSecure = false;


}