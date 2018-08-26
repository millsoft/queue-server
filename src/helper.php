<?php

function writelog($txt, $extra = null) {
	if(isCli()){
		echo $txt . "\n";
	}else{
        //echo $txt . '<br/>';
	}
}

//Check if we are in terminal or in webbrowser
function isCli(){
	return php_sapi_name() == "cli" ? true : false;
}

/**
 * Get the app version from the .version file
 */
function getAppVersion(){
    $versionFile = __DIR__ . "/../.version";
    if(!file_exists($versionFile )){
        return '';
    }

    return file_get_contents($versionFile);
}