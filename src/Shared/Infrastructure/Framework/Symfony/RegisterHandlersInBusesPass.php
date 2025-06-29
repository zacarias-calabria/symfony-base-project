<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Event\Event;
use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Infrastructure\Framework\Symfony\CheckMessageConventions\CheckStrategyFactory;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterHandlersInBusesPass implements CompilerPassInterface
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id) {
            $class = $id->getClass();

            if (!empty($class) && preg_match('/.*Application.*Handler/', $class) && class_exists($class)) {
                $handlerClass = new ReflectionClass($class);
                $messageClass = self::checkHandlerConventions($handlerClass);
                $container->getDefinition($class)->addTag('messenger.message_handler', self::getOptions($handlerClass, $messageClass));
            }
        }
    }

    /**
     * @template T of object
     *
     * @param ReflectionClass<T> $handler
     *
     * @return ReflectionClass<object>
     *
     * @throws ReflectionException
     */
    private static function checkHandlerConventions(ReflectionClass $handler): ReflectionClass
    {
        try {
            $method = $handler->getMethod('__invoke');
        } catch (ReflectionException $exception) {
            throw new LogicException(sprintf('Handler %s does not have an __invoke method', $handler->getName()));
        }

        if (!str_ends_with($handler->getName(), 'Handler')) {
            throw new LogicException(sprintf('Handler %s name does not ends with `Handler`', $handler->getName()));
        }

        return self::checkMethodConventions($handler, $method);
    }

    /**
     * @template T of object
     *
     * @param ReflectionClass<T> $handler
     *
     * @return ReflectionClass<object>
     *
     * @throws ReflectionException
     */
    private static function checkMethodConventions(ReflectionClass $handler, ReflectionMethod $method): ReflectionClass
    {
        if (($numParams = count($method->getParameters())) !== 1) {
            throw new LogicException(sprintf('Handlers __invoke must have only one parameter, but %s has %d parameters.', $handler->getName(), $numParams));
        }

        return self::checkMessageConventions($handler, $method->getParameters()[0]);
    }

    /**
     * @template T of object
     *
     * @param ReflectionClass<T> $handler
     *
     * @return ReflectionClass<object>
     * @throws ReflectionException
     */
    private static function checkMessageConventions(ReflectionClass $handler, ReflectionParameter $messageParameter): ReflectionClass
    {
        $strategy = CheckStrategyFactory::build($messageParameter);

        $strategy->checkSuffix();

        $messageType = $messageParameter->getType();

        if (!$messageType instanceof ReflectionNamedType || !class_exists($messageType->getName())) {
            throw new LogicException('Cannot determine message type using reflection.');
        }

        $messageClass = new ReflectionClass($messageType->getName());

        $strategy->checkHandlerName($handler->getName());

        return $messageClass;
    }


    /**
     * @template T of object
     * @template K of object
     *
     * @param ReflectionClass<T> $handlerClass
     * @param ReflectionClass<K> $messageClass
     *
     * @return array<string, mixed>
     */
    private static function getOptions(ReflectionClass $handlerClass, ReflectionClass $messageClass): array
    {
        $options = [
            'bus'     => self::getBus($messageClass),
            'handles' => $messageClass->getName(),
        ];

        if (($transport = self::getTransport($handlerClass, $messageClass)) !== null) {
            $options += ['from_transport' => $transport];
        }

        return $options;
    }


    /**
     * @template T of object
     *
     * @param ReflectionClass<T> $messageClass
     */
    private static function getBus(ReflectionClass $messageClass): string
    {
        if ($messageClass->isSubclassOf(Command::class)) {
            return 'command.bus';
        }

        if ($messageClass->isSubclassOf(Query::class)) {
            return 'query.bus';
        }

        if ($messageClass->isSubclassOf(Event::class)) {
            return 'event.bus';
        }

        throw new InvalidArgumentException(sprintf('Unknown type %s', $messageClass->getName()));
    }

    private static function getTransport(): null
    {
        return null;
    }
}
