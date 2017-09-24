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
     * @var Docker
     */
    protected $dockerService;


    /**
     * NetworkService constructor.
     * @param DockerService $dockerService
     */
    public function __construct(DockerService $dockerService)
    {
        $this->dockerService = $dockerService;
    }

    /**
     * return all ports exposed by host
     * @return array
     */
    public function getAllPortsHostExposed(): array
    {

        $containers = $this->dockerService->findAllContainers();
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
