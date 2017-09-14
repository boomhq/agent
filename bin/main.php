<?php

require_once __DIR__ . '/../App/Boot/bootstrap.php';

$app = new \App\Services\Docker\DockerService();
$r = $app->findAllContainers();
print_r($r);
