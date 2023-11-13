<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Domain;

use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\ProductInCart;
use Tests\Techpump\Inventory\Products\Domain\ProductMother;
use Tests\Techpump\Shared\Domain\MotherCreator;

final class ProductInCartMother
{
    public static function create(
        ?CartId $cartId = null,
        ?string $productId = null,
        ?int $quantity = 1
    ): ProductInCart {
        return ProductInCart::create(
            cart: CartMother::create($cartId),
            product: ProductMother::create($productId),
            unitPrice: MotherCreator::random()->randomFloat(nbMaxDecimals: 2, min: 10, max: 100),
            taxRate: MotherCreator::random()->randomFloat(nbMaxDecimals: 0, min: 4, max: 21),
            quantity: $quantity
        );
    }
}
