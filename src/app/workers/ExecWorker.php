<?php

/**
 * ExecWorker
 * Will work on a shell script
 */

namespace Millsoft\Queuer\Workers;

class ExecWorker implements iWorker{

    /**
     * Execute a shell command
     * @param $cmd
     */
	public function work($cmd) {
        $shell_command = $cmd['cmd'];
        exec($shell_command );
	}

}
