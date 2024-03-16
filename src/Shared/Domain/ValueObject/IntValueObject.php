<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class IntValueObject
{
    public function __construct(protected int $value) {}

    final public function value(): int
    {
        return $this->value;
    }

    final public function isLessThan(self $other): bool
    {
        return $this->value() < $other->value();
    }
}
