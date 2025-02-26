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
            self::wireServices($context, $metadata, $container);
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
        self::importBoundedContextRoutes(metadata: $metadata, routes: $routes);
        self::importModulesRoutes(metadata: $metadata, routes: $routes);
    }

    private static function importBoundedContextRoutes(Metadata $metadata, RoutingConfigurator $routes): void
    {
        $rootDir = $metadata->getRootDir();
        $httpAdapterPath = '%s/Ui/Adapter/Http';
        $baseControllersDir = sprintf($httpAdapterPath, $rootDir);
        if (is_dir($baseControllersDir)) {
            $routes->import($baseControllersDir, 'attribute', false);
        }
        $rootName = strtolower(basename($rootDir));
        self::importRoutesInDir(routes: $routes, baseControllersDir: $baseControllersDir, rootName: $rootName, type: 'Inner');
        self::importRoutesInDir(routes: $routes, baseControllersDir: $baseControllersDir, rootName: $rootName, type: 'Outer');
    }

    private static function importModulesRoutes(Metadata $metadata, RoutingConfigurator $routes): void
    {
        $rootDir = $metadata->getRootDir();
        $httpAdapterPath = '%s/*/Ui/Adapter/Http';
        $baseControllersDir = sprintf($httpAdapterPath, $rootDir);
        foreach (glob($baseControllersDir) as $baseModuleControllersDir) {
            if (is_dir($baseModuleControllersDir)) {
                $routes->import($baseModuleControllersDir, 'attribute', false);
            }
            $rootName = strtolower(basename($rootDir));
            self::importRoutesInDir(routes: $routes, baseControllersDir: $baseModuleControllersDir, rootName: $rootName, type: 'Inner');
            self::importRoutesInDir(routes: $routes, baseControllersDir: $baseModuleControllersDir, rootName: $rootName, type: 'Outer');
        }
    }

    private static function importRoutesInDir(
        RoutingConfigurator $routes,
        string $baseControllersDir,
        string $rootName,
        string $type,
    ): void {
        $controllersDir = sprintf("{$baseControllersDir}/%s", ucfirst($type));

        if (is_dir($controllersDir)) {
            $routes->import($controllersDir, 'attribute', false)
                ->prefix(sprintf('/%s/%s', strtolower($type), strtolower($rootName)));
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
                sprintf('%s/Ui/Adapter/Http/**/*Controller.php', $dir),
            )
            ->tag('controller.service_arguments')
            ->autowire()
            ->autoconfigure()
            ->public();
    }

    private static function wireServices(Context $context, Metadata $meta, ContainerConfigurator $configurator): void
    {
        $path = $meta->getRelativePath('Infrastructure/DependencyInjection/Symfony');

        $configurator->import(sprintf('%s/services.yaml', $path), 'yaml', 'not_found');
        $configurator->import(sprintf('%s/services_%s.yaml', $path, $context->env), 'yaml', 'not_found');
    }
}
