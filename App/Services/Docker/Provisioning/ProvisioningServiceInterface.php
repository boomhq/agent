<?php

namespace App\Services\Docker\Provisioning;

/**
 * Interface ProvisioningServiceInterface
 * @package App\Services\Docker
 */
interface ProvisioningServiceInterface
{
    public function createContainer(
        string $name,
        array $envVariables,
        string $hostPort = '25565',
        string $image = 'itzg/minecraft-server'
    );
}
