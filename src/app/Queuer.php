<?php
namespace Millsoft\Queuer;
use Medoo\Medoo;

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Queuer {

	public $config = null;
	public $db = null;
	private $statusFile = __DIR__ . "/../../.statusfile";
	protected $webSocketHandler = null;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->loadConfig();

		$maxRetries = 10;
		$retries = 0;

		while ( $this->loadDatabase() === false && $retries < $maxRetries ) {
			\writelog("Unable to connect to database, retrying...");
			flush();
			sleep(2);
			$retries++;
		 };


	}

	private function loadDatabase() {

		try {

			$this->db = new Medoo([
				// required
				'database_type' => 'mysql',
				'database_name' => $this->config->db["dbname"],
				'server' => $this->config->db["host"],
				'username' => $this->config->db["user"],
				'password' => $this->config->db["pass"],

				// [optional]
				'charset' => 'utf8',
				'port' => isset($this->config->db["port"]) ? $this->config->db["port"] : 3306,

				'option' => [
					\PDO::ATTR_CASE => \PDO::CASE_NATURAL,
				],

				// [optional] Medoo will execute those commands after connected to the database for initialization
				'command' => [
					'SET SQL_MODE=ANSI_QUOTES',
				],
			]);

		} catch (\Exception $e) {
			return false;
		} catch (\PDOException $e) {
			return false;
		}

		\writelog("Connected to the database: " . $this->config->db["dbname"]);
		return $this->db;

	}

	private function loadConfig() {
		$config_file = __DIR__ . "/../config.php";
		$config_file_dev = __DIR__ . "/../config_dev.php";

		\writelog("Loading config.php");

		require_once $config_file;
		if (file_exists($config_file_dev)) {
			require_once $config_file_dev;
			$this->config = new Config();
			\writelog("Loaded config_dev.php");

		} else {
			//Load only global config
			$this->config = new GlobalConfig();
		}
	}

	//Get the loaded configuration data
	public function getConfig(){
		return $this->config;
	}

    //Touch the status file so the websocket know something was updated
    public function updateStatusFile(){
        touch($this->statusFile);
    }

    /**
     * Get the timestamp of the status file
     * @return int timestamp
     */
    public function getStatusFileTimeStamp(){
    	if(!file_exists($this->statusFile)){
    		return 0;
    	}

    	$ti = filemtime($this->statusFile);
    	$ti = date("H:i:s", $ti);
    	return $ti;
    }

    public function setWebsocketHandler($handler){
        \writelog("Setting Websocket Connection");
        $this->webSocketHandler = $handler;
    }

    /**
     * Push data to pusher (used for the Management console)
     * @param  string $event name of the event
     * @param  array  $data  optional additional data that will also be pushed
     * @return mixed
     */
    public function pushData($event, $data = []){
        if($this->webSocketHandler === null || $this->webSocketHandler->connection === null){
              return false;
        }

        $dataToSend = [
            'event' => $event
        ];

        if(!empty($data)){
            $dataToSend['data'] = $data;
        }

        $dataToSend = json_encode($dataToSend);

        $re = $this->webSocketHandler->connection->write($dataToSend);
        return $re;
    }



}