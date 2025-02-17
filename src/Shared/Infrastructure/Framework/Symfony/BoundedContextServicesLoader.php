<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony;

use App\Shared\Domain\BoundedContext\Context;
use App\Shared\Domain\BoundedContext\Metadata;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final readonly class BoundedContextServicesLoader
{
    /**
     * @param class-string ...$boundedContexts
     */
    public static function configureContainer(
        ContainerConfigurator $container,
        Context $context,
        string ...$boundedContexts
    ): void {
        $services = $container->services();
        foreach ($boundedContexts as $boundedContext) {
            $metadata = new Metadata(new $boundedContext());
            $namespace = $metadata->getNamespace();
            $dir = $metadata->getRootDir();
            self::wireHttpControllers($services, $namespace, $dir);
        }
    }

    public static function configureRouter(RoutingConfigurator $routes, string ...$boundedContexts): void
    {
        foreach ($boundedContexts as $boundedContext) {
            self::importRoutes(new Metadata(new $boundedContext()), $routes);
        }
    }

    private static function importRoutes(Metadata $metadata, RoutingConfigurator $routes): void
    {
        $rootDir = $metadata->getRootDir();
        $boundedContextName = strtolower(basename($metadata->getRootDir()));
        $controllersDir = sprintf('%s/Ui/Adapter/Http', $rootDir);

        if (is_dir($controllersDir)) {
            $routes->import($controllersDir, 'attribute', false);
        }

        self::importRoutesInDir($routes, $rootDir, $boundedContextName, 'inner');
        self::importRoutesInDir($routes, $rootDir, $boundedContextName, 'outer');
    }

    private static function importRoutesInDir(
        RoutingConfigurator $routes,
        string $rootDir,
        string $boundedContextName,
        string $type
    ): void {
        $prefix = sprintf('/%s/%s', $type, $boundedContextName);
        $controllersDir = sprintf('%s/Ui/Adapter/Http/%s', $rootDir, ucfirst($type));

        if (is_dir($controllersDir)) {
            $routes->import($controllersDir, 'annotation', false)
                ->prefix($prefix);
        }
    }

    private static function wireHttpControllers(ServicesConfigurator $services, string $namespace, string $dir): void
    {
        if (!is_dir(sprintf('%s/Ui/Adapter/Http', $dir))) {
            return;
        }

        $services
            ->load(
                sprintf('%s\\Ui\\Adapter\\Http\\', $namespace),
                sprintf('%s/Ui/Adapter/Http/**/*Controller.php', $dir)
            )
            ->tag('controller.service_arguments')
            ->autowire()
            ->autoconfigure()
            ->public();
    }
}
