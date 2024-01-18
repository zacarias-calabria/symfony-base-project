<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

use App\Shared\Domain\DomainError;

final class CartAlreadyExistsError extends DomainError
{
    public function errorCode(): string
    {
        return 'cart_already_exists';
    }

    protected function errorMessage(): string
    {
        return 'Cart already exists';
    }
}
