<?php


namespace Millsoft\Queuer;

use \Ratchet\MessageComponentInterface;
use \Ratchet\ConnectionInterface;

/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
 */
class WebSocketServer implements MessageComponentInterface {
    
   protected $clients;
   protected $jobs;

   public $statusFile = __DIR__ . "/../../.statusfile";

    public function __construct() {
        $this->clients = new \SplObjectStorage;

        $this->jobs = new Jobs;
        //print_r($jobs_count);
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $response = $msg;

        if($msg == 'status'){
        	$jobs_count = $this->jobs->getJobsCount();

        	//$statusTimestamp = $this->jobs->getStatusFileTimeStamp();
        	//$statusTimestamp = $this->getStatusFileTimeStamp();
        	//$response = $statusTimestamp;
        	$response = md5(json_encode($jobs_count));
        }

        //$client->send($response);
        $from->send($response);

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                //$client->send($response);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

        public function getStatusFileTimeStamp(){
    	if(!file_exists($this->statusFile)){
    		return 0;
    	}

    	$ti = filemtime($this->statusFile);
    	$ti = date("H:i:s", $ti);
    	return $ti;


    }
}