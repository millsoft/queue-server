<?php
namespace Millsoft\Queuer;

class GlobalConfig {
	public $db = [];

	//how many workers can work simultanously?
	//This depends on the server power
	public $workers_count = 1;

}