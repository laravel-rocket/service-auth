<?php
namespace LaravelRocket\ServiceAuthentication\Console\Commands;

use LaravelRocket\Generator\Console\Commands\GeneratorCommand;
use LaravelRocket\ServiceAuthentication\Generators\ServiceGenerator;
use Symfony\Component\Console\Input\InputArgument;

class ServiceGeneratorCommand extends GeneratorCommand
{
    protected $name        = 'rocket:make:service-auth-service';

    protected $description = 'Generate Service Authentication Service';

    protected $generator   = ServiceGenerator::class;

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $authName    = $this->getTargetName();
        $serviceName = $this->getServiceName();

        return $this->generate($authName.':'.$serviceName);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getServiceName()
    {
        return $this->argument('service');
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the auth'],
            ['service', InputArgument::REQUIRED, 'The name of the service'],
        ];
    }
}
