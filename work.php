<?php
namespace Millsoft\Queuer;

/*
* Worker script
* This script will handle every single job.
* This will be called multiple of times, eveb in parallel
*/

require_once __DIR__ . "/src/init.php";

$short_options = "j::";
$long_options = ["job::"];


$options = getopt($short_options, $long_options);

//print_r($options);

$job_id = isset($options['job']) ? $options['job'] : (isset($options['j']) ? $options['j'] : null);
$worker = new Worker($job_id);

