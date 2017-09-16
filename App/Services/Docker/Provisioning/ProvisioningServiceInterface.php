<?php

namespace App\Services\Docker\Provisioning;

/**
 * Interface ProvisioningServiceInterface
 * @package App\Services\Docker
 */
interface ProvisioningServiceInterface
{
    public function createContainer(
        array $envVariables,
        string $hostPort = '25565',
        string $containerPort = '25565',
        string $image = 'itzg/minecraft-server',
        string $hostIp = null,
        string $name = null
    );
}
