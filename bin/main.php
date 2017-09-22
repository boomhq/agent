<?php

require_once __DIR__.'/../App/bootstrap.php';

$testC = $app->get('\App\Controllers\ManageContainerController');

$r = $testC->buildMinecraftContainer('25565');

if (!$r === false) {
   $u = $testC->startContainer($r->getId());
}

print_r($r);
print"___________________\n";
print_r($u);