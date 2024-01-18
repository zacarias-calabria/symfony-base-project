<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\Create;

use App\Shop\Carts\Domain\Cart;
use App\Shop\Carts\Domain\CartId;
use DateTimeImmutable;

final readonly class CartCreator
{
    public function __invoke(
        CartId $id
    ): Cart {
        return Cart::create(
            id: $id,
            status: Cart::STATUS_ACTIVE,
            createdAt: new DateTimeImmutable()
        );
    }
}
