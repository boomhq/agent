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
class ConsoleService implements ConsoleServiceInterface
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


    /**
     * Exec command in Console like $me->executeCommandInConsoleAttach('inspiring_euclid','say hello')
     * return the last line reponse
     * @param string $containerIdOrName
     * @param string $cmd
     * @return string
     */
    public function executeCommandInConsoleAttach(string $containerIdOrName, string $cmd)
    {

        try {
            $attachStream = $this->docker->getContainerManager()->attachWebsocket(
                $containerIdOrName,
                [
                    'stream' => true,
                    'stdout' => true,
                    'stderr' => true,
                ]
            );
        } catch (\Exception $exception) {
            die(print "Caught TestException ('{$exception->getMessage()}')\n{$exception}\n");
        }

        if (isset($attachStream)) {
            $consoleOut = "";
            $attachStream->write($cmd."\n");
            while (($data = $attachStream->read()) != false) {
                $consoleOut .= $data;
            }
        } else {
            return false;
        }

        return $consoleOut;
    }

    /**
     * Exec commands in system (spawn tty) and return STDOUT and STDERR
     * like $me->executeCommandsInNewTTY('inspiring_euclid',['ls','-al'])
     * @param string $containerIdOrName
     * @param array $cmd
     * @return array
     * @throws \Exception
     */
    public function executeCommandsInNewTTY(string $containerIdOrName, array $cmd)
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

        $execId = $this->docker->getExecManager()->create($containerIdOrName, $execConfig)->getId();
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
