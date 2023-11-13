<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Domain;

use DateTimeImmutable;
use Techpump\Shop\Carts\Domain\Cart;
use Techpump\Shop\Carts\Domain\CartId;

final class CartMother
{
    public static function create(
        ?CartId $id = null,
    ): Cart {
        return Cart::create(
            id: $id ?? CartIdMother::create(),
            status: Cart::STATUS_ACTIVE,
            createdAt: new DateTimeImmutable()
        );
    }
}
