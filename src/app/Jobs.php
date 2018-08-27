<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 */

namespace Millsoft\Queuer;

class Jobs extends Queuer
{

    private $currentJobsWaiting = null;
    private $currentJobsWorking = null;
    private $currentJobsAll = null;

    //max threads - this will be set by the config file
    private $maxThreads = null;

    //a hash for the counts of the job counts
    //If the hash changes (eg. a job gets done) it will be pushed to the management console
    private $jobStatusHash = null;

    public function __construct()
    {
        parent::__construct();
        $this->maxThreads = $this->config->maxThreads;
    }

    //Get the count with new jobs, working jobs etc..
    public function getJobsCount()
    {

        $jobs_waiting = $this->db->count("queue", ["worker_status" => 0]);
        $jobs_working = $this->db->count("queue", ["worker_status" => 1]);
        $jobs_all = $this->db->count("queue");

        $re = [
            "all_jobs" =>(int)  $jobs_all,
            "waiting" => (int) $jobs_waiting,
            "working" => (int) $jobs_working,
            "max_threads" => (int) $this->maxThreads,
            "free_threads" => (int)  ($this->maxThreads - $jobs_working),
        ];

        $this->jobStatus = $re;

        \writelog(json_encode($re));
        return $re;
    }

    //Add a job to the queue
    public function addJob($job)
    {

        \writelog("Adding job to queue");

        $jobHash = md5(time() . rand(1, 10000));
        $priority = isset($job['priority']) ? $job['priority'] : $this->config->defaultPriority;
        $context = isset($job['context']) ? $job['context'] : $this->config->defaultContext;

        $this->db->insert("queue", [
            "worker" => 0,
            "worker_status" => 0,
            "command" => "",
            "job_hash" => $jobHash,
            "output" => "",
            "return_code" => null,
            "context" => $context,
            "priority" => $priority,
            "job" => json_encode($job),
        ]);

        $this->pushStatus();

        $this->updateStatusFile();
        //$this->pushData("update", ["status" => "add"]);
        return $this->db->id();
    }

    /**
     * Get a job waiting in the queue
     * @return null
     */
    public function getJobFromQueue()
    {
        $job = $this->db->get("queue", "*", [
            "worker_status" => 0,
            "ORDER" => ["priority" => "DESC"]
        ]);

        if (!$job) {
            return null;
        }

        //Set the id to 1 (assigned)

        $this->db->update("queue", [
            "worker_status" => 1,
        ], [
            "id" => $job['id'],
        ]);

        //Push update notification to frontend
        //$this->pushData("update", ["status" => "started"]);

        $this->updateStatusFile();
        return $job;

    }

    /**
     * Print the current job status
     */
    private function printJobStatus()
    {
        $jobs_count = $this->jobStatus;
        if ($jobs_count['waiting'] != $this->currentJobsWaiting && $jobs_count['waiting'] > 0) {
            $this->currentJobsWaiting = $jobs_count['waiting'];
            \writelog("Waiting jobs in queue: " . $jobs_count['waiting']);
        }

        if ($jobs_count['working'] != $this->currentJobsWorking && $jobs_count['working'] > 0) {
            $this->currentJobsWorking = $jobs_count['working'];
            \writelog("Working on " . $jobs_count['working'] . " jobs...");
        }

        $allJobsCount = (int)($jobs_count['waiting'] + $jobs_count['working']);

        if ($allJobsCount == 0 && $allJobsCount !== $this->currentJobsAll) {
            $this->currentJobsAll = $allJobsCount;
            \writelog("Nothing to do. Waiting for jobs.");
        }

    }

    //Check if there are new jobs, also starts jobs
    public function checkJobs()
    {
        $jobs_count = $this->getJobsCount();
        $this->pushStatus($jobs_count);

        if (!$jobs_count['waiting']) {
            return false;
        }

        while ($jobs_count['waiting']) {

            $this->printJobStatus();

            //Dispatch jobs
            if ($jobs_count['waiting'] > 0 && ($jobs_count['working'] <= $this->maxThreads) ) {
                //Dispatch new job to worker
                \writelog("Getting a job from queue");
                
                //Get next job from the queue
                $job = $this->getJobFromQueue();

                \writelog("Dispatching job {$job['id']} to worker...");

                //$worker = new Worker($job);
                $this->dispatchJob($job);


                $this->pushStatus( );

                sleep(0.2);
            } else {
                sleep(1);
            }
            //\writelog("blah");

            $jobs_count = $this->getJobsCount();
        }
        $this->pushStatus($jobs_count);
        \writelog("Queue done. Going to watch mode");


    }


