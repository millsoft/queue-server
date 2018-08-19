<?php

namespace Millsoft\Queuer;
use \Psr\Container\ContainerInterface;

class Management{
	
	protected $container;

   public function __construct(ContainerInterface $container) {
       $this->container = $container;
   }

   //Management - Start Dashboard
   public function dashboard($request, $response, $args) {
   	$jobsCount = $this->container->jobs->getJobsCount();


    return $this->container->view->render($response, 'index.html', [
        'counts' => $jobsCount
    ]);

   }
}