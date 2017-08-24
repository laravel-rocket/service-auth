<?php
namespace LaravelRocket\ServiceAuthentication\Generators;

class ServiceGenerator extends Generator
{
    public function generate($name, $overwrite = false, $baseDirectory = null)
    {
        $names       = explode(':', $name);
        $authName    = $names[0];
        $serviceName = $names[1];

        $this->generateController($authName, $serviceName);
        $this->updateConfig($authName, $serviceName);
    }

    protected function generateController($authName, $serviceName)
    {
        $modelName = $this->getModelName($authName);
        $service   = $this->getServiceName($serviceName);
        $className = $this->getControllerClass($authName, $serviceName);
        $classPath = $this->convertClassToPath($className);

        $stubFilePath = $this->getStubPath('/service-auth/service/controller.stub');

        return $this->generateFile($className, $classPath, $stubFilePath, [
            'MODEL'   => $modelName,
            'SERVICE' => $service,
        ]);
    }

    protected function getServiceName($name)
    {
        return title_case($name);
    }

    protected function getModelName($name)
    {
        return title_case($name);
    }

    protected function getControllerClass($authName, $serviceName)
    {
        $service = $this->getServiceName($serviceName);
        $model   = $this->getModelName($authName);

        return '\\App\\Http\\Controllers\\'.$model.'\\'.$service.'ServiceAuthController';
    }

    protected function addRoute($authName, $serviceName)
    {
        $model   = $this->getModelName($authName);
        $service = $this->getServiceName($serviceName);

        $path = strtolower($authName) == 'user' ? 'web' : strtolower($authName);

        $routePath = base_path('/routes/'.$path.'.php');

        $key    = '/* NEW SERVICE AUTH ROOT */';
        $routes = '\Route::get(\'signin/'.$serviceName.'\', \''.$model.'\\'.$service.'ServiceAuthController@redirect\');'.PHP_EOL.
                  '        \Route::get(\'signin/'.$serviceName.'/callback\', \''.$model.'\\'.$service.'ServiceAuthController@callback\');';
        $this->replaceFile([
            $key => $routes,
        ], $routePath);

        return true;
    }

    protected function updateConfig($authName, $serviceName)
    {
        $model   = $this->getModelName($authName);
        $service = $this->getServiceName($serviceName);

        $configPath = base_path('/config/services.php');

        $key    = '/* NEW SERVICE INFO */';
        $config = '\'' . $serviceName . '\' => [' . PHP_EOL
        . '        \'client_id\'       => \'\',' . PHP_EOL
        . '        \'client_secret\'   => \'\',' . PHP_EOL
        . '        \'redirect_action\' => \''.$model.'\\'.$service.'ServiceAuthController@callback\',' . PHP_EOL
        . '    ],' . PHP_EOL . PHP_EOL.  '    ';

        $this->replaceFile([
            $key => $config,
        ], $configPath);

    }
}
