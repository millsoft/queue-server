<?php

require_once(__DIR__ . "/vendor/autoload.php");


function computeAwesomeResultAsynchronously($fn){
    echo "Computing...";
    sleep(1);
    echo "Computing DONE";
    $fn(false, "cool");
}

function getAwesomeResultPromise()
{
    $deferred = new React\Promise\Deferred();

    // Execute a Node.js-style function using the callback pattern
    computeAwesomeResultAsynchronously(function ($error, $result) use ($deferred) {
        if ($error) {
            echo "ERRRROR";
            $deferred->reject($error);
        } else {
            echo "DOOONNE";

            $deferred->resolve($result);
        }
    });

    // Return the promise
    return $deferred->promise();
}

getAwesomeResultPromise()
    ->then(
        function ($value) {
            // Deferred resolved, do something with $value
            echo("THEN!");
        },
        function ($reason) 
{            // Deferred rejected, do something with $reason
            echo("REJECTED!");

        },
        function ($update) {
            // Progress notification triggered, do something with $update
            echo("PROGRESS!");

        }
    );


echo "\n\nSTOP\n\n";