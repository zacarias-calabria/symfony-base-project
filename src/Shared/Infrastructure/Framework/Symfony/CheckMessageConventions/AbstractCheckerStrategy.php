<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony\CheckMessageConventions;

abstract class AbstractCheckerStrategy
{
    protected string $messageClass;

    public function __construct(string $messageClass)
    {
        $this->messageClass = $messageClass;
    }

    public function checkSuffix(): void
    {
        if (is_subclass_of($this->messageClass, $this->getClassName(), true) && str_ends_with($this->messageClass, $this->getSuffix())) {
            throw new \LogicException(sprintf('Suffix `%s` is not necessary in %s', $this->getSuffix(), $this->messageClass));
        }
    }

    public function checkHandlerName(string $handler): void
    {
        if ($this->messageClass.'Handler' !== $handler) {
            throw new \LogicException(sprintf('Handler must be named from the message name, expected %s but handler name is %s.', $this->messageClass.'Handler', $handler));
        }
    }

    abstract public function getClassName(): string;

    abstract protected function getSuffix(): string;
}
