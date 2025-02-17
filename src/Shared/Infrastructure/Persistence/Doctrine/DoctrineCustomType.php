<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

interface DoctrineCustomType
{
    public static function customTypeName(): string;
}
