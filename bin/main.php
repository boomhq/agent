<?php

require_once __DIR__.'/../App/Boot/bootstrap.php';

$x = new \App\Controllers\ManageContainerController();

$z = $x->buildMinecraftContainer('25565');


print_r($z);

$docker = new \Docker\Docker();
$containerManager = $docker->getContainerManager();

$attachStream = $containerManager->attach(
    $z->getId(),
    [
        'stream' => true,
        'stdin' => true,
        'stdout' => true,
        'stderr' => true,
    ]
);

$containerManager->start($z->getId());


$attachStream->onStdout(
    function ($stdout) {
        echo $stdout;
    }
);
$attachStream->onStderr(
    function ($stderr) {
        echo $stderr;
    }
);

$attachStream->wait();
