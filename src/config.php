<?php
namespace Millsoft\Queuer;

class GlobalConfig {
	public $db = [
		"host" => "",
		"dbname" => "",
		"user" => "",
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

}