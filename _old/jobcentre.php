<?php


/*
Jobcentre.
Will see what is in the queue and start the workers.
*/

require(__DIR__ . "/../_init.php");

class Jobcentre{
	public static $f3 = null;
	public static $DB = null;
	public static $workerID = 1;
	private static $job = null;
	public static $phpCommand = "php";

	//how many workers can be running simultanously?
	public static $maxWorkers = 8;

	public static function init(){
		echo "Starting Jobcentre...\n";
	    global $f3;
	    self::$DB = $f3->db;



	    //do we have anything to do?
	    $available_jobs = self::getAvailableJobs();
	    if($available_jobs == 0){
	    	echo "Nothing to do. Exiting\n";
	    	return false;	
	    }

	    //how many workers are currently running?
	    $running_workers = self::getRunningWorkersCount();
	    if($running_workers >= self::$maxWorkers){
	    	echo "The maxium amount of workers ($running_workers) was reached. Exiting.\n";
	    	return false;
	    }


	    //how many workers can be started?
	    $workers_to_start = self::$maxWorkers - $running_workers;

	    //get the last worker ID:
	    $last_worker_id = self::getLastRunningWorkerId();

	    //now start the workers:

	    for($worker = 0 ; $worker < $workers_to_start ; $worker++){
	    	$last_worker_id++;
	    	echo "Starting Worker " . ($worker + 1) . " with ID " . $last_worker_id;

	    	//do the magic - aka Pfusch..: Start the worker in own process and background :)
	    	$workerDir = __DIR__ . "/";
	    	$workerScript = $workerDir . "thumbgen.php";
	    	$logFile = $workerDir . "log_worker_{$last_worker_id}.txt";

echo "\n";
			$cmd = 'nohup nice -n 10 ' . self::$phpCommand . ' ' . $workerScript . ' -- -i' . $last_worker_id .' > ' . $logFile . ' & printf "%u" $!';

			//echo $cmd . "\n";
			$pid = shell_exec($cmd);
			sleep(1);

			echo " - PID=" . $pid . "\n";
	    }


	}

	//get the currently running workers:
	private static function getRunningWorkersCount(){
		$sql = "SELECT count(*) as num_workers FROM queue WHERE worker_status = 1";
		$num_worker = self::$DB->exec($sql);
		$num_worker = $num_worker[0]['num_workers'];
		return $num_worker;
	}


	//get the currently running workers:
	private static function getAvailableJobs(){
		$sql = "SELECT COUNT(*) AS available_jobs FROM queue WHERE worker_status = 0;";
		$num_worker = self::$DB->exec($sql);
		$num_worker = $num_worker[0]['available_jobs'];
		return $num_worker;
	}

		//get the currently running workers:
	private static function getLastRunningWorkerId(){
		$sql = "SELECT max(worker) as max_worker FROM queue WHERE worker_status = 1";
		$max_worker = self::$DB->exec($sql);
		$max_worker = $max_worker[0]['max_worker'];
		return $max_worker;
	}




}



Jobcentre::init();