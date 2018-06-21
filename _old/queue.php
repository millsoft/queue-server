<?php

/*
Queue Checker.
By Michel - 08.09.2017

Das Script läuft ununterbrochen im Hintergrund. Prüft ob es etwas in der Queue zu tun gibt
Und leitet startet dann jobcentre.php der dann die nötigen Jobs verteilt.
*/


set_time_limit(0);
require(__DIR__ . "/../_init.php");

class Queue{

	  public static $f3 = null;
	  public static $DB = null;
	  public static $phpCommand = "php";


	public static function init($id = 0){
	    global $f3;
	    self::$DB = $f3->db;

	    self::run();
	  }
	  


	//Run the Queue checker
	public static function run(){

		//add some Verschnaufpause:

    	$workerDir = __DIR__ . "/";
    	$jobCentreScript = $workerDir . "jobcentre.php";
    	$logFile = $workerDir . "log_queue.txt";

		
		while(1){
			$has_jobs = self::hasJobs();

			if($has_jobs){
				//check if JobCentre or thumbgen process already running
				$command = "ps -cax | grep 'jobcentre\|thumbgen' | grep -o '^[ ]*[0-9]*'";
				exec($command, $output, $return);

				if($return != 0){
					//no running workers found, start the Jobcentre now:
					$cmd = 'nohup nice -n 10 ' . self::$phpCommand .  ' ' . $jobCentreScript . ' 2> /dev/null > ' . $logFile . ' & printf "%u" $!';
					$pid = shell_exec($cmd);
				}
			}

			sleep(5);
		}

	}

	//check if we have any jobs waiting to be processed
	private static function hasJobs(){
		//do we have at least one job to do?
		$sql = "SELECT id FROM queue WHERE worker_status = 0 LIMIT 1";
		$todo = self::$DB->exec($sql);
		return empty($todo) ? false : true;
	}
}


echo "Queue watcher 1.0 - by Michel\n";
Queue::init();
