<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\Create;

use DateTimeImmutable;
use Techpump\Shop\Carts\Domain\Cart;
use Techpump\Shop\Carts\Domain\CartId;

final readonly class CartCreator
{
    public function __invoke(
        CartId $id
    ): Cart
    {
        return Cart::create(
            id: $id,
            status: Cart::STATUS_ACTIVE,
            createdAt: new DateTimeImmutable()
        );
    }
}
