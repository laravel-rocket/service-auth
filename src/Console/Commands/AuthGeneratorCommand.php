<?php
namespace LaravelRocket\ServiceAuthentication\Console\Commands;

use LaravelRocket\Generator\Console\Commands\GeneratorCommand;
use LaravelRocket\ServiceAuthentication\Generators\AuthGenerator;

class AuthGeneratorCommand extends GeneratorCommand
{
    protected $name        = 'rocket:make:service-auth-base';

    protected $description = 'Generate Service Authentication Service';

    protected $generator   = AuthGenerator::class;

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $targetName = $this->getTargetName();

        return $this->generate($targetName);
    }
}
