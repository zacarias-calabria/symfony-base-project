<?php

declare(strict_types=1);

namespace Techpump\Inventory\Products\Infrastructure\Persistence;

use Exception;
use Techpump\Inventory\Products\Domain\Product;
use Techpump\Inventory\Products\Domain\ProductNotFound;
use Techpump\Inventory\Products\Domain\ProductRepository;
use Techpump\Inventory\Products\Domain\ProductRepositoryError;
use Techpump\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final class DoctrineProductRepository extends DoctrineRepository implements ProductRepository
{
    public function search(string $id): Product
    {
        try {
            $product = $this->repository(Product::class)->findOneBy(['id' => $id]);
        } catch (Exception $e) {
            throw new ProductRepositoryError($e->getMessage());
        }
        return $product ?? throw new ProductNotFound();
    }
}
