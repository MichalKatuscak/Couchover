<?php

use Couchover\Application;
use Couchover\Router;

@header('X-Powered-By: Couchover Framework');
@header('Content-Type: text/html; charset=utf-8'); 

include_once (__DIR__ . '/config.php');
include_once (__DIR__ . '/loader.php');

$application = new Application;
$application->configuration($config);
$application->route(
            new Router('//:language/:controller/:action/', '//cs/Default/default/')
        );
$application->run();