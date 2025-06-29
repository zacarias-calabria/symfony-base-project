<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony\CheckMessageConventions;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Event\Event;
use App\Shared\Domain\Bus\Query\Query;
use LogicException;
use ReflectionNamedType;
use ReflectionParameter;

final class CheckStrategyFactory
{
    public static function build(ReflectionParameter $message): EventCheckerStrategy|QueryCheckerStrategy|CommandCheckerStrategy
    {
        $messageType = $message->getType();

        if (!$messageType instanceof ReflectionNamedType) {
            throw new LogicException('Cannot determine message type using reflection.');
        }

        $class = $messageType->getName();

        if (is_subclass_of($class, Command::class, true)) {
            return new CommandCheckerStrategy($class);
        }

        if (is_subclass_of($class, Query::class, true)) {
            return new QueryCheckerStrategy($class);
        }

        if (is_subclass_of($class, Event::class, true)) {
            return new EventCheckerStrategy($class);
        }

        $msg = sprintf(
            'Invalid parameter type in handler %s. Accepted types are %s, %s or %s',
            $message->getDeclaringClass()?->getName(),
            new CommandCheckerStrategy('')->getClassName(),
            new QueryCheckerStrategy('')->getClassName(),
            new EventCheckerStrategy('')->getClassName(),
        );

        throw new LogicException($msg);
    }
}
