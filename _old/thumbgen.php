<?php

/**
 * Thumbnail background generator
 * Generiert Vorschaubilder im Hintergrund
 * @Michel - 25.07.2017
 */


/**
 * Algorhitmus:
 * - Prüfe ob gerade Vorschaubilder generiert werden
 * - wenn die Prozess-Anzahl unter 3 liegt, starte einen neuen Prozess (Zahl kann varieren)
 * - Jeder dieser Threads erstellt die Vorschaubilder
 */

/*
//Thread in einem eigenen Prozess ausführen
$cmd = 'nohup nice -n 10 php -f longprocess.php > log_from_caller.txt & printf "%u" $!';
$pid = shell_exec($cmd);
 */

require(__DIR__ . "/../_init.php");

class Worker
{

    public static $f3 = null;
    public static $DB = null;
    public static $workerID = 1;
    private static $job = null;


    public static function init ($id = 0)
    {
        global $f3;
        self::$DB = $f3->db;

        //get a new item from the queue:
        self::$workerID = $id;
        self::work();


    }

    //get jobs and work..
    public static function work ()
    {

        //work until dead.. -.-
        while (1) {
            self::getJob();
            self::startWork();
        }

    }

    //get a job from queue:
    public static function getJob ()
    {

        $sql = "SELECT * FROM queue WHERE worker_status = 0 ORDER BY priority,id LIMIT 1";
        $data = self::$DB->exec($sql);

        if (empty($data)) {
            die("Nothing to do :)");
        }

        self::$job = $data[ 0 ];

        //update status to 1 so no other worker can use this job:
        $sql = "UPDATE queue SET worker_status = 1, worker = :worker WHERE id = :id";
        self::$DB->exec($sql, array(
            "worker" => self::$workerID,
            "id"     => self::$job[ 'id' ]
        ));

        echo "Working on job " . self::$job[ 'id' ] . "... ";


    }


    //OK - Lets do this!
    public static function startWork ()
    {
        if (is_null(self::$job)) {
            die("Are you fucking kidding me? No Job was specified -.-");
        }

        $command = self::$job[ 'command' ];


        $exec = exec($command, $output, $returnCode);

        $output_str = implode("\n", $output);

        //update the job:
        $job_id = self::$job[ 'id' ];
        $sql = "UPDATE queue SET worker_status = 2, output = :output,  return_code = :return_code, time_completed = now() WHERE id = {$job_id}";

        //delete job from list, but only when there was no error:

        if ($returnCode == 0) {
            //no errors occured, simply remove the item from queue:
            $sql = "DELETE FROM queue WHERE id = {$job_id}";
            self::$DB->exec($sql);

        } else {


            //echo $sql . "\n---------------\n";
            self::$DB->exec($sql, array(
                "output"      => $output_str,
                "return_code" => $returnCode
            ));
        }


        echo("DONE!\n");

        return true;

    }

}


//get the worker ID from argument:

$options = getopt("i::");
if (isset($options[ 'i' ])) {
    $id = $options[ 'i' ];
} else {
    $id = 1;
}


Worker::init($id);
