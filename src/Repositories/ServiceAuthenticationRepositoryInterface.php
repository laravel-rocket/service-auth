<?php
namespace LaravelRocket\ServiceAuthentication\Repositories;

use LaravelRocket\Foundation\Repositories\SingleKeyModelRepositoryInterface;
use LaravelRocket\ServiceAuthentication\ServiceAuthenticationBase;

interface ServiceAuthenticationRepositoryInterface extends SingleKeyModelRepositoryInterface
{
    /**
     * @return string
     */
    public function getAuthModelColumn();

    /**
     * Find Service Auth Info by service and service id.
     *
     * @param string $service
     * @param string $id
     *
     * @return ServiceAuthenticationBase
     */
    public function findByServiceAndId($service, $id);

    /**
     * Find Service Auth Info by service and user id.
     *
     * @param string $service
     * @param int    $authModelId
     *
     * @return ServiceAuthenticationBase
     */
    public function findByServiceAndAuthModelId($service, $authModelId);
}
