<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Infrastructure\Persistence;

use Exception;
use Techpump\Shop\Carts\Domain\Cart;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\CartRepository;
use Techpump\Shop\Carts\Domain\CartRepositoryError;
use Tests\Techpump\Shared\Domain\TraitInMemoryRepository;

final class InMemoryActiveCartRepository implements CartRepository
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
                fn(Cart $cart): bool => $cart->id()->value() === $id->value() && $cart->status() === Cart::STATUS_ACTIVE
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
