<?php

namespace App\Controllers;

use App\Services\Docker\Console\ConsoleService;

/**
 * Class InteractContainerController
 * @package App\Controllers
 */
class InteractContainerController extends Controller
{

    /**
     * @var ConsoleService
     */
    private $consoleService;

    /**
     * InteractContainerController constructor.
     * @param ConsoleService $consoleService
     */
    public function __construct(ConsoleService $consoleService)
    {
        $this->consoleService = $consoleService;
    }


    /**
     * @param string $containerIdOrName
     * @param string $command
     * @return bool|string
     */
    public function sendCommandToConsoleAttach(string $containerIdOrName, string $command)
    {
        $reponseReturned = $this->consoleService->executeCommandInConsoleAttach($containerIdOrName, $command);

        if ($reponseReturned === false) {
            return false;
        }

        return $reponseReturned;
    }

    /**
     * @param string $containerIdOrName
     * @param array $command
     * @return array|bool
     */
    public function sendCommandToConsoleNewTTY(string $containerIdOrName, array $command)
    {
        $reponseReturned = $this->consoleService->executeCommandsInNewTTY($containerIdOrName, $command);

        if (!is_array($reponseReturned)) {
            return false;
        }

        return $reponseReturned;
    }

}
