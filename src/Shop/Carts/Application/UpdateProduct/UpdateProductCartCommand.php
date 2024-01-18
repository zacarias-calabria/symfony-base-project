<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\UpdateProduct;

use App\Shared\Domain\Bus\Command\Command;

final readonly class UpdateProductCartCommand implements Command
{
    public function __construct(
        private string $cartId,
        private string $productId,
        private int $quantity,
    ) {
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
