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

}