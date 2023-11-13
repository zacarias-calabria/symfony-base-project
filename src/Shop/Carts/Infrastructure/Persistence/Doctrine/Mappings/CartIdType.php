<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Infrastructure\Persistence\Doctrine\Mappings;

use Techpump\Shared\Infrastructure\Persistence\Doctrine\UuidType;
use Techpump\Shop\Carts\Domain\CartId;

final class CartIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return CartId::class;
    }
}
