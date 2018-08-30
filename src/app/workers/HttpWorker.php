<?php

/**
 * HttpWorker
 * Will do http requests
 */

namespace Millsoft\Queuer\Workers;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class HttpWorker  implements iWorker {

    public $job_id = null;
    public $caller = null;

		//Execute a HTTP task
	public function work($cmd) {

		echo "Doing HTTP to " . $cmd['url'] . "\n";
		//Store return data here temporary:
		$return = [];

		$timeout = isset($cmd['timeout']) ? $cmd['timeout'] : (isset($this->config->httpTimeout) ? $this->config->httpTimeout : 10);

		$client = new Client([
			'timeout' => $timeout,
		]);

		$method = strtoupper(isset($cmd['method']) ? $cmd['method'] : 'GET');

		$params = array();
		if (isset($cmd['params'])) {
			$params['form_params'] = $cmd['params'];
		}
        $useStream = true;

        $params['stream'] = $useStream;

		try {

		    $url = $cmd['url'];

            //Replace placeholders with actual values:
		    $url = $this->parseRequestParams($url);

			$response = $client->request($method, $url , $params);

            if($useStream) {
                $body = $response->getBody();
                $resp = '';
                $time_start = microtime(true);

                while (!$body->eof()) {
                    $resp .= $body->read(1024);
                    $time_end = microtime(true);
                    $time = $time_end - $time_start;

                    if ($time > 0.5) {
                        //TODO: 1 second passed between stream reader. Update the output to the database:
                        //$this->caller->setJobDone(2, $resp);
                    \writelogProgress("Received " . strlen($resp) . " bytes");

                    //Reset the timer:
                        $time_start = microtime(true);
                    }

                }
            }

            \writelog("\n");

		} catch (\GuzzleHttp\Exception\RequestException $e) {

			if ($e->hasResponse()) {
				$return['response'] = $e->getResponse();
			}

			\writelog("Error! " . $e->getMessage());
			return false;
		}

		//if (isset($response) && $response !== null) {
            //$body = (string) $response->getBody();
        //echo $resp;
        //Output the http request
            $return = $resp;

        //}

		\writelog("HTTP done");
		
		return $return;

	}

	//Replace placeholder variables with actual values
	private function parseRequestParams($req){
	    $req = str_replace("__JOBID__", $this->job_id, $req);
	    return $req;
    }

}
