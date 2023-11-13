<?php

declare(strict_types=1);

namespace Techpump\Inventory\Products\Domain;

use Techpump\Shared\Domain\AggregateRoot\AggregateRoot;

class Product extends AggregateRoot
{
    public function __construct(
        private readonly string $id,
        private readonly string $sku,
        private readonly string $name,
        private readonly float $price,
        private readonly float $taxRate,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function taxRate(): float
    {
        return $this->taxRate;
    }
}
