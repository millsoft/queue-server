<?php
namespace Millsoft\Queuer;

class GlobalConfig {
	public $db = [];

	//how many workers can work simultanously?
	//This depends on the server power
	public $workers_count = 1;

	//should the jobs be executed asynchronously or synchronously?
	//async = true works currently only on linux, so set it to false when you work on windows.
	public $async = true;

	public $phpCommand = 'php';

	public $workerScript = __DIR__ . '/../work.php';


}