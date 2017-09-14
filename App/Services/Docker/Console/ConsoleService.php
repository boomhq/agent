<?php

namespace App\Services\Docker\Console;

use Docker\API\Model\ExecConfig;
use Docker\API\Model\ExecStartConfig;
use Docker\Docker;
use Docker\Manager\ExecManager;

/**
 * Class ConsoleService
 *
 * @package App\Services\Docker\Console
 */
class ConsoleService
{
    /**
     * @var Docker
     */
    protected $docker;


    /**
     * ProvisioningService constructor.
     */
    public function __construct()
    {
        $this->docker = new Docker();
    }


    public function executeCommandInGameServer($container, $cmd)
    {
        $attachStream = $this->docker->getContainerManager()->attachWebsocket(
            $container,
            [
                'logs' => true,
                'stream' => true,
                'stdin' => true,
                'stdout' => true,
                'stderr' => true,
            ]
        );

        $attachStream->write($cmd."\n");

        $consoleOut = "";
        while ($attachStream->read()) {
            $consoleOut .= $attachStream->read();
        }

        return $consoleOut;
    }

    /**
     * Exec command in system like $me->exec('inspiring_euclid',['ls','-al'])
     * @param $container
     * @param $cmd
     * @return array
     * @throws \Exception
     */
    public function executeCommandInSystem($container, $cmd)
    {

        if (!is_array($cmd)) {
            throw new \Exception("cmd must be an array of strings");
        }

        $execConfig = new ExecConfig();
        $execConfig->setTty(true);
        $execConfig->setAttachStdout(true);
        $execConfig->setAttachStderr(true);
        $execConfig->setCmd($cmd);

        $startConfig = new ExecStartConfig();
        $startConfig->setDetach(false);

        $execId = $this->docker->getExecManager()->create($container, $execConfig)->getId();
        $stream = $this->docker->getExecManager()->start($execId, $startConfig, [], ExecManager::FETCH_STREAM);

        $stdoutText = "";
        $stderrText = "";

        $stream->onStdout(
            function ($stdout) use (&$stdoutText) {
                $stdoutText .= $stdout;
            }
        );
        $stream->onStderr(
            function ($stderr) use (&$stderrText) {
                $stderrText .= $stderr;
            }
        );
        $stream->wait();

        return ["stdout" => $stdoutText, "stderr" => $stderrText];
    }
}
