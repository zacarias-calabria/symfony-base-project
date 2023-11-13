<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Domain;

use Techpump\Shared\Domain\DomainError;

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
