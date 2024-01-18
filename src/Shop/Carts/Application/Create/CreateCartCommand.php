<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\Create;

use App\Shared\Domain\Bus\Command\Command;

final class CreateCartCommand implements Command
{
    public function __construct(
        private readonly string $id
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }
}
