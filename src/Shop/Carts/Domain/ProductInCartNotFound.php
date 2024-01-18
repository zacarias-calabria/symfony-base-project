<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

use App\Shared\Domain\DomainError;

final class ProductInCartNotFound extends DomainError
{
    public function errorCode(): string
    {
        return 'product_in_cart_not_found';
    }

    protected function errorMessage(): string
    {
        return 'Product in cart not found';
    }
}
