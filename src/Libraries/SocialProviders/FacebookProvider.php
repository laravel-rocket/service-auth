<?php
namespace LaravelRocket\ServiceAuthentication\Libraries\SocialProviders;

use Socialite;

class FacebookProvider extends AbstractProvider
{
    public function getUserByToken($token)
    {
        $driver      = Socialite::driver('facebook');
        $accessToken = $driver->getAccessToken($token);
        $user        = $driver->userFromToken($accessToken);

        return $user;
    }
}
