<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony\CheckMessageConventions;

use App\Shared\Domain\Bus\Event\Event;
use LogicException;
use ReflectionClass;
use ReflectionException;

final class EventCheckerStrategy extends AbstractCheckerStrategy
{
    public function getClassName(): string
    {
        return Event::class;
    }

    public function checkSuffix(): void
    {
        if (!str_ends_with($this->messageClass, $this->getSuffix())) {
            throw new LogicException(sprintf('Suffix `Event` is mandatory in %s', $this->messageClass));
        }
    }

    /**
     * @throws ReflectionException
     */
    public function checkHandlerName(string $handler): void
    {
        $reflectionMessageClass = new ReflectionClass($this->messageClass);

        $partOfNameRegex = sprintf('/(([a-zA-Z]{1,3})On%sHandler)$/', $reflectionMessageClass->getShortName());

        if (!preg_match($partOfNameRegex, $handler)) {
            throw new LogicException(sprintf('Handler must be named from the message name, expected %s but handler name is %s.', $this->messageClass.'Handler', $handler));
        }
    }

    protected function getSuffix(): string
    {
        return 'Event';
    }
}
