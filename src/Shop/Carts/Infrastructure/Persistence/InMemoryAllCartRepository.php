<?php

declare(strict_types=1);

namespace App\Shop\Carts\Infrastructure\Persistence;

use App\Shop\Carts\Domain\Cart;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\CartRepositoryError;
use Exception;
use Tests\App\Shared\Domain\TraitInMemoryRepository;

final class InMemoryAllCartRepository implements CartRepository
{

    use TraitInMemoryRepository;

    public function save(Cart $cart): void
    {
        try {
            $this->persistInMemoryObject($cart);
        } catch (Exception $e) {
            throw new CartRepositoryError($e->getMessage());
        }
    }

    public function search(CartId $id): Cart
    {
        try {
            $cart = $this->findObject(
                fn(Cart $cart): bool => $cart->id()->value() === $id->value()
            );
        } catch (Exception $e) {
            throw new CartRepositoryError($e->getMessage());
        }

        return $cart ?? throw new CartNotFound();
    }

    protected function getObjectId(object $object): mixed
    {
        return $object->id()->value();
    }
}
