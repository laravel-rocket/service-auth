<?php
namespace LaravelRocket\Foundation\Tests\Services;

use LaravelRocket\ServiceAuthentication\Services\ServiceAuthenticationServiceInterface;
use LaravelRocket\ServiceAuthentication\Tests\TestCase;

class ServiceAuthenticationServiceTest extends TestCase
{
    public function testGetInstance()
    {
        /** @var ServiceAuthenticationServiceTest $service */
        $service = app()->make(ServiceAuthenticationServiceInterface::class);
        $this->assertNotNull($service);
    }
}
