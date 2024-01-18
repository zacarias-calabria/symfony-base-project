<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Domain;

use App\Shop\Carts\Domain\Cart;
use App\Shop\Carts\Domain\CartId;
use DateTimeImmutable;

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
