<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Domain;

use Techpump\Shared\Domain\ValueObject\IntValueObject;

final class ProductInCartQuantity extends IntValueObject {
    public function __construct(int $value)
    {
        $this->ensureIsAValidQuantity($value);
        parent::__construct($value);
    }

    private function ensureIsAValidQuantity(int $value): void
    {
        if ($value < 1) {
            throw new InsufficientQuantityProductsError();
        }
    }
}
