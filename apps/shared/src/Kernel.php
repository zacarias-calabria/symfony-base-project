<?php

declare(strict_types=1);

namespace App\Apps\Shared;

use App\Shared\Domain\BoundedContext\Context;
use App\Shared\Infrastructure\Framework\Symfony\BoundedContextServicesLoader;
use App\Shared\Infrastructure\Framework\Symfony\RegisterHandlersInBusesPass;
use Override;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

abstract class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private Context $context;
    /** @var array<class-string> */
    private array $boundedContexts;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->context = new Context($environment, $debug);
        $this->registerBoundedContexts();
    }

    abstract public function getRootPath(): string;

    abstract public function getAppPath(): string;

    #[Override]
    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterHandlersInBusesPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
    }


    #[\Override]
    public function getCacheDir(): string
    {
        return sprintf(
            '%1$s%2$s/var/cache/%3$s/%4$s',
            $this->getProjectDir(),
            $this->getRootPath(),
            $this->getAppPath(),
            $this->environment,
        );
    }

    #[\Override]
    public function getLogDir(): string
    {
        return sprintf(
            '%1$s%2$s/var/log/%3$s/%4$s',
            $this->getProjectDir(),
            $this->getRootPath(),
            $this->getAppPath(),
            $this->environment,
        );
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');
        $container->import('../config/{services}.yaml');
        $container->import('../config/{services}_' . $this->environment . '.yaml');
        BoundedContextServicesLoader::configureContainer($container, $this->context, ...$this->boundedContexts);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/{routes}.yaml');
        BoundedContextServicesLoader::configureRouter($routes, ...$this->boundedContexts);
    }

    private function registerBoundedContexts(): void
    {
        $boundedContexts = require $this->getProjectDir() . $this->getRootPath(
        ) . '/apps/shared/config/boundedContexts.php';
        foreach ($boundedContexts as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                $this->boundedContexts[] = $class;
            }
        }
    }
}
