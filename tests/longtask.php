<?php
@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);
@ob_end_clean();
ob_implicit_flush(1);

header('Content-Type: text/HTML; charset=utf-8');
header("Content-Encoding: none");

/*
 * This is an example for a long taking task.
 */
ob_end_flush();
ob_start();

//How many seconds to wait?
$seconds = isset($_REQUEST['seconds']) ? $_REQUEST['seconds'] : 10;

echo "Long task started... This task will take $seconds seconds.";


for ($a = 0; $a < $seconds ; $a++){
    echo "Read Line #" . $a . "\r\n";

    //This helps with the flushing but generates bloat data..
    //    echo str_repeat(' ',1024*64);

    ob_flush();
    flush();
    sleep(1);
}
ob_flush();
print_r($_REQUEST);

//sleep(5);
echo "I am done";