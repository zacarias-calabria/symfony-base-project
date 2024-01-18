<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

use App\Shared\Domain\DomainError;

final class CartRepositoryError extends DomainError
{
    public function __construct(
        private readonly ?string $errorMessage = null
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'cart_repository_error';
    }

    protected function errorMessage(): string
    {
        return $this->errorMessage ?? 'Cart repository error';
    }
}
