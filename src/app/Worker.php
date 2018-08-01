<?php

/**
 * Worker Module
 * This handles the job in the background
 */

namespace Millsoft\Queuer;

use GuzzleHttp\Client;

class Worker extends Queuer {
	private $job = null;
	private $jobData = null;

	public function __construct($job_id) {
		parent::__construct();
		$this->loadJob($job_id);
	}

	private function loadJob($job_id){
		$this->job = $this->db->get("queue", "*", [
			"id" => $job_id
		]);

		if(!$this->job){
			\writelog("Job " . $job_id . " not found");
			exit (404);
		}


		$this->jobData = json_decode($this->job['job'], true);

		$this->validateJob();
		$this->work();
	}

	private function validateJob(){
		$errors = [];

		if(!isset($this->jobData['command'])){
			$errors[] = "'command' key not found in the job";
		}else{
			if(!is_array($this->jobData)){
				$errors[] = "'command' should be array";
			}else{
				$cmd = $this->jobData['command'];
				if(!isset($cmd['type'])){
					$errors[] = "Command 'type' is missing";
				}

			}
		}

		if(!empty($errors)){
			echo implode("\n", $erros);
			die();
		}

	}

	private function work(){
		$cmd = $this->jobData['command'];

		if($cmd['type'] == 'http'){
			$this->doHttp($cmd);
		}


	}

	private function doHttp($cmd){
		echo "Doing HTTP to " . $cmd['url'] . "\n";

		$client = new Client([
			// Base URI is used with relative requests
			//'base_uri' => $cmd['url'],
			// You can set any number of default request options.
			'timeout'  => 5.0,
		]);

		$re = $client->get($cmd['url']);

		print_r($re);

	}




}