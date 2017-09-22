<?php
/**
 * Created by PhpStorm.
 * User: boom
 * Date: 22/09/2017
 * Time: 08:14
 */

namespace App\Services\Docker\Provisioning;

use Docker\API\Model\ContainerCreateResult;


/**
 * Class ProvisioningService
 *
 * @package App\Services\Docker
 */
interface ProvisioningServiceInterface
{
    /**
     * @param string $name
     * @param array $envVariables
     * @param string $hostPort
     * @param string $containerPort
     * @param int $memoryContainer
     * @param string $image
     * @param string $hostIp
     * @return ContainerCreateResult|string;
     */
    public function createContainer(
        array $envVariables,
        string $hostPort = '25565',
        string $containerPort = '25565',
        string $image = 'itzg/minecraft-server',
        int $memoryContainer = 512000000,
        string $hostIp = '0.0.0.0',
        string $name = null
    );
}