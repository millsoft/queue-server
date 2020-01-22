<?php

/**
 * This Class will run anything in the background without blocking the main process
 * Currently this only works on linux server
 * Code is based by https://github.com/pandasanjay/php-script-background-processer
 */

namespace Millsoft\Queuer;

class BackgroundProcess{
    private $pid;
    private $command;
    private $msg="";

    /**
     * BackgroundProcess constructor.
     * @param null $cmd - the linux command that should be run in the background
     */
    public function __construct($cmd=null){

        if(!empty($cmd))
        {
            $this->command=$cmd;
        }
        else{
            $this->msg['error']="Please Provide the Command Here";
        }
    }

    public function setCmd($cmd){
        $this->command = $cmd;
        return true;
    }

    public function setProcessId($pid){
        $this->pid = $pid;
        return true;
    }
    public function getProcessId(){
        return $this->pid;
    }
    public function status(){
        $command = 'ps -p '.$this->pid;
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }

    public function showAllPocess(){
        $command = 'ps -ef '.$this->pid;
        exec($command,$op);
        return $op;
    }

    public function start(){
        if ($this->command != '')
            $this->do_process();
        else return true;
    }
    public function stop(){
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }

    //do the process in background
    public function do_process(){
        $command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
        \writelog("do_process: $command");
        exec($command ,$pross);
        $this->pid = (int)$pross[0];
    }

    /*
    *To execute a PHP url on background you have to do the following things.
    * $process=new BackgroundProcess("curl -s -o <Base Path>/log/log_storewav.log <PHP URL to execute> -d param_key=<Param_value>");
    */

}