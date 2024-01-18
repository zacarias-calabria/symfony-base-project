<?php

declare(strict_types=1);

namespace App\Inventory\Products\Infrastructure\Persistence;

use App\Inventory\Products\Domain\Product;
use App\Inventory\Products\Domain\ProductNotFound;
use App\Inventory\Products\Domain\ProductRepository;
use App\Inventory\Products\Domain\ProductRepositoryError;
use Exception;
use Tests\App\Shared\Domain\TraitInMemoryRepository;

final class InMemoryProductRepository implements ProductRepository
{

    use TraitInMemoryRepository;

    public function search(string $id): Product
    {
        try {
            $product = $this->findObject(
                fn(Product $product): bool => $product->id() === $id
            );
        } catch (Exception $e) {
            throw new ProductRepositoryError($e->getMessage());
        }

        if (!is_null($product)) {
            return $product;
        }

        throw new ProductNotFound();
    }

    protected function getObjectId(object $object): mixed
    {
        return $object->id();
    }
}
