<?php
namespace Millsoft\Queuer;

class Jobs extends Queuer {
	public function __construct() {
		parent::__construct();
		//die("Jobs OK");
	}

	//Get the count with new jobs (status = 0)
	public function getCountNewJobs() {
		//$sql = "SELECT count(*) as count_jobs FROM queue WHERE status=0";
		//		$sql = "SELECT * FROM events LIMIT 5";

		//$data = $this->db->query($sql)->fetchAll();

		$re = $this->db->count("queue", [
			"status" => 0,
		]);

		return (int) $re;

	}

	public function addJob($job) {
		\writelog("Adding job to queue");

		$this->db->insert("queue", [
			"status" => 0,
			"command" => "test",
		]);

	}
}