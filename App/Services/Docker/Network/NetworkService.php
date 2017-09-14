<?php

namespace App\Services\Docker\Network;

use App\Services\Docker\DockerService;

class NetworkService
{

    public function getAllPortsHostExposed(): array
    {
        $app = new DockerService();

        $containers = $app->findAllContainers();
        $portsExposed = [];
        foreach ($containers as $container) {
            $ports = $container->getPorts();
            foreach ($ports as $port) {
                if (is_int($port->getPublicPort())) {
                    $portsExposed[] = $port->getPublicPort();
                }
            }
        }
        return $portsExposed;
    }
}
