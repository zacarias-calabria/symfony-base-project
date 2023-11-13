<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Domain;

use Techpump\Shop\Carts\Domain\CartId;
use Tests\Techpump\Shared\Domain\UuidMother;

final class CartIdMother
{
    public static function create(?string $value = null): CartId
    {
        return new CartId($value ?? UuidMother::create());
    }
}
