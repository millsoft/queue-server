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

	//Work on the current job
	private function work() {

		//Set status to "2" after the task has been performed successfully
		$status = 2;

		$re = $this->workOn("command");

		//Execute callback (if available)
		$this->workOn("callback_done");

		
		if ($re === false) {
			//Job failed. Set the status to 99
			$status = 99;
		}else{
			$status = 3;
			//TODO: remove the task from queue?
		}

		$this->setJobDone($status, $re);

	}

    /**
     * Send the job to a specific worker
     * @param $type (worker name)
     * @return bool - false if failed, array on success
     */
	private function workOn($type) {
		if (!isset($this->jobData[$type])) {
			return false;
		}

		//set default return value
		$re = false;

		$cmd = $this->jobData[$type];
		$workerClass = '\\Millsoft\\Queuer\\Workers\\' . ucfirst($cmd['type']) . 'Worker';

		if(class_exists($workerClass)){
			$W = new $workerClass();
			$re = $W->work($cmd);
		}else{
			\writelog("Worker '" . $workerClass . "' not found");
		}

		return $re;
	}


	//Set the job status to "done"
	//2 = OK, 99 = Failed
	private function setJobDone($status = 2, $output = null) {

	    if(is_array($output)){
	        $output = json_encode($output);
        }

		//save status:
		$this->db->update("queue", [
			"worker_status" => $status,
			"output" => $output,
			"time_completed" => date("Y-m-d H:i:s"),
		], [
			"id" => $this->job['id'],
		]);
	}

}