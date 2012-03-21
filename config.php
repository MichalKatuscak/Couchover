<?php

$config = Array();

$config['language-default'] = 'cs';

$config['application-url'] = __DIR__ . '/Application';

$config['db'] = Array();
$config['db']['type'] = 'MySQLi';
$config['db']['server'] = 'localhost';
$config['db']['username'] = 'root';
$config['db']['password'] = '';
$config['db']['database'] = 'test';

$config['debugger'] = Array();
$config['debugger']['level'] = 2;
$config['debugger']['file_log_errors'] = __DIR__ . '/Logs/php_errors.log';
