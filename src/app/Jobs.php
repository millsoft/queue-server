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

		$jobs_waiting = $this->db->count("queue", ["worker_status" => 0]);
		$jobs_working = $this->db->count("queue", ["worker_status" => 1]);

		$re = [
			"waiting" => $jobs_waiting,
			"working" => $jobs_working,
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

	//Get a job wairing in queue
	public function getJobFromQueue() {
		$job = $this->db->get("queue", "*", [
			"worker_status" => 0,
		]);

		if (!$job) {
			return null;
		}

		//Set the id to 1 (assigned)

		/*
			$this->db->update("queue", [
				"worker_status" => 1,
				//TODO: update other stuff, like worker_id
			]);
		*/

		return $job;

	}

	//Check if there are new jobs, also starts jobs
	public function checkJobs() {
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

		if ($allJobsCount == 0 && $allJobsCount !== $this->currentJobsAll) {
			$this->currentJobsAll = $allJobsCount;
			\writelog("Nothing to do. Waiting for jobs.");
		}

		//Dispatch jobs
		if ($jobs_count['waiting'] > 0 && $jobs_count['working'] < $this->config->workers_count) {
			//Dispatch new job to worker
			\writelog("Dispatching job to worker...");
			$job = $this->getJobFromQueue();
			//$worker = new Worker($job);
			$this->work();
			\writelog("job dispatched");

		}

	}

	public function dowork() {
		\writelog("dowork wait...");
		sleep(5);
		\writelog("dowork cool");

	}

	public function work() {
		$wrk = $this->dowork();
		\writelog("working");
		$resolver = function ($wrk, callable $reject, callable $notify) {
			// Do some work, possibly asynchronously, and then
			// resolve or reject. You can notify of progress events (deprecated)
			// along the way if you want/need.
			\writelog("promissing");
			//$resolve($awesomeResult);
			die("FUCK");
			// or throw new Exception('Promise rejected');
			// or $resolve($anotherPromise);
			// or $reject($nastyError);
			// or $notify($progressNotification);
		};

		$canceller = function () {
			// Cancel/abort any running operations like network connections, streams etc.

			// Reject promise by throwing an exception
			throw new Exception('Promise cancelled');
		};

		$promise = new \React\Promise\Promise($resolver, $canceller);
	}
}