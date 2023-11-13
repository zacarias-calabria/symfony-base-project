<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Infrastructure\Persistence;

use Exception;
use Techpump\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use Techpump\Shop\Carts\Domain\Cart;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\CartRepository;
use Techpump\Shop\Carts\Domain\CartRepositoryError;
use Throwable;

final class DoctrineActiveCartRepository extends DoctrineRepository implements CartRepository
{
    public function save(Cart $cart): void
    {
        try {
            $this->persist($cart);
        } catch (Throwable $e) {
            throw new CartRepositoryError(
                errorMessage: $e->getMessage()
            );
        }
    }

    public function search(CartId $id): Cart
    {
        try {
            $cart = $this->repository(Cart::class)
                         ->findOneBy(
                             criteria:[
                                 'id' => $id,
                                 'status' => Cart::STATUS_ACTIVE
                             ]
                         );
        } catch (Exception $e) {
            throw new CartRepositoryError($e->getMessage());
        }
        return $cart ?? throw new CartNotFound();
    }
}
