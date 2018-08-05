<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 */

namespace Millsoft\Queuer;

/*
* Worker script
* This script will handle every single job.
* This will be called multiple of times, even in parallel
*/

//make the app run forever, each job can decide how long a job should run by providing the "timeout" value.
set_time_limit(0);

require_once __DIR__ . "/src/init.php";

//This worker accepts the -j or --job parameter with the job id.
$short_options = "j::";
$long_options = ["job::"];

$options = getopt($short_options, $long_options);

$job_id = isset($options['job']) ? $options['job'] : (isset($options['j']) ? $options['j'] : null);

//Run the worker
new Worker($job_id);
