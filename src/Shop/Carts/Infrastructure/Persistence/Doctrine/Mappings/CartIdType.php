<?php

declare(strict_types=1);

namespace App\Shop\Carts\Infrastructure\Persistence\Doctrine\Mappings;

use App\Shared\Infrastructure\Persistence\Doctrine\UuidType;
use App\Shop\Carts\Domain\CartId;

final class CartIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return CartId::class;
    }
}
