<?php

require_once __DIR__.'/../App/Boot/bootstrap.php';

$test = new \App\Services\Docker\Console\ConsoleService();

$x = $test->executeCommandInConsoleAttach( 'mc',"help");

print_r($x);
