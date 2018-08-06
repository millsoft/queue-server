<?php

/**
 * What is this file?
 * You can use your own "dev" version of config file, simply copy this file as "config_dev.php" and
 * change the settings. This file is useful for development purposes and won't be commited using git
 * In this file you can overwrite everything stored in the config.php file.
 */

namespace Millsoft\Queuer;

class Config extends GlobalConfig {
	public $db = [
		"host" => "",
		"dbname" => "",
		"user" => "",
		"pass" => "",
		//"port" => 3306,
	];

	//how many workers can work simultanously?
	//This depends on the server power
    public $maxThreads = 5;

    public $workers_count = 1;

	public $phpCommand = 'php';

	public $workerScript = __DIR__ . '/work.php';

	//Default priority for tasks. Each task can specific its own priority.
    //Priority can be between 0 and 1000
	public $defaultPriority = 500;

}