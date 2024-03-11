<?php

declare(strict_types=1);

namespace App\Shared\Domain;


final class Assert
{
    public static function instanceOf(string $class, mixed $item): void
    {
        if (!$item instanceof $class) {
            throw new \InvalidArgumentException(
                sprintf('The object <%s> is not an instance of <%s>', $class, $item::class)
            );
        }
    }
}
