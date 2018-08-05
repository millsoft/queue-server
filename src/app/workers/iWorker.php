<?php

/**
 * Each worker should always implement this interface
 */

namespace Millsoft\Queuer\Workers;

Interface iWorker{
    public function work($cmd);
}