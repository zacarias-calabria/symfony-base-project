<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class StringValueObject implements \Stringable
{
    public function __construct(
        protected string $value,
    ) {
        $this->ensureIsAValidString($value);
    }

    public function equals(StringValueObject $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->value();
    }

    private function ensureIsAValidString(string $string): void
    {
        if (true === empty($string)) {
            throw new \InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $string));
        }
    }
}
