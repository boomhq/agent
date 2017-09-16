<?php

namespace App\Services\Docker\Network;

use App\Services\Docker\DockerService;

/**
 * Class NetworkService
 * @package App\Services\Docker\Network
 */
class NetworkService implements NetworkServiceInterface
{

    /**
     * return all ports exposed by host
     * @return array
     */
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
