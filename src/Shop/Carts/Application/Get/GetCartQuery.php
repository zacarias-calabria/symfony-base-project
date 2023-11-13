<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\Get;

use Techpump\Shared\Domain\Bus\Query\Query;

final class GetCartQuery implements Query
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
