<?php

namespace App\Services\Docker;

use Docker\Docker;

/**
 * Class ContainerService
 *
 * @package App\Services\Docker
 */
class DockerService extends Docker implements DockerServiceInterface
{

    /**
     * Trouver tout les containers en fonctionnement with full info
     *
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllRunningContainers()
    {
        return $this->getContainerManager()->findAll(['filters' => '{ "status": [ "running" ] }']);
    }

    /**
     * Trouver tout les containers à l'arret with full info
     *
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllStoppedContainers()
    {
        return $this->getContainerManager()->findAll(['all' => 1, 'filters' => '{ "status": [ "exited" ] }']);
    }

    /**
     * Trouver tout les containers filtrer par status
     *
     * @param string $status status à filtrer # https://stackoverflow.com/a/28055631/8240404
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllFiltredByStatusContainers(string $status)
    {
        return $this->getContainerManager()->findAll(['all' => 1, 'filters' => '{ "status": [ "' . $status . '" ] }']);
    }

    /**
     * Trouver tout les containers à l'arret et en fonctionnement with full info
     *
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllContainers()
    {
        return $this->getContainerManager()->findAll(['all' => 1]);
    }


    /**
     * Trouver tout les containers à l'arret et en fonctionnement with full info
     *
     * @param string $idOrName id docker ou nom du container
     * @return \Docker\API\Model\Container|\Psr\Http\Message\ResponseInterface
     */
    public function findByIdOrNameContainer(string $idOrName)
    {
        return $this->getContainerManager()->find($idOrName);
    }

    /**
     * Start container
     *
     * @param string $idOrName du containter
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function startContainer(string $idOrName)
    {
        return $this->getContainerManager()->start($idOrName);
    }

    /**
     * Stop Container
     *
     * @param string $idOrName du containter
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function stopContainer(string $idOrName)
    {
        return $this->getContainerManager()->stop($idOrName);
    }

    /**
     * Kill a container (force stop)
     *
     * @param string $idOrName du containter
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function killContainer(string $idOrName)
    {
        return $this->getContainerManager()->kill($idOrName);
    }
}
