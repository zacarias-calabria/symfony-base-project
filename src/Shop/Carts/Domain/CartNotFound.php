<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

use App\Shared\Domain\DomainError;

final class CartNotFound extends DomainError
{

    public function errorCode(): string
    {
        return 'cart_not_found';
    }

    protected function errorMessage(): string
    {
        return 'Cart not found';
    }
}
