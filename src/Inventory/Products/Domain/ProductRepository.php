<?php

declare(strict_types=1);

namespace App\Inventory\Products\Domain;

interface ProductRepository
{
    public function search(string $id): Product;
}
