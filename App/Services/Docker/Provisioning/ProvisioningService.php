<?php

namespace App\Services\Docker\Provisioning;

use Http\Client\Common\Exception\ClientErrorException;
use Docker\API\Model\ContainerConfig;
use Docker\API\Model\HostConfig;
use Docker\API\Model\PortBinding;
use Docker\Docker;

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
     */
    public function __construct()
    {
        $this->containerConfig = new ContainerConfig();
        $this->docker = new Docker();
        $this->hostConfig = new HostConfig();
    }

    /**
     * @param string $name
     * @param array $envVariables
     * @param string $hostPort
     * @param string $image
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function createContainer(
        string $name,
        array $envVariables,
        string $hostPort = '25565',
        string $image = 'itzg/minecraft-server'
    ) {

        $containerManager = $this->docker->getContainerManager();

        $this->containerConfig->setImage($image);
        $this->hostConfig->setPortBindings($this->prepareArrayForIpPortsBinding($hostPort));
        $this->containerConfig->setHostConfig($this->hostConfig);

        $VariableEnv = $this->prepareEnvVariables($envVariables);
        $this->containerConfig->setEnv($VariableEnv);

        try {
            $containerCreated = $containerManager->create($this->containerConfig, ['name' => $name]);
        } catch (ClientErrorException $e) {
            return $e->getMessage();
        }

        return $containerManager->start($containerCreated->getId());
    }

    /**
     * @param string $hostPort
     * @param string|null $hostIp
     * @param string $containerPort
     * @return \ArrayObject
     */
    private function prepareArrayForIpPortsBinding(
        string $hostPort,
        string $hostIp = null,
        string $containerPort = '25565'
    ) {

        $portMap = new \ArrayObject();
        //On prepare l'ip et le port de l'Host à exposer
        $portBinding = $this->prepareHostPortIp($hostPort, $hostIp);
        //Port Du container à binder sur HostPort
        $portMap[$containerPort . '/tcp'] = [$portBinding];

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
            $flatEnvironmentVariable[] = $key . '=' . $value;
        }
        return $flatEnvironmentVariable;
    }
}
