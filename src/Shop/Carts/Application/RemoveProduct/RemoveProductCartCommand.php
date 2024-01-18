<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\RemoveProduct;

use App\Shared\Domain\Bus\Command\Command;

final readonly class RemoveProductCartCommand implements Command
{
    public function __construct(
        private string $cartId,
        private string $productId,
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
}
