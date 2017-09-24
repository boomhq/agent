<?php

namespace App\Controllers;

use App\Services\Docker\DockerService;
use App\Services\Docker\Network\NetworkService;
use App\Services\Docker\Provisioning\ProvisioningService;
use Psr\Log\LoggerInterface;

class ManageContainerController extends Controller
{
    private $dockerService;
    private $provisioningService;
    private $networkService;
    private $logger;

    public function __construct(
        DockerService $dockerService,
        ProvisioningService $provisioningService,
        NetworkService $networkService,
        LoggerInterface $logger
    ) {
        $this->dockerService = $dockerService;
        $this->provisioningService = $provisioningService;
        $this->networkService = $networkService;
        $this->logger = $logger;
    }

    public function buildMinecraftContainer(
        string $portExposedFromHost,
        int $containerMemory = 512000000,
        string $nameOfContainer = null,
        string $portExposedFromContainer = '25565',
        string $versionMinecraft = 'SNAPSHOT',
        string $hostIP = '0.0.0.0',
        string $imageDocker = 'itzg/minecraft-server'
    ) {
        //check port is Goo d and not used

        if ($portExposedFromHost < 1024 || $portExposedFromHost > 65536) {
            return "Impossible, le port selectionné ne fait pas partie d'une plage de port autorisés";
        }

        $allPortsExposed = $this->networkService->getAllPortsHostExposed();

        if (in_array($portExposedFromHost, $allPortsExposed)) {
            return "Le port selectionné est utilisé";
        }

        //prepare Env Array
        $envVariable = [
            'EULA' => 'TRUE',
            'VERSION' => $versionMinecraft,
            'MIN_RAM' => '256M',
            'MAX_RAM' => '256M',
        ];

        $newContainer = $this->provisioningService->createContainer(
            $envVariable,
            $portExposedFromHost,
            $portExposedFromContainer,
            $imageDocker,
            $containerMemory,
            $hostIP,
            $nameOfContainer
        );
        $this->logger->info('Container Created '.$newContainer->getId());

        return $newContainer;
    }

    public function stopContainer(string $containerIdOrName)
    {
        $stopContainerReponse = $this->dockerService->stopContainer($containerIdOrName);
        if ($stopContainerReponse->getStatusCode() === 204) {
            return true;
        } else {
            return false;
        }
    }

    public function startContainer(string $containerIdOrName)
    {
        $startContainer = $this->dockerService->startContainer($containerIdOrName);
        if ($startContainer->getStatusCode() === 204) {
            return true;
        } else {
            return false;
        }
    }

    public function restartContainer(string $containerIdOrName)
    {
        $restartContainer = $this->dockerService->restartContainer($containerIdOrName);
        if ($restartContainer->getStatusCode() === 204) {
            return true;
        } else {
            return false;
        }
    }

    public function killContainer(string $containerIdOrName)
    {
        $killContainer = $this->dockerService->killContainer($containerIdOrName);
        if ($killContainer->getStatusCode() === 204) {
            return true;
        } else {
            return false;
        }
    }
}