    //Dispatch a job to a worker abd execute the worker in the background
    public function dispatchJob($job)
    {
        $last_worker_id = 0;
        //$job_worker_cmd = $this->config->phpCommand . ' ' . $this->config->workerScript . ' -- -j' . $job['id'];
        $logfile = __DIR__ . "/../../logs/job_" . $job['id'] . ".log";

        //Execute php with the worker script
        $job_worker_cmd = $this->config->phpCommand

            . ' ' . $this->config->workerScript
            //. ' -- -j' . $job['id']

            //Add job id param
            . ' -j' . $job['id']

            //Write a log file for current file (for debugging purposes)
            . ' > ' . $logfile;

        //$job_worker_cmd = $this->config->phpCommand . ' ' . $this->config->workerScript . ' -j' . $job['id'];
        if ($this->config->async) {
            \writelog("Starting Background Job " . $job['id']);

            //Execute the script asynchronously without blocking the current process
            $p = new BackgroundProcess($job_worker_cmd);
            $p->start();


        } else {
            //Execute the script synchronously.
            \writelog("Executing Job synchronously");
            $cmd_output = shell_exec($job_worker_cmd);

        }

    }

    /**
     * Delete all jobs in database
     */
    public function deleteAllJobs()
    {
        $sql = "TRUNCATE TABLE queue";
        $this->db->query($sql);
        \writelog("all jobs in the queue has been removed");
    }

    /**
     * Delete a single job
     * @param $job_id
     * @return bool
     */
    public function deleteJob($job_id)
    {
        $deleted = $this->db->delete("queue", array(
            "id" => $job_id
        ));
        
        $this->updateStatusFile();

        if($deleted->rowCount()){
            \writelog("Jobs {$job_id} has been deleted");
            return true;
        }else{
            \writelog("Jobs {$job_id} could not be deleted");
            return false;
        }
    }



    /**
     * Get the current status for a job
     * @param null $job_id
     * @return mixed - null or array with data
     */
    public function getStatus($job_id = null)
    {
        $job_status = $this->db->get("queue", [
            "id", "worker_status", "time_added", "time_completed", "priority"
        ], [
            "id" => $job_id
        ]);

        return $job_status;

    }


    /**
     * Get a single or multiple jobs
     * @param  mixed $job_id id of the job or the string of the status: waiting, working, done, failed.
     * @return array
     */
    public function getJob($job_id, $params = []){

        $limit = isset($params['limit']) ? $params['limit'] : null;

        if(is_numeric($job_id)){
            $jobs = $this->db->get("queue", "*", [
                "id" => $job_id,
            ]);
        }else{
            //Search by string
            $statusMap = [
                'waiting' => 0,
                'working' => 1,
                'done' => 3,
                'failed' => 99,
            ];

            $queryParams = [
                "ORDER" => [
                    "time_added" => "DESC",
                    "time_completed" => "ASC",
                ],
                "LIMIT" => $limit
            ];

            if($job_id == 'all'){
                //Get all jobs

            }else{
                //get jobs with specific status
                $workerStatus = isset($statusMap[$job_id]) ? $statusMap[$job_id] : null;

                if($workerStatus === null){
                    //Not recognized string
                    return false;
                }

                $queryParams['worker_status'] = $workerStatus;

            }

            $jobs = $this->db->select("queue", "*", $queryParams);
        }


        //unserialize job:
        if(!empty($jobs)){
            foreach($jobs as &$job){
                $job['job'] = json_decode($job['job'], true);
            }
        }

        return $jobs;

    }

    /**
     * Push the current job status to websocket
     * @param $jobStatus
     * @return bool
     */
    public function pushStatus($jobStatus = null){

        if($jobStatus === null ){
            $jobStatus = $this->getJobsCount();
        }

        $newJobStatusHash = md5(json_encode($jobStatus));

        if($newJobStatusHash == $this->jobStatusHash){
            //Do not push anything because the status didn't change
            return false;
        }

        $this->pushData("update", $jobStatus);
        $this->jobStatusHash = $newJobStatusHash;
        return true;
    }
}