<?php
namespace LaravelRocket\ServiceAuthentication\Services;

use LaravelRocket\Foundation\Services\BaseServiceInterface;

interface ServiceAuthenticationServiceInterface extends BaseServiceInterface
{
    /**
     * @param string $service
     * @param array  $input
     *
     * @return int
     */
    public function getAuthModelId($service, $input);

    /**
     * @param string $service
     * @param string $token
     *
     * @return int
     */
    public function getUserIdFromToken($service, $token);
}
