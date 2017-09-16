<?php
/**
 * Created by PhpStorm.
 * User: boom
 * Date: 16/09/2017
 * Time: 21:04
 */

namespace App\Services\Docker\Network;


/**
 * Class NetworkService
 * @package App\Services\Docker\Network
 */
interface NetworkServiceInterface
{
    /**
     * @return array
     */
    public function getAllPortsHostExposed(): array;
}