<?php

/*
 * This is an example for a long taking task.
 */
ob_start();

//How many seconds to wait?
$seconds = isset($_REQUEST['seconds']) ? $_REQUEST['seconds'] : 10;

echo "Long task started... This task will take $seconds seconds.";


for ($a = 0; $a < $seconds ; $a++){
    echo "Read Line #" . $a . "\n";
    flush();
    sleep(1);
}
ob_flush();
print_r($_REQUEST);

//sleep(5);
echo "I am done";