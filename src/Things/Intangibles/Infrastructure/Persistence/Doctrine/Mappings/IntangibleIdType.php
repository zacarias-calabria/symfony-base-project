<?php

declare(strict_types=1);

namespace App\Things\Intangibles\Infrastructure\Persistence\Doctrine\Mappings;

use App\Shared\Infrastructure\Persistence\Doctrine\UuidType;
use App\Things\Intangibles\Domain\IntangibleId;

final class IntangibleIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return IntangibleId::class;
    }
}
