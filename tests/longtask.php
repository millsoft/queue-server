<?php

/*
 * This is an example for a long taking task.
 */
ob_start();
echo "Long task started...";

for ($a = 0; $a < 5 ; $a++){
    echo "Read Line #" . $a . "\n";
    flush();
    sleep(1);
}
ob_flush();
print_r($_REQUEST);

//sleep(5);
echo "I am done";