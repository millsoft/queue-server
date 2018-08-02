<?php
namespace Millsoft\Queuer;
use Medoo\Medoo;

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Queuer {

	public $config = null;
	public $db = null;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->loadConfig();
		$this->loadDatabase();
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

				// [optional] Table prefix
				//'prefix' => 'PREFIX_',

				// [optional] Enable logging (Logging is disabled by default for better performance)
				//'logging' => true,

				// [optional] MySQL socket (shouldn't be used with server and port)
				//'socket' => '/tmp/mysql.sock',

				// [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
				'option' => [
					\PDO::ATTR_CASE => \PDO::CASE_NATURAL,
				],

				// [optional] Medoo will execute those commands after connected to the database for initialization
				'command' => [
					'SET SQL_MODE=ANSI_QUOTES',
				],
			]);

		} catch (Exception $e) {
			die("Error Connecting to database!\n" . $e->getMessage());
		} catch (PDOException $e) {
			die("Error Connecting to database!\n" . $e->getMessage());
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

}