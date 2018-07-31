<?php
namespace Millsoft\Queuer;

class Jobs extends Queuer {

	private $currentJobsWaiting = null;
	private $currentJobsWorking = null;
	private $currentJobsAll = null;

	public function __construct() {
		parent::__construct();
		//die("Jobs OK");
	}

	//Get the count with new jobs, working jobs etc..
	public function getJobsCount() {

		$jobs_waiting = $this->db->count("queue", [ "worker_status" => 0 ]);
		$jobs_working = $this->db->count("queue", [ "worker_status" => 1 ]);

		$re = [
			"waiting" => $jobs_waiting,
			"working" => $jobs_working
		];

		return $re;
	}

	public function addJob($job) {
		\writelog("Adding job to queue");

		$jobHash = md5($job['command']);

		$this->db->insert("queue", [
			"worker" => 0,
			"worker_status" => 0,
			"command" => "",
			"job_hash" => $jobHash,
			"output" => "",
			"return_code" => null,
			"priority" => 10,
		]);
	}

	//Check if there are new jobs, also starts jobs
	public function checkJobs(){
		$jobs_count = $this->getJobsCount();

		if ($jobs_count['waiting'] != $this->currentJobsWaiting && $jobs_count['waiting'] > 0) {
			$this->currentJobsWaiting = $jobs_count['waiting'];
			\writelog("Waiting jobs in queue: " . $jobs_count['waiting']);
		}

		if ($jobs_count['working'] != $this->currentJobsWorking && $jobs_count['working'] > 0) {
			$this->currentJobsWorking = $jobs_count['working'];
			\writelog("Working on " . $jobs_count['working'] . " jobs...");
		}

		$allJobsCount = (int) ($jobs_count['waiting'] + $jobs_count['working']);

		if($allJobsCount == 0 && $allJobsCount !== $this->currentJobsAll){
			$this->currentJobsAll = $allJobsCount;
			\writelog("Nothing to do. Waiting for jobs.");
		}

	}
}