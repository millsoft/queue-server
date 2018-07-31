<?php

/**
 * Worker Module
 * This handles the job in the background
 */

namespace Millsoft\Queuer;

class Worker extends Queuer {
	private $job = null;

	public function __constuct($job) {
		$this->job = $job;
		$this->work();
	}

	public function dowork() {
		die("dowork YEAH");
	}

	public function work() {
		$wrk = $this->dowork;
		\writelog("working");
		$resolver = function ($wrk, callable $reject, callable $notify) {
			// Do some work, possibly asynchronously, and then
			// resolve or reject. You can notify of progress events (deprecated)
			// along the way if you want/need.
			\writelog("promissing");

			//$resolve($awesomeResult);
			die("FUCK");
			// or throw new Exception('Promise rejected');
			// or $resolve($anotherPromise);
			// or $reject($nastyError);
			// or $notify($progressNotification);
		};

		$canceller = function () {
			// Cancel/abort any running operations like network connections, streams etc.

			// Reject promise by throwing an exception
			throw new Exception('Promise cancelled');
		};

		$promise = new React\Promise\Promise($resolver, $canceller);

	}
}