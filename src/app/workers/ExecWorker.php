<?php

/**
 * ExecWorker
 * Will work on a shell script
 */

namespace Millsoft\Queuer\Workers;

class ExecWorker implements iWorker{
		//Execute a HTTP task
	public function work($cmd) {
        //echo "executing system call" . $cmd['cmd'] . "\n";
        $shell_command = $cmd['cmd'];
        exec($shell_command );
        //TODO: Nothing here yet!!!

	}

}
