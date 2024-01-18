<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Domain;

use App\Shop\Carts\Domain\ProductId;
use Tests\App\Shared\Domain\UuidMother;

final class ProductIdMother
{
    public static function create(?string $value = null): ProductId
    {
        return new ProductId($value ?? UuidMother::create());
    }
}
