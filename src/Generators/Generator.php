<?php
namespace LaravelRocket\ServiceAuthentication\Generators;

use LaravelRocket\Generator\Generators\Generator as BaseGenerator;

abstract class Generator extends BaseGenerator
{
    protected function getStubPath($path)
    {
        $stubFilePath = resource_path('stubs'.$path);

        if ($this->files->exists($stubFilePath)) {
            return $stubFilePath;
        }

        $stubFilePath = __DIR__.'/../../stubs'.$path;

        if ($this->files->exists($stubFilePath)) {
            return $stubFilePath;
        }

        $stubFilePath = __DIR__.'/../../../generator/stubs'.$path;

        return $stubFilePath;
    }
}
