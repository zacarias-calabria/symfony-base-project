<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\Pay;

use App\Shared\Domain\Bus\Command\Command;

final class PayCartCommand implements Command
{
    public function __construct(
        private readonly string $cartId
    ) {
    }

    public function cartId(): string
    {
        return $this->cartId;
    }
}
