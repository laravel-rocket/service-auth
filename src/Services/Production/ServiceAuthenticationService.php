<?php
namespace LaravelRocket\ServiceAuthentication\Services\Production;

use LaravelRocket\Foundation\Repositories\AuthenticatableRepositoryInterface;
use LaravelRocket\Foundation\Services\AuthenticatableServiceInterface;
use LaravelRocket\Foundation\Services\Production\BaseService;
use LaravelRocket\ServiceAuthentication\Repositories\ServiceAuthenticationRepositoryInterface;
use LaravelRocket\ServiceAuthentication\Services\ServiceAuthenticationServiceInterface;

class ServiceAuthenticationService extends BaseService implements ServiceAuthenticationServiceInterface
{
    /** @var ServiceAuthenticationRepositoryInterface $serviceAuthenticationRepository */
    protected $serviceAuthenticationRepository;

    /** @var AuthenticatableRepositoryInterface $authenticatableRepository */
    protected $authenticatableRepository;

    /** @var AuthenticatableServiceInterface $authenticatableService */
    protected $authenticatableService;

    public function __construct(
        AuthenticatableRepositoryInterface $authenticatableRepository,
        ServiceAuthenticationRepositoryInterface $serviceAuthenticationRepository,
        AuthenticatableServiceInterface $authenticatableService
    ) {
        $this->authenticatableRepository       = $authenticatableRepository;
        $this->serviceAuthenticationRepository = $serviceAuthenticationRepository;
        $this->authenticatableService          = $authenticatableService;
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
            $imageUrl          = array_get($input, 'avatar');
            $input['password'] = str_random(20);
            $authUser          = $this->authenticatableService->createWithImageUrl($input, $imageUrl);
        }

        $input[$columnName] = $authUser->id;
        $input['image_url'] = array_get($input, 'avatar', '');
        $this->serviceAuthenticationRepository->create($input);

        return $authUser->id;
    }

    public function getUserIdFromToken($service, $token)
    {
        $redirectAction = config("services.$service.redirect_action");
        if (!empty($redirectAction)) {
            config()->set("services.$service.redirect", action(config("services.$service.redirect_action")));
        }
        $driver      = \Socialite::driver($service);
        $serviceUser = $driver->userFromToken($token);

        $authUser = $this->getAuthModelId($service, [
            'service'    => $service,
            'service_id' => $serviceUser->getId(),
            'name'       => $serviceUser->getName(),
            'email'      => $serviceUser->getEmail(),
            'avatar'     => $serviceUser->getAvatar(),
        ]);

        return $authUser;
    }
}
