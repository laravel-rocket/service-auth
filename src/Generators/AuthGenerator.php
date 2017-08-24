<?php
namespace LaravelRocket\ServiceAuthentication\Generators;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use function ICanBoogie\singularize;

class AuthGenerator extends Generator
{
    public function generate($name, $overwrite = false, $baseDirectory = null)
    {
        $this->generateMigration($name);

        $this->generateModel($name);
        $this->generatePresenter($name);
        $this->generateModelUnitTest($name);
        $this->generateModelFactory($name);

        $this->generateService($name);
        $this->generateServiceInterface($name);
        $this->generateServiceUnitTest($name);
        $this->bindServiceInterface($name);

        $this->generateRepository($name);
        $this->generateRepositoryInterface($name);
        $this->generateRepositoryUnitTest($name);
        $this->bindRepositoryInterface($name);
    }

    /// MIGRATIONS

    protected function generateMigration($name)
    {
        $name = $this->getTableName($name);

        if (class_exists($className = $this->getMigrationClassName($name))) {
            throw new InvalidArgumentException("A $className migration already exists.");
        }

        $path         = $this->getMigrationPath($name);
        $stubFilePath = $this->getStubPath('/service-auth/auth/migration.stub');

        return $this->generateFile($className, $path, $stubFilePath, [
            'CLASS' => $className,
            'TABLE' => $name,
        ]);
    }

    protected function getTableName($name)
    {
        return singularize(snake_case($name)).'_service_authentications';
    }

    protected function getMigrationClassName($name)
    {
        return 'Create'.ucfirst(camel_case($name)).'Table';
    }

    protected function getMigrationPath($name)
    {
        $basePath = database_path('migrations');

        return $basePath.'/'.date('Y_m_d_His').'_create_'.$name.'_table.php';
    }

    // MODEL

