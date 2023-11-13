<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\Create;

use Techpump\Shared\Domain\Bus\Command\Command;

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
