<?php
/**
 * Copyright (C) 2018 Michael Milawski - All Rights Reserved
 * You may use, distribute and modify this code under the
 *  terms of the MIT license.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);



require_once __DIR__ . "/src/initapp.php";
require_once __DIR__ . "/src/routes.php";

//Middleware - will be used later for auth
$app->add(function ($request, $response, $next) {
    //$response->getBody()->write('BEFORE');
    $response = $next($request, $response);
    //$response->getBody()->write('AFTER');
    return $response;
});


$app->run();