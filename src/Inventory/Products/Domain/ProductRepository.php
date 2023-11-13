<?php

declare(strict_types=1);

namespace Techpump\Inventory\Products\Domain;

interface ProductRepository
{
    public function search(string $id): Product;
}
