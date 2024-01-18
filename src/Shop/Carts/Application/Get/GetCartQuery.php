<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\Get;

use App\Shared\Domain\Bus\Query\Query;

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
