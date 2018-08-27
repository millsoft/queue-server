<?php

namespace Millsoft\Queuer;
use \Psr\Container\ContainerInterface;

class Management{
	
	protected $container;

    /**
     * @var \Millsoft\Queuer\Jobs
     */
	protected $Jobs = null;

    /**
     * @var \Millsoft\Queuer\Config
     */
	protected $Conf = null;

   public function __construct(ContainerInterface $container) {
       $this->container = $container;
       $this->Jobs = $container->jobs;
       $this->Conf = $this->Jobs->config;
   }

   //Management - Start Dashboard
   public function dashboard($request, $response, $args) {
   	$jobsCount = $this->container->jobs->getJobsCount();
   	$websocketUrl = $this->getWebsocketUrl();

    return $this->container->view->render($response, 'index.html', [
        'counts' => $jobsCount,
        'websocket_url' => $websocketUrl
    ]);

   }

    /**
     * Get the websocket URL for management console javascript
     * @return bool|string
     */
   private function getWebsocketUrl(){
       if($this->Conf->webSocket){
           $ws = $this->Conf->webSocketSecure ? 'wss' : 'ws';
           return $ws . '://' . $this->Conf->webSocketServer . ':' . $this->Conf->webSocketPort;
       }else{
           return false;
       }

   }


}