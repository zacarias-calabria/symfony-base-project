<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Domain;

use Techpump\Shop\Carts\Domain\ProductId;
use Tests\Techpump\Shared\Domain\UuidMother;

final class ProductIdMother
{
    public static function create(?string $value = null): ProductId
    {
        return new ProductId($value ?? UuidMother::create());
    }
}
