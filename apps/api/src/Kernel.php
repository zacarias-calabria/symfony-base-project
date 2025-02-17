<?php

declare(strict_types=1);

namespace App\Apps\API;

use App\Shared\Domain\BoundedContext\Context;
use App\Shared\Infrastructure\Framework\Symfony\BoundedContextServicesLoader;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const string ROOT_PATH = '/../..';
    private const string APP_PATH = '/api';

    private Context $context;
    /** @var array<class-string> */
    private array $boundedContexts;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->context = new Context($environment, $debug);
        $this->registerBoundedContexts();
    }

    #[\Override]
    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    #[\Override]
    public function getCacheDir(): string
    {
        return $this->getProjectDir().self::ROOT_PATH.'/var/cache'.self::APP_PATH.'/'.$this->environment;
    }

    #[\Override]
    public function getLogDir(): string
    {
        return $this->getProjectDir().self::ROOT_PATH.'/var/log'.self::APP_PATH;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');
        $container->import('../config/{services}.yaml');
        $container->import('../config/{services}_'.$this->environment.'.yaml');
        BoundedContextServicesLoader::configureContainer($container, $this->context, ...$this->boundedContexts);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/{routes}.yaml');
        BoundedContextServicesLoader::configureRouter($routes, ...$this->boundedContexts);
    }

    private function registerBoundedContexts(): void
    {
        $boundedContexts = require $this->getProjectDir().self::ROOT_PATH.'/apps/shared/config/boundedContexts.php';
        foreach ($boundedContexts as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                $this->boundedContexts[] = $class;
            }
        }
    }
}
