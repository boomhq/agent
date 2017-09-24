<?php

namespace App\Services\Docker\Provisioning;

use Docker\API\Model\ContainerConfig;
use Docker\API\Model\ContainerCreateResult;
use Docker\API\Model\HostConfig;
use Docker\API\Model\PortBinding;
use Docker\Docker;
use PHPUnit\Runner\Exception;

/**
 * Class ProvisioningService
 *
 * @package App\Services\Docker
 */
class ProvisioningService implements ProvisioningServiceInterface
{
    /**
     * @var Docker
     */
    protected $docker;
    /**
     * @var HostConfig
     */
    protected $hostConfig;
    /**
     * @var ContainerConfig
     */
    protected $containerConfig;


    /**
     * ProvisioningService constructor.
     * @param ContainerConfig $containerConfig
     * @param Docker $docker
     * @param HostConfig $hostConfig
     */
    public function __construct(ContainerConfig $containerConfig, Docker $docker, HostConfig $hostConfig)
    {
        $this->containerConfig = $containerConfig;
        $this->docker = $docker;
        $this->hostConfig = $hostConfig;
    }

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
    ) {

        $this->prepareContainerConfig($envVariables, $image, $hostPort, $hostIp, $containerPort, $memoryContainer);

        $containerManager = $this->docker->getContainerManager();
        try {
            if (!empty($name)) {
                $containerCreated = $containerManager->create($this->containerConfig, ['name' => $name]);
            } else {
                $containerCreated = $containerManager->create($this->containerConfig);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $containerCreated;
    }

    private function prepareContainerConfig(
        array $envVariables,
        string $image,
        string $hostPort,
        string $hostIp,
        string $containerPort,
        int $memoryContainer
    ): ContainerConfig {

        $this->prepareHostConfig($hostPort, $containerPort, $hostIp, $memoryContainer);

        $this->containerConfig->setImage($image);
        $this->containerConfig->setHostConfig($this->hostConfig);
        $this->containerConfig->setOpenStdin(true);
        $this->containerConfig->setTty(true);
        $this->containerConfig->setAttachStdin(true);
        $this->containerConfig->setAttachStdout(true);
        $this->containerConfig->setAttachStderr(true);
        $VariableEnv = $this->prepareEnvVariables($envVariables);
        $this->containerConfig->setEnv($VariableEnv);

        return $this->containerConfig;
    }

    private function prepareHostConfig(
        string $hostPort,
        string $containerPort,
        string $hostIp,
        int $memoryContainer
    ): HostConfig {
        $this->hostConfig->setPortBindings($this->prepareArrayForIpPortsBinding($hostPort, $containerPort, $hostIp));
        $this->hostConfig->setMemory($memoryContainer);
        $this->hostConfig->setMemorySwap(0);
        $this->hostConfig->setOomKillDisable(true);

        return $this->hostConfig;
    }

    /**
     * Appel prepareHostPortIp pour construire le parametre final
     * @param string $hostPort
     * @param string|null $hostIp
     * @param string $containerPort
     * @return \ArrayObject
     */
    private function prepareArrayForIpPortsBinding(
        string $hostPort,
        string $containerPort,
        string $hostIp
    ) {

        $portMap = new \ArrayObject();
        //On prepare l'ip et le port de l'Host à exposer
        $portBinding = $this->prepareHostPortIp($hostPort, $hostIp);
        //Port Du container à binder sur HostPort
        $portMap[$containerPort.'/tcp'] = [$portBinding];

        return $portMap;
    }

    /**
     * Preparer l'ip et le port de l'host à exposé
     * @param string $hostPort
     * @param string|null $hostIp
     * @return PortBinding
     */
    private function prepareHostPortIp(string $hostPort, string $hostIp = null): PortBinding
    {
        $portBinding = new PortBinding();
        // filter_var($hostIp, FILTER_VALIDATE_IP);
        $portBinding->setHostIp($hostIp);
        //Port de l'host à binder
        $portBinding->setHostPort($hostPort);

        return $portBinding;
    }

    /**
     * Transformer le tableau dans le format attendu par CreateContainer
     * @param array $environmentVariable
     * @return array
     */
    private function prepareEnvVariables(array $environmentVariable): array
    {
        $flatEnvironmentVariable = [];
        foreach ($environmentVariable as $key => $value) {
            $flatEnvironmentVariable[] = $key.'='.$value;
        }

        return $flatEnvironmentVariable;
    }
}
