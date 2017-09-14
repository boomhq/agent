<?php
/**
 * Created by PhpStorm.
 * User: boom
 * Date: 14/09/2017
 * Time: 09:36
 */

namespace App\Services\Docker;


/**
 * Class ContainerService
 *
 * @package App\Services\Docker
 */
interface DockerServiceInterface
{
    /**
     * Trouver tout les containers en fonctionnement with full info
     *
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllRunningContainers();

    /**
     * Trouver tout les containers à l'arret with full info
     *
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllStoppedContainers();

    /**
     * Trouver tout les containers filtrer par status
     *
     * @param string $status status à filtrer # https://stackoverflow.com/a/28055631/8240404
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllFiltredByStatusContainers(string $status);

    /**
     * Trouver tout les containers à l'arret et en fonctionnement with full info
     *
     * @return \Docker\API\Model\ContainerInfo[]|\Psr\Http\Message\ResponseInterface
     */
    public function findAllContainers();

    /**
     * Trouver tout les containers à l'arret et en fonctionnement with full info
     *
     * @param string $idOrName id docker ou nom du container
     * @return \Docker\API\Model\Container|\Psr\Http\Message\ResponseInterface
     */
    public function findByIdOrNameContainer(string $idOrName);

    /**
     * Start container
     *
     * @param string $idOrName du containter
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function startContainer(string $idOrName);

    /**
     * Stop Container
     *
     * @param string $idOrName du containter
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function stopContainer(string $idOrName);

    /**
     * Kill a container (force stop)
     *
     * @param string $idOrName du containter
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function killContainer(string $idOrName);
}