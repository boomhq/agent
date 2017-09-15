<?php
namespace App\Services\Docker\Console;

/**
 * Class ConsoleService
 *
 * @package App\Services\Docker\Console
 */
interface ConsoleServiceInterface
{
    /**
     * Exec command in Console like $me->executeCommandInConsoleAttach('inspiring_euclid','say hello')
     * return the last line reponse
     * @param string $containerIdOrName
     * @param string $cmd
     * @return string
     */
    public function executeCommandInConsoleAttach(string $containerIdOrName, string $cmd);

    /**
     * Exec commands in system (spawn tty) and return STDOUT and STDERR
     * like $me->executeCommandsInNewTTY('inspiring_euclid',['ls','-al'])
     * @param string $containerIdOrName
     * @param array $cmd
     * @return array
     * @throws \Exception
     */
    public function executeCommandsInNewTTY(string $containerIdOrName, array $cmd);
}