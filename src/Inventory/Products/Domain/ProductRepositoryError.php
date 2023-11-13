<?php

declare(strict_types=1);

namespace Techpump\Inventory\Products\Domain;

use Techpump\Shared\Domain\DomainError;

final class ProductRepositoryError extends DomainError
{
    public function __construct(
        private readonly ?string $errorMessage = null
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_repository_error';
    }

    protected function errorMessage(): string
    {
        return $this->errorMessage ?? 'Product repository error';
    }
}
