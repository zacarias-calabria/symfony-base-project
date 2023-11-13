<?php

declare(strict_types=1);

namespace Techpump\Shared\Domain\Utils;

use ReflectionClass;

final class Classes
{

    public static function extractClassName(object $object): string
    {
        return (new ReflectionClass($object))->getShortName();
    }
}
