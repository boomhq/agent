<?php

namespace App\Controllers;

use App\Services\Docker\Console\ConsoleService;

class InteractContainerController extends Controller
{

    private $consoleService;

    public function __construct()
    {
        $this->consoleService = new ConsoleService();
    }


    public function sendCommandToConsoleAttach(string $containerIdOrName, string $command)
    {
        $reponseReturned = $this->consoleService->executeCommandInConsoleAttach($containerIdOrName, $command);

        if ($reponseReturned === false) {
            return false;
        }

        return $reponseReturned;
    }

    public function sendCommandToConsoleNewTTY(string $containerIdOrName, array $command)
    {
        $reponseReturned = $this->consoleService->executeCommandsInNewTTY($containerIdOrName, $command);

        if (!is_array($reponseReturned)) {
            return false;
        }

        return $reponseReturned;
    }

}
