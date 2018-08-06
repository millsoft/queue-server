<?php

/**
 * HttpWorker
 * Will do http requests
 */

namespace Millsoft\Queuer\Workers;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class HttpWorker implements iWorker {

    public $job_id = null;

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

		try {

		    $url = $cmd['url'];

            //Replace placeholders with actual values:
		    $url = $this->parseRequestParams($url);

			$response = $client->request($method, $url , $params);
            $body = $response->getBody();

		} catch (\GuzzleHttp\Exception\RequestException $e) {

			if ($e->hasResponse()) {
				$return['response'] = $e->getResponse();
			}

			\writelog("Error! " . $e->getMessage());
			return false;
		}

		if (isset($response) && $response !== null) {
            $body = (string) $response->getBody();

            //Output the http request
            echo $body;
            $return = $body;
		}

		\writelog("HTTP done");
		
		return $return;

	}

	//Replace placeholder variables with actual values
	private function parseRequestParams($req){
	    $req = str_replace("__JOBID__", $this->job_id, $req);
	    return $req;
    }

}
