<?php

declare(strict_types=1);

namespace App\Inventory\Products\Domain;

use App\Shared\Domain\DomainError;

final class ProductNotFound extends DomainError
{

    public function errorCode(): string
    {
        return 'product_not_found';
    }

    protected function errorMessage(): string
    {
        return 'Product not found';
    }
}
