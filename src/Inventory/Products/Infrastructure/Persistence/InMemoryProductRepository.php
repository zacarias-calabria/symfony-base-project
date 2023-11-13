<?php

declare(strict_types=1);

namespace Techpump\Inventory\Products\Infrastructure\Persistence;

use Exception;
use Techpump\Inventory\Products\Domain\Product;
use Techpump\Inventory\Products\Domain\ProductNotFound;
use Techpump\Inventory\Products\Domain\ProductRepository;
use Techpump\Inventory\Products\Domain\ProductRepositoryError;
use Tests\Techpump\Shared\Domain\TraitInMemoryRepository;

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
