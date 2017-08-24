<?php
namespace LaravelRocket\ServiceAuthentication\Services\Production;

use LaravelRocket\Foundation\Repositories\AuthenticatableRepositoryInterface;
use LaravelRocket\Foundation\Services\Production\BaseService;
use LaravelRocket\ServiceAuthentication\Repositories\ServiceAuthenticationRepositoryInterface;
use LaravelRocket\ServiceAuthentication\Services\ServiceAuthenticationServiceInterface;

class ServiceAuthenticationService extends BaseService implements ServiceAuthenticationServiceInterface
{
    /** @var ServiceAuthenticationRepositoryInterface $serviceAuthenticationRepository */
    protected $serviceAuthenticationRepository;

    /** @var AuthenticatableRepositoryInterface $authenticatableRepository */
    protected $authenticatableRepository;

    public function __construct(
        AuthenticatableRepositoryInterface $authenticatableRepository,
        ServiceAuthenticationRepositoryInterface $serviceAuthenticationRepository
    ) {
        $this->authenticatableRepository       = $authenticatableRepository;
        $this->serviceAuthenticationRepository = $serviceAuthenticationRepository;
    }

    public function getAuthModelId($service, $input)
    {
        $columnName = $this->serviceAuthenticationRepository->getAuthModelColumn();

        $authInfo = $this->serviceAuthenticationRepository->findByServiceAndId(
            $service,
            array_get($input, 'service_id')
        );
        if (!empty($authInfo)) {
            return $authInfo->$columnName;
        }

        $authUser = $this->authenticatableRepository->findByEmail(array_get($input, 'email'));
        if (!empty($authUser)) {
            $authInfo = $this->serviceAuthenticationRepository->findByServiceAndAuthModelId($service, $authUser->id);
            if (!empty($authInfo)) {
                return $authUser->id;
            }
        } else {
            if (array_key_exists('avatar', $input)) {
                $input['image_url'] = $input['avatar'];
            }
            $authUser = $this->authenticatableRepository->create($input);
        }

        $input[$columnName] = $authUser->id;
        $this->serviceAuthenticationRepository->create($input);

        return $authUser->id;
    }
}
