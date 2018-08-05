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

			$response = $client->request($method, $cmd['url'], $params);

		} catch (\GuzzleHttp\Exception\RequestException $e) {
			//echo "ERROR: " . \GuzzleHttp\Psr7\str($e->getRequest());
			//die("ERROR!!!");

			if ($e->hasResponse()) {
				//echo Psr7\str($e->getResponse());
				$return['response'] = $e->getResponse();
			}

			\writelog("Error! " . $e->getMessage());
			return false;
		}

		if (isset($response) && $response !== null) {
            $body = (string) $response->getBody();

            /*
            $statusCode = $response->getStatusCode();
            $return['status_code'] = $statusCode ;
            $return['body'] =  $body;
            */

            $return = $body;
		}

		\writelog("HTTP done");
		
		return $return;

	}

}
