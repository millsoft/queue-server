<?php

/**
 * Worker Module
 * This handles the job in the background
 */

namespace Millsoft\Queuer;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Worker extends Queuer {
	private $job = null;
	private $jobData = null;

	private $returnData = [];

	public function __construct($job_id) {
		parent::__construct();
		$this->loadJob($job_id);
	}

	private function loadJob($job_id) {
		$this->job = $this->db->get("queue", "*", [
			"id" => $job_id,
		]);

		if (!$this->job) {
			\writelog("Job " . $job_id . " not found");
			exit(404);
		}

		$this->jobData = json_decode($this->job['job'], true);

		$this->validateJob();
		$this->work();
	}

	private function validateJob() {
		$errors = [];

		if (!isset($this->jobData['command'])) {
			$errors[] = "'command' key not found in the job";
		} else {
			if (!is_array($this->jobData)) {
				$errors[] = "'command' should be array";
			} else {
				$cmd = $this->jobData['command'];
				if (!isset($cmd['type'])) {
					$errors[] = "Command 'type' is missing";
				}

			}
		}

		if (!empty($errors)) {
			echo implode("\n", $erros);
			die();
		}

	}

	private function work() {

		//Set status to "2" after the task has been performed successfully
		$status = 2;

		$re = $this->workOn("command");
		$this->workOn("callback_done");

		if ($re === false) {
			$status = 99;
		}
		$this->setJobDone($status);

	}

	private function workOn($type) {
		if (!isset($this->jobData[$type])) {
			return false;
		}

		$cmd = $this->jobData[$type];

		if ($cmd['type'] == 'http') {
			$re = $this->doHttp($cmd);
		}

		return $re;
	}

	//Execute a HTTP task
	private function doHttp($cmd) {

		echo "Doing HTTP to " . $cmd['url'] . "\n";

		//Store return data here temporary:
		$return = [];

		$timeout = isset($cmd['timeout']) ? $cmd['timeout'] : (isset($this->config->httpTimeout) ? $this->config->httpTimeout : 10);

		$client = new Client([
			'timeout' => $timeout,
		]);

		$method = strtoupper(isset($cmd['method']) ? $cmd['method'] : 'GET');

		$params = array();
		if (isset($cmd['params'])) {
			$params['form_params'] = $cmd['params'];
		}

		try {

			$response = $client->request($method, $cmd['url'], $params);

		} catch (\GuzzleHttp\Exception\RequestException $e) {
			//echo "ERROR: " . \GuzzleHttp\Psr7\str($e->getRequest());
			//die("ERROR!!!");

			if ($e->hasResponse()) {
				//echo Psr7\str($e->getResponse());
				$return['response'] = $e->getResponse();
			}

			\writelog("Error! " . $e->getMessage());
			return false;
		}

		if (isset($response) && $response !== null) {
			$return['status_code'] = $response->getStatusCode();
			$return['body'] = $response->getBody();
		}

		//echo $response->getBody();

		\writelog("HTTP done");
	}

	//Set the job status to "done"
	//2 = OK, 99 = Failed
	private function setJobDone($status = 2) {
		//save status:
		$this->db->update("queue", [
			"worker_status" => $status,
		], [
			"id" => $this->job['id'],
		]);
	}

}