<?php

namespace App\Controllers;

use App\Services\Docker\DockerService;
use App\Services\Docker\Network\NetworkService;
use App\Services\Docker\Provisioning\ProvisioningService;

class ManageContainerController extends Controller
{

    private $dockerService;
    private $provisioningService;


    public function __construct()
    {
        $this->dockerService = new DockerService();
        $this->provisioningService = new ProvisioningService();
    }


    public function buildMinecraftContainer(
        string $portExposedFromHost,
        string $nameOfContainer = null,
        string $portExposedFromContainer = '25565',
        string $versionMinecraft = 'SNAPSHOT',
        string $hostIP = '0.0.0.0'
    ) {

        //check port is Good and not used

        if ($portExposedFromHost < 1024 || $portExposedFromHost > 65536) {
            return "Impossible, le port selectionné ne fait pas partie d'une plage de port autorisés";
        }

        $networkService = new NetworkService();
        $allPortsExposed = $networkService->getAllPortsHostExposed();

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

        $provisioningService = new ProvisioningService();
        $newContainer = $provisioningService->createContainer(
            $envVariable,
            $portExposedFromHost,
            $portExposedFromContainer,
            'itzg/minecraft-server',
            $hostIP,
            $nameOfContainer
        );

        return $newContainer;
    }
}