    protected function generateModel($name)
    {
        $modelName = $this->getModelName($name);
        $className = $this->getModelClass($modelName);
        $classPath = $this->convertClassToPath($className);

        $stubFilePath = $this->getStubPath('/service-auth/auth/model.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath, [
        ]);
    }

    protected function generatePresenter($name)
    {
        $modelName = $this->getModelName($name);

        $className    = '\\App\\Presenters\\'.$modelName.'Presenter';
        $classPath    = $this->convertClassToPath($className);
        $stubFilePath = $this->getStubPath('/model/presenter.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath, [
            'MULTILINGUAL_COLUMNS' => '',
            'IMAGE_COLUMNS'        => '',
        ]);
    }

    protected function generateModelUnitTest($modelName)
    {
        $classPath    = base_path('/tests/Models/'.$modelName.'Test.php');
        $stubFilePath = $this->getStubPath('/model/model_unittest.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function getModelClass($name)
    {
        $modelName = $this->getModelName($name);

        return '\\App\\Models\\'.$modelName;
    }

    protected function generateModelFactory($name)
    {
        $modelName = $this->getModelName($name);
        $className = $this->getModelClass($modelName);

        $factoryPath = base_path('/database/factories/ModelFactory.php');
        $key         = '/* NEW MODEL FACTORY */';

        $data = '$factory->define('.$className.'::class, function (Faker\Generator $faker) {'.PHP_EOL
                .'    return ['.PHP_EOL
                .'        \'user_id\'       => 0,'.PHP_EOL
                .'        \'name\'          => $faker->name,'.PHP_EOL
                .'        \'email\'         => $faker->email,'.PHP_EOL
                .'        \'service\'       => \'\','.PHP_EOL
                .'        \'service_id\'    => \'\','.PHP_EOL
                .'        \'service_token\' => \'\','.PHP_EOL
                .'        \'image_url\'     => $faker->imageUrl(),'.PHP_EOL;
        EOF;
        $data .= '    ];'.PHP_EOL.'});'.PHP_EOL.PHP_EOL;

        $this->replaceFile([
            $key => $data,
        ], $factoryPath);

        return true;
    }

    /// SERVICE

    protected function generateService($name)
    {
        $modelName = $this->getModelName($name);

        $className = $this->getServiceClass($modelName);
        $classPath = $this->convertClassToPath($className);

        $stubFilePath = $this->getStubPath('/service-auth/auth/service.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function generateServiceInterface($name)
    {
        $modelName = $this->getModelName($name);

        $className = $this->getServiceInterfaceClass($modelName);
        $classPath = $this->convertClassToPath($className);

        $stubFilePath = $this->getStubPath('/service-auth/auth/service_interface.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function generateServiceUnitTest($name)
    {
        $modelName    = $this->getModelName($name);
        $classPath    = base_path('/tests/Services/'.$modelName.'ServiceAuthenticationServiceTest.php');
        $stubFilePath = $this->getStubPath('/service/service_unittest.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function bindServiceInterface($name)
    {
        $bindingPath = base_path('/app/Providers/ServiceServiceProvider.php');
        $modelName   = $this->getModelName($name);

        $key  = '/* NEW BINDING */';
        $bind = '$this->app->singleton('.PHP_EOL.'            \\App\\Services\\'.$modelName.'ServiceAuthenticationServiceInterface::class,'.PHP_EOL.'            \\App\\Services\\Production\\'.$name.'ServiceAuthenticationService::class'.PHP_EOL.'        );'.PHP_EOL.PHP_EOL.'        ';
        $this->replaceFile([
            $key => $bind,
        ], $bindingPath);

        return true;
    }

    protected function getModelName($name)
    {
        $className = $this->getClassName(singularize($name));

        return $className;
    }

    protected function getServiceClass($name)
    {
        $modelName = $this->getModelName($name);

        return '\\App\\Services\\Production\\'.$modelName.'ServiceAuthenticationService';
    }

    protected function getServiceInterfaceClass($name)
    {
        $modelName = $this->getModelName($name);

        return '\\App\\Services\\'.$modelName.'ServiceAuthenticationServiceInterface';
    }

    // REPOSITORIES

    protected function generateRepository($modelName)
    {
        $className = $this->getRepositoryClass($modelName);
        $classPath = $this->convertClassToPath($className);

        $stubFilePath = $this->getStubPath('/service-auth/auth/repository.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function getRepositoryClass($name)
    {
        $modelName = $this->getModelName($name);

        return '\\App\\Repositories\\Eloquent\\'.$modelName.'ServiceAuthenticationRepository';
    }

    protected function getRepositoryInterfaceClass($name)
    {
        $modelName = $this->getModelName($name);

        return '\\App\\Repositories\\'.$modelName.'ServiceAuthenticationRepositoryInterface';
    }

    protected function generateRepositoryInterface($name)
    {
        $modelName = $this->getModelName($name);
        $className = $this->getRepositoryInterfaceClass($name);
        $classPath = $this->convertClassToPath($className);

        $stubFilePath = $this->getStubPath('/service-auth/auth/repository_interface.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function generateRepositoryUnitTest($name)
    {
        $modelName = $this->getModelName($name);

        $classPath    = base_path('/tests/Repositories/'.$modelName.'ServiceAuthenticationRepositoryTest.php');
        $stubFilePath = $this->getStubPath('/repository/repository_unittest.stub');

        return $this->generateFile($modelName, $classPath, $stubFilePath);
    }

    protected function bindRepositoryInterface($name)
    {
        $modelName   = $this->getModelName($name);
        $bindingPath = base_path('/app/Providers/RepositoryServiceProvider.php');

        $key  = '/* NEW BINDING */';
        $bind = '$this->app->singleton('.PHP_EOL.'            \\App\\Repositories\\'.$modelName.'ServiceAuthenticationRepositoryInterface::class,'.PHP_EOL.'            \\App\\Repositories\\Eloquent\\'.$modelName.'ServiceAuthenticationRepository::class'.PHP_EOL.'        );'.PHP_EOL.PHP_EOL.'        ';
        $this->replaceFile([
            $key => $bind,
        ], $bindingPath);

        return true;
    }
}
