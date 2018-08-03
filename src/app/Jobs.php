<?php
namespace Millsoft\Queuer;

class Jobs extends Queuer {

	private $currentJobsWaiting = null;
	private $currentJobsWorking = null;
	private $currentJobsAll = null;

    //max threads
	public $maxThreads = 5;

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
			"max_threads" => $this->maxThreads,
			"free_threads" => $this->maxThreads - $jobs_working,
		];

		return $re;
	}

	//Add a job to the queue
	public function addJob($job) {

		\writelog("Adding job to queue");

		//$jobHash = md5(json_encode($job['command']));
		$jobHash = md5( time() . rand(1,10000) );

		$added = $this->db->insert("queue", [
			"worker" => 0,
			"worker_status" => 0,
			"command" => "",
			"job_hash" => $jobHash,
			"output" => "",
			"return_code" => null,
			"priority" => 10,
			"job" => json_encode($job),
		]);

		return $added->id;
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

		$this->db->update("queue", [
			"worker_status" => 1,
			//TODO: update other stuff, like worker_id
		], [
			"id" => $job['id'],
		]);

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



		while($jobs_count['waiting']){



		//Dispatch jobs
		if ($jobs_count['waiting'] > 0 && $jobs_count['working'] < $this->config->workers_count) {
			//Dispatch new job to worker
			\writelog("Dispatching job to worker...");

			//Get next job from the queue
			$job = $this->getJobFromQueue();
			//$worker = new Worker($job);
			$this->dispatchJob($job);
			\writelog("job dispatched");

		}

            $jobs_count = $this->getJobsCount();
        }

	}

	//Dispatch a job to a worker abd execute the worker in the background
	public function dispatchJob($job) {
		$last_worker_id = 0;
		//$job_worker_cmd = $this->config->phpCommand . ' ' . $this->config->workerScript . ' -- -j' . $job['id'];
		$logfile = __DIR__ . "/../../logs/job_" . $job['id'] . ".log";

        $job_worker_cmd = $this->config->phpCommand
            . ' ' . $this->config->workerScript
            //. ' -- -j' . $job['id']
            . ' -j' . $job['id']
            . ' > ' . $logfile;

		if ($this->config->async) {
			//Execute the script asynchronously without blocking the current process
			//$cmd = 'nohup nice -n 10 ' . $job_worker_cmd . ' & printf "%u" $!';
			$cmd = 'nohup nice -n 10 ' . $job_worker_cmd . ' >/dev/null 2>&1 &';
		} else {
			//Execute the script synchronously.
			$cmd = $job_worker_cmd;
		}

        \writelog($cmd);
		$cmd_output = shell_exec($cmd);
		\writelog($cmd_output);
	}

	//Delete all jobs in database
	public function deleteAllJobs() {
		$sql = "TRUNCATE TABLE queue";
		$this->db->query($sql);
		\writelog("all jobs in the queue has been removed");
	}
}