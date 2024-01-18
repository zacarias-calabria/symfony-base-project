<?php

declare(strict_types=1);

namespace Tests\App\Inventory\Products\Domain;

use App\Inventory\Products\Domain\Product;
use Tests\App\Shared\Domain\MotherCreator;
use Tests\App\Shared\Domain\UuidMother;

final class ProductMother
{
    public static function create(
        ?string $id = null,
        ?float $price = null,
        ?float $taxRate = null
    ): Product {
        return new Product(
            id: $id ?? UuidMother::create(),
            name: MotherCreator::random()->sentence(nbWords: 3),
            price: $price ?? MotherCreator::random()->randomFloat(nbMaxDecimals: 2, min: 10, max: 100),
            sku: MotherCreator::random()->unique()->ean13(),
            taxRate: $taxRate ?? MotherCreator::random()->randomFloat(nbMaxDecimals: 0, min: 4, max: 21),
        );
    }
}
