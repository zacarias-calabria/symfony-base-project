<?php

declare(strict_types=1);

namespace App\Shop\Carts\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Shop\Carts\Domain\Cart;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\CartRepositoryError;
use Exception;
use Throwable;

final class DoctrineAllCartRepository extends DoctrineRepository implements CartRepository
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
                    criteria: [
                        'id' => $id,
                    ]
                );
        } catch (Exception $e) {
            throw new CartRepositoryError($e->getMessage());
        }
        return $cart ?? throw new CartNotFound();
    }
}
