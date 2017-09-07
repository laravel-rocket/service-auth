<?php
namespace LaravelRocket\ServiceAuthentication\Libraries\SocialProviders;

abstract class AbstractProvider
{
    /**
     * @param string $token
     *
     * @return mixed
     */
    abstract public function getUserByToken($token);
}
