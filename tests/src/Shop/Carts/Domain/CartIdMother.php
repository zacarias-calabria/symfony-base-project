<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Domain;

use App\Shop\Carts\Domain\CartId;
use Tests\App\Shared\Domain\UuidMother;

final class CartIdMother
{
    public static function create(?string $value = null): CartId
    {
        return new CartId($value ?? UuidMother::create());
    }
}
