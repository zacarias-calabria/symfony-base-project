<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

use App\Shared\Domain\DomainError;

final class InsufficientQuantityProductsError extends DomainError
{
    public function errorCode(): string
    {
        return 'insufficient_products_quantity';
    }

    protected function errorMessage(): string
    {
        return 'The quantity of products to be added must be equal to or greater than 1';
    }
}
