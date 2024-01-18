<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

interface CartRepository
{
    public function save(Cart $cart): void;

    public function search(CartId $id): Cart;
}
