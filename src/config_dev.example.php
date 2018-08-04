<?php

namespace Millsoft\Queuer;

class Config extends GlobalConfig {
	public $db = [
		"host" => "",
		"username" => "",
		"password" => "",
		"database" => "",
		"port" => 3306, //optional, default 3306
	];

	//how many workers can work simultanously?
	//This depends on the server power
	public $workers_count = 1;

	public $workers_count = 1;

	public $phpCommand = 'php';

	public $workerScript = __DIR__ . '/work.php';

	//Default priority for tasks. Each task can specific its own priority.
    //Priority can be between 0 and 1000
	public $defaultPriority = 500;

}