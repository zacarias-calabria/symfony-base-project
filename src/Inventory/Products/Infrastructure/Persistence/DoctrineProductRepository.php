<?php

declare(strict_types=1);

namespace App\Inventory\Products\Infrastructure\Persistence;

use App\Inventory\Products\Domain\Product;
use App\Inventory\Products\Domain\ProductNotFound;
use App\Inventory\Products\Domain\ProductRepository;
use App\Inventory\Products\Domain\ProductRepositoryError;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use Exception;

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
