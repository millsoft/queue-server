<?php

use \Millsoft\Queuer\Jobs;
use \Codeception\Util\Fixtures;

class JobTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public $job_id = null;

    /**
     * @var Jobs $jobs
     */
    public $jobs = null;

    protected function _before()
    {
        $this->jobs = new Jobs();
    }

    protected function _after()
    {
    }

    // tests
    public function testJobsCount()
    {
        $count = $this->jobs->getJobsCount();

        $this->assertTrue(  is_array($count)  );
        $this->assertTrue(  !empty($count)  );
        $this->assertTrue( isset($count['waiting'])  );
        $this->assertTrue( isset($count['working'])  );
        $this->assertTrue( isset($count['max_threads'])  );
        $this->assertTrue( isset($count['free_threads'])  );

        codecept_debug($count);
    }

    public function testAddJob(){
        $job = array(
            "priority" => 200,
            "context" => "test",
            "command" => array(
                "type" => "http",
                "url" => "http://www.cool.de",
            )
        );

        $job_id = $this->jobs->addJob($job);
        Fixtures::add("job_id", $job_id);

        $this->assertTrue(is_numeric($job_id), "Add job");
    }

    /**
     * @depends testAddJob
     */
    public function testCheckJob(){

        $job_id = Fixtures::get("job_id");

        //get status:
        codecept_debug("Job ID = " . $job_id);
        $status = $this->jobs->getStatus($job_id);
        codecept_debug($status);

        $this->assertNotNull($status);
        $this->assertTrue(is_array($status));
        $this->assertArrayHasKey("id", $status);
        $this->assertArrayHasKey("worker_status", $status);
        $this->assertArrayHasKey("time_added", $status);
        $this->assertArrayHasKey("time_completed", $status);
    }

    /**
     * @depends testCheckJob
     */
    public function testDeleteJob(){
        $job_id = Fixtures::get("job_id");

        //delete the job:
        $deleted = $this->jobs->deleteJob($job_id);
        $this->assertTrue($deleted, "Delete job");

        //check if the job is really deleted:
        $status = $this->jobs->getStatus($job_id);
        $this->assertFalse($status);
    }


}